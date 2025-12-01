<?php

namespace App\Filament\Resources\SystemDocuments\Pages;

use App\Filament\Resources\SystemDocuments\SystemDocumentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSystemDocuments extends ListRecords
{
    protected static string $resource = SystemDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
