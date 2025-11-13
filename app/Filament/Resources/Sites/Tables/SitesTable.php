<?php

namespace App\Filament\Resources\Sites\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SitesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('period.name')
                    ->label(__('filament.labels.period'))
                    ->searchable(),
                TextColumn::make('code')
                    ->label(__('filament.labels.code'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(__('filament.labels.name'))
                    ->searchable(),
                TextColumn::make('local')
                    ->label(__('filament.labels.local'))
                    ->searchable(),
                TextColumn::make('direction')
                    ->label(__('filament.labels.direction'))
                    ->searchable(),
                TextColumn::make('phone')
                    ->label(__('filament.labels.phone'))
                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('filament.labels.email_address'))
                    ->searchable(),
                IconColumn::make('active')
                    ->label(__('filament.labels.active'))
                    ->boolean(),
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
