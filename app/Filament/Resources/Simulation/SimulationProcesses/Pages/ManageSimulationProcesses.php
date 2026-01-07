<?php

namespace App\Filament\Resources\Simulation\SimulationProcesses\Pages;

use App\Filament\Resources\Simulation\SimulationProcesses\SimulationProcessResource;
use Filament\Resources\Pages\ManageRecords;

class ManageSimulationProcesses extends ManageRecords
{
    protected static string $resource = SimulationProcessResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No permitimos crear procesos manualmente, se crean automáticamente
        ];
    }
}
