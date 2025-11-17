<?php

namespace App\Filament\Resources\Ubigeos\Pages;

use App\Filament\Resources\Ubigeos\UbigeoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageUbigeos extends ManageRecords
{
    protected static string $resource = UbigeoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
