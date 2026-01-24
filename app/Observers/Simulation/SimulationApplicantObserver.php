<?php

namespace App\Observers\Simulation;

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

        // Crear el registro de obligación de pago (cartera para OCEF)
        $this->createPaymentObligation($simulationApplicant);
    }

    /**
     * Crear registro de obligación de pago (cartera) para el postulante.
     * Este registro representa la deuda que se enviará al banco (OCEF).
     * El pago real (Payment) se creará cuando se procese el CSV del banco confirmando el ingreso.
     */
    private function createPaymentObligation(SimulationApplicant $applicant): void
    {
        // Cargar relaciones necesarias: simulacro y tarifa asignada al postulante
        $applicant->loadMissing('examSimulation', 'tariff');
        $examSimulation = $applicant->examSimulation;
        $tariff = $applicant->tariff;

        if (!$examSimulation || !$tariff) {
            return; // No crear obligación si no hay tarifa configurada
        }

        // Generar número de recibo para la obligación
        $receipt = PaymentPortfolio::generateReceiptNumber('SIM');

        // Crear registro de cartera (obligación/deuda) - NO se crea Payment aquí
        PaymentPortfolio::createObligation([
            'receipt' => $receipt,
            'service_code' => $tariff->code,
            'description' => $examSimulation->description,
            'amount' => $tariff->amount,
            'payment_date' => null, // Se llenará cuando se confirme el pago
            'document_number' => $applicant->dni,
            'client_name' => "{$applicant->first_names} {$applicant->last_name_father} {$applicant->last_name_mother}",
            'client_email' => $applicant->email,
            'payable_type' => SimulationApplicant::class,
            'payable_id' => $applicant->id,
            'process_type' => 'simulation',
            'process_id' => $examSimulation->id,
        ]);
    }
}