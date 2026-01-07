<?php

namespace App\Filament\Resources\SystemDocuments\Pages;

use App\Filament\Resources\SystemDocuments\SystemDocumentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSystemDocument extends EditRecord
{
    protected static string $resource = SystemDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
