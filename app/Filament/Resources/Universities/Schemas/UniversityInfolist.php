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
                TextEntry::make('code'),
                TextEntry::make('name'),
                TextEntry::make('management'),
                TextEntry::make('ubigeo.id')
                    ->label('Ubigeo')
                    ->placeholder('-'),
                TextEntry::make('country.name')
                    ->label('Country'),
                IconEntry::make('activo')
                    ->boolean(),
            ]);
    }
}
