<?php

namespace App\Filament\Resources\ScheduleActivities\Pages;

use App\Filament\Resources\ScheduleActivities\ScheduleActivityResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageScheduleActivities extends ManageRecords
{
    protected static string $resource = ScheduleActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
