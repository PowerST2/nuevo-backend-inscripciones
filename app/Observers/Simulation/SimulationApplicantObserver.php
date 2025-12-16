<?php

namespace App\Observers\Simulation;

use App\Models\Simulation\SimulationApplicant;
use App\Models\Simulation\SimulationProcess;

class SimulationApplicantObserver
{
    /**
     * Handle the SimulationApplicant "created" event.
     * Crea el registro de proceso con pre_registration en true
     */
    public function created(SimulationApplicant $simulationApplicant): void
    {
        // Crear registro de proceso con pre_registration en true
        SimulationProcess::create([
            'simulation_applicant_id' => $simulationApplicant->id,
            'pre_registration' => true,
            'payment' => false,
            'data_confirmation' => false,
            'registration' => false,
        ]);
    }
}
