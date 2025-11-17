<?php

namespace App\Filament\Resources\Modalities\Pages;

use App\Filament\Resources\Modalities\ModalityResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageModalities extends ManageRecords
{
    protected static string $resource = ModalityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
