<?php

namespace App\Filament\Resources\Faculties;

use App\Filament\Resources\Faculties\Pages\CreateFaculty;
use App\Filament\Resources\Faculties\Pages\EditFaculty;
use App\Filament\Resources\Faculties\Pages\ListFaculties;
use App\Filament\Resources\Faculties\Schemas\FacultyForm;
use App\Filament\Resources\Faculties\Tables\FacultiesTable;
use App\Models\Faculty;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class FacultyResource extends Resource
{
    protected static ?string $model = Faculty::class;
    protected static ?string $modelLabel = 'Facultad';
    protected static ?string $pluralModelLabel = 'Facultades';

    protected static string | UnitEnum | null $navigationGroup = 'Academico';
    protected static ?int $navigationSort = 6;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return FacultyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FacultiesTable::configure($table);
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
            'index' => ListFaculties::route('/'),
            'create' => CreateFaculty::route('/create'),
            'edit' => EditFaculty::route('/{record}/edit'),
        ];
    }
}
