<?php

namespace App\Filament\Resources\Simulation\SimulationApplicants\Pages;

use App\Filament\Resources\Simulation\SimulationApplicants\SimulationApplicantResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageSimulationApplicants extends ManageRecords
{
    protected static string $resource = SimulationApplicantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
