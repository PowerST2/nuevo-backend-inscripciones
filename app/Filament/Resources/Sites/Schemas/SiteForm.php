<?php

namespace App\Filament\Resources\Sites\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SiteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('period_id')
                    ->label(__('filament.labels.period'))
                    ->relationship('period', 'name')
                    ->required(),
                TextInput::make('code')
                    ->label(__('filament.labels.code'))
                    ->required(),
                TextInput::make('name')
                    ->label(__('filament.labels.name'))
                    ->required(),
                TextInput::make('local')
                    ->label(__('filament.labels.local'))
                    ->required(),
                TextInput::make('direction')
                    ->label(__('filament.labels.direction'))
                    ->required(),
                TextInput::make('phone')
                    ->label(__('filament.labels.phone'))
                    ->tel(),
                TextInput::make('email')
                    ->label(__('filament.labels.email_address'))
                    ->email(),
                Toggle::make('active')
                    ->label(__('filament.labels.active'))
                    ->required(),
            ]);
    }
}
