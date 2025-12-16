<?php

namespace App\Observers\Simulation;

use App\Models\Simulation\SimulationApplicant;
use App\Models\Simulation\SimulationProcess;

class SimulationApplicantObserver
{
    /**
     * Handle the SimulationApplicant "created" event.
     */
    public function created(SimulationApplicant $simulationApplicant): void
    {
        SimulationProcess::create([
            'simulation_applicant_id' => $simulationApplicant->id,
            // Aquí definimos la hora de creación manual con UTC-5
            'pre_registration_at' => now('America/Lima'),
            'payment_at' => null,
            'data_confirmation_at' => null,
            'registration_at' => null,
        ]);
    }
}