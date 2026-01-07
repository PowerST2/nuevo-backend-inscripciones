<?php

namespace App\Filament\Resources\ModalityDocuments\Pages;

use App\Filament\Resources\ModalityDocuments\ModalityDocumentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageModalityDocuments extends ManageRecords
{
    protected static string $resource = ModalityDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
