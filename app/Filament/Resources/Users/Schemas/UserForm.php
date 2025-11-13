<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('filament.labels.name'))
                    ->required(),
                TextInput::make('email')
                    ->label(__('filament.labels.email'))
                    ->email()
                    ->required(),
                DateTimePicker::make('email_verified_at')
                    ->label(__('filament.labels.email') . ' ' . __('filament.labels.updated_at')),
                TextInput::make('password')
                    ->label(__('filament.labels.password'))
                    ->password()
                    ->required(),
            ]);
    }
}
