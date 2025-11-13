<?php

namespace App\Filament\Resources\Faculties\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class FacultyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label(__('filament.labels.code'))
                    ->required(),
                TextInput::make('name')
                    ->label(__('filament.labels.name'))
                    ->required(),
                TextInput::make('acronym')
                    ->label(__('filament.labels.acronym')),
            ]);
    }
}
