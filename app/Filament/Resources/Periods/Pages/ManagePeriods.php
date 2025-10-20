<?php

namespace App\Filament\Resources\Periods\Pages;

use App\Filament\Resources\Periods\PeriodResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManagePeriods extends ManageRecords
{
    protected static string $resource = PeriodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
