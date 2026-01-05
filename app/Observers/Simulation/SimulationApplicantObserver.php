<?php

namespace App\Observers\Simulation;

use App\Models\Payment;
use App\Models\PaymentPortfolio;
use App\Models\Simulation\SimulationApplicant;
use App\Models\Simulation\SimulationProcess;

class SimulationApplicantObserver
{
    /**
     * Handle the SimulationApplicant "created" event.
     */
    public function created(SimulationApplicant $simulationApplicant): void
    {
        // Crear el proceso de simulacro
        SimulationProcess::create([
            'simulation_applicant_id' => $simulationApplicant->id,
            // Aquí definimos la hora de creación manual con UTC-5
            'pre_registration_at' => now('America/Lima'),
            'payment_at' => null,
            'data_confirmation_at' => null,
            'registration_at' => null,
        ]);

        // Crear el registro de pago pendiente
        $this->createPaymentRecord($simulationApplicant);
    }

    /**
     * Crear registro de pago para el postulante
     */
    private function createPaymentRecord(SimulationApplicant $applicant): void
    {
        // Obtener el simulacro con su tarifa
        $examSimulation = $applicant->examSimulation()->with('tariff')->first();
        
        if (!$examSimulation || !$examSimulation->tariff) {
            return; // No crear pago si no hay tarifa configurada
        }

        $tariff = $examSimulation->tariff;

        // Generar número de recibo
        $receipt = Payment::generateReceiptNumber('SIM');

        // Crear el registro de pago
        $payment = Payment::create([
            'receipt' => $receipt,
            'service_code' => $tariff->code,
            'description' => $examSimulation->description,
            'amount' => $tariff->amount,
            'payment_date' => now('America/Lima')->toDateString(),
            'document_number' => $applicant->dni,
            'client_name' => "{$applicant->first_names} {$applicant->last_name_father} {$applicant->last_name_mother}",
            'client_email' => $applicant->email,
            'payable_type' => SimulationApplicant::class,
            'payable_id' => $applicant->id,
            'process_type' => 'simulation',
            'process_id' => $examSimulation->id,
        ]);

        // Crear registro en cartera (histórico)
        PaymentPortfolio::createFromPayment($payment);
    }
}