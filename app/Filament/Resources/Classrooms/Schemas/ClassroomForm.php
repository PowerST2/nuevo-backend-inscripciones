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
                    ->label(__('filament.labels.code'))
                    ->required(),
                TextInput::make('sector')
                    ->label(__('filament.labels.sector'))
                    ->required(),
                TextInput::make('capacity')
                    ->label(__('filament.labels.capacity'))
                    ->required()
                    ->numeric(),
                TextInput::make('available_1')
                    ->label(__('filament.labels.available_1'))
                    ->required()
                    ->numeric(),
                TextInput::make('assigned_1')
                    ->label(__('filament.labels.assigned_1'))
                    ->required()
                    ->numeric(),
                TextInput::make('available_2')
                    ->label(__('filament.labels.available_2'))
                    ->required()
                    ->numeric(),
                TextInput::make('assigned_2')
                    ->label(__('filament.labels.assigned_2'))
                    ->required()
                    ->numeric(),
                TextInput::make('available_3')
                    ->label(__('filament.labels.available_3'))
                    ->required()
                    ->numeric(),
                TextInput::make('assigned_3')
                    ->label(__('filament.labels.assigned_3'))
                    ->required()
                    ->numeric(),
                TextInput::make('available_voca')
                    ->label(__('filament.labels.available_voca'))
                    ->required()
                    ->numeric(),
                TextInput::make('assigned_voca')
                    ->label(__('filament.labels.assigned_voca'))
                    ->required()
                    ->numeric(),
                Toggle::make('active')
                    ->label(__('filament.labels.active'))
                    ->required(),
                Toggle::make('special')
                    ->label(__('filament.labels.special'))
                    ->required(),
                Toggle::make('vocational')
                    ->label(__('filament.labels.vocational'))
                    ->required(),
            ]);
    }
}
