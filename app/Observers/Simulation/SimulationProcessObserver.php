<?php

namespace App\Observers\Simulation;

use App\Models\Simulation\SimulationProcess;

class SimulationProcessObserver
{
    /**
     * Handle the SimulationProcess "updated" event.
     */
    public function updated(SimulationProcess $simulationProcess): void
    {
        $this->checkAndGenerateCode($simulationProcess);
    }

    /**
     * Verifica si todos los campos tienen fecha asignada y genera el código
     */
    private function checkAndGenerateCode(SimulationProcess $simulationProcess): void
    {
        // Verificar si todos los campos tienen valor (no son nulos)
        // En PHP, un objeto Carbon (fecha) se evalúa como true, y null como false.
        if (
            $simulationProcess->pre_registration_at &&
            $simulationProcess->payment_at &&
            $simulationProcess->data_confirmation_at &&
            $simulationProcess->registration_at
        ) {
            $applicant = $simulationProcess->simulationApplicant;

            // Solo generar si aún no tiene código
            if ($applicant && empty($applicant->code)) {
                $this->generateCode($applicant);
            }
        }
    }

    /**
     * Genera un código único para el aplicante
     * Formato: SIM{id_padded}
     */
    private function generateCode($simulationApplicant): void
    {
        $paddedId = str_pad($simulationApplicant->id, 4, '0', STR_PAD_LEFT);
        $code = "SIM{$paddedId}";

        $simulationApplicant->code = $code;
        $simulationApplicant->saveQuietly();
    }
}