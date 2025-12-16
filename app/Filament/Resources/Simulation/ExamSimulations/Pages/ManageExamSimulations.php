<?php

namespace App\Filament\Resources\Simulation\ExamSimulations\Pages;

use App\Filament\Resources\Simulation\ExamSimulations\ExamSimulationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageExamSimulations extends ManageRecords
{
    protected static string $resource = ExamSimulationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
