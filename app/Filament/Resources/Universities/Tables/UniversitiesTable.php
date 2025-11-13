<?php

namespace App\Filament\Resources\Universities\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UniversitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label(__('filament.labels.code'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(__('filament.labels.name'))
                    ->searchable(),
                TextColumn::make('management')
                    ->label(__('filament.labels.management'))
                    ->searchable(),
                TextColumn::make('ubigeo.description')
                    ->label(__('filament.labels.ubigeo'))
                    ->searchable(),
                TextColumn::make('country.name')
                    ->label(__('filament.labels.country'))
                    ->searchable(),
                IconColumn::make('active')
                    ->label(__('filament.labels.active'))
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
