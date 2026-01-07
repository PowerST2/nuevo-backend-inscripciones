<?php

namespace App\Providers;

use App\Models\Simulation\SimulationApplicant;
use App\Models\Simulation\SimulationProcess;
use App\Observers\Simulation\SimulationApplicantObserver;
use App\Observers\Simulation\SimulationProcessObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registrar observers
        SimulationApplicant::observe(SimulationApplicantObserver::class);
        SimulationProcess::observe(SimulationProcessObserver::class);
    }
}
