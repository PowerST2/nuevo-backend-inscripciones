<?php

namespace App\Filament\Resources\Universities\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UniversityInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('code')
                    ->label(__('filament.labels.code')),
                TextEntry::make('name')
                    ->label(__('filament.labels.name')),
                TextEntry::make('management')
                    ->label(__('filament.labels.management')),
                TextEntry::make('ubigeo.id')
                    ->label(__('filament.labels.ubigeo'))
                    ->placeholder('-'),
                TextEntry::make('country.name')
                    ->label(__('filament.labels.country')),
                IconEntry::make('activo')
                    ->label(__('filament.labels.active'))
                    ->boolean(),
            ]);
    }
}
