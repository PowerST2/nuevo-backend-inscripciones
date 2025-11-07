<?php

namespace App\Filament\Resources\Universities\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UniversityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('management')
                    ->required(),
                Select::make('ubigeo_id')
                    ->relationship('ubigeo', 'id'),
                Select::make('country_id')
                    ->relationship('country', 'name')
                    ->required(),
                Toggle::make('activo')
                    ->required(),
            ]);
    }
}
