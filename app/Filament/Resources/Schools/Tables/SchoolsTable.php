<?php

namespace App\Filament\Resources\Schools\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SchoolsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('modular_code')
                    ->label(__('filament.labels.modular_code'))
                    ->searchable(),
                TextColumn::make('annexed')
                    ->label(__('filament.labels.annexed'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('level')
                    ->label(__('filament.labels.level'))
                    ->searchable(),
                TextColumn::make('nombre')
                    ->label(__('filament.labels.nombre'))
                    ->searchable(),
                TextColumn::make('management_minedu')
                    ->label(__('filament.labels.management_minedu'))
                    ->searchable(),
                TextColumn::make('management')
                    ->label(__('filament.labels.management'))
                    ->searchable(),
                TextColumn::make('director')
                    ->label(__('filament.labels.director'))
                    ->searchable(),
                TextColumn::make('address')
                    ->label(__('filament.labels.address'))
                    ->searchable(),
                TextColumn::make('phones')
                    ->label(__('filament.labels.phones'))
                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('filament.labels.email_address'))
                    ->searchable(),
                TextColumn::make('ubigeo.description')
                    ->label(__('filament.labels.ubigeo'))
                    ->searchable(),
                TextColumn::make('country.name')
                    ->label(__('filament.labels.country'))
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label(__('filament.labels.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('filament.labels.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
