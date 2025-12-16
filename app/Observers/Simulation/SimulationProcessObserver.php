<?php

namespace App\Observers\Simulation;

use App\Models\Simulation\SimulationProcess;

class SimulationProcessObserver
{
    /**
     * Handle the SimulationProcess "updated" event.
     * Genera el código cuando todos los campos del proceso son true
     */
    public function updated(SimulationProcess $simulationProcess): void
    {
        $this->checkAndGenerateCode($simulationProcess);
    }

    /**
     * Verifica si todos los campos del proceso son true y genera el código
     */
    private function checkAndGenerateCode(SimulationProcess $simulationProcess): void
    {
        // Verificar si todos los campos son true
        if (
            $simulationProcess->pre_registration &&
            $simulationProcess->payment &&
            $simulationProcess->data_confirmation &&
            $simulationProcess->registration
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
