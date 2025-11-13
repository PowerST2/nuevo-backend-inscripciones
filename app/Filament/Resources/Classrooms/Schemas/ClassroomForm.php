<?php

namespace App\Filament\Resources\Classrooms\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ClassroomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->required(),
                TextInput::make('sector')
                    ->required(),
                TextInput::make('capacity')
                    ->required()
                    ->numeric(),
                TextInput::make('available_1')
                    ->required()
                    ->numeric(),
                TextInput::make('assigned_1')
                    ->required()
                    ->numeric(),
                TextInput::make('available_2')
                    ->required()
                    ->numeric(),
                TextInput::make('assigned_2')
                    ->required()
                    ->numeric(),
                TextInput::make('available_3')
                    ->required()
                    ->numeric(),
                TextInput::make('assigned_3')
                    ->required()
                    ->numeric(),
                TextInput::make('available_voca')
                    ->required()
                    ->numeric(),
                TextInput::make('assigned_voca')
                    ->required()
                    ->numeric(),
                Toggle::make('active')
                    ->required(),
                Toggle::make('special')
                    ->required(),
                Toggle::make('vocational')
                    ->required(),
            ]);
    }
}
