<?php

namespace App\Filament\Resources\SystemDocuments;

use App\Filament\Resources\SystemDocuments\Pages\CreateSystemDocument;
use App\Filament\Resources\SystemDocuments\Pages\EditSystemDocument;
use App\Filament\Resources\SystemDocuments\Pages\ListSystemDocuments;
use App\Filament\Resources\SystemDocuments\Schemas\SystemDocumentForm;
use App\Filament\Resources\SystemDocuments\Tables\SystemDocumentsTable;
use App\Models\SystemDocument;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SystemDocumentResource extends Resource
{
    protected static ?string $model = SystemDocument::class;
    protected static ?string $modelLabel = 'Docs. Sistema';
    protected static ?string $pluralModelLabel = 'Docs. de Sistema';

    protected static string | UnitEnum | null $navigationGroup = 'Documentos';
    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentDuplicate;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return SystemDocumentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SystemDocumentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSystemDocuments::route('/'),
            'create' => CreateSystemDocument::route('/create'),
            'edit' => EditSystemDocument::route('/{record}/edit'),
        ];
    }
}
