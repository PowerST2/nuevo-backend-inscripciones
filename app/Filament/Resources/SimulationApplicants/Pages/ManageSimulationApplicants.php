<?php

namespace App\Filament\Resources\SimulationApplicants\Pages;

use App\Filament\Resources\SimulationApplicants\SimulationApplicantResource;
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
