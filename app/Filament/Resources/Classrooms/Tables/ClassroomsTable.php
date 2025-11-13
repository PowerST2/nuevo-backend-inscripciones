<?php

namespace App\Filament\Resources\Classrooms\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClassroomsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label(__('filament.labels.code'))
                    ->searchable(),
                TextColumn::make('sector')
                    ->label(__('filament.labels.sector'))
                    ->searchable(),
                TextColumn::make('capacity')
                    ->label(__('filament.labels.capacity'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('available_1')
                    ->label(__('filament.labels.available_1'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('assigned_1')
                    ->label(__('filament.labels.assigned_1'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('available_2')
                    ->label(__('filament.labels.available_2'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('assigned_2')
                    ->label(__('filament.labels.assigned_2'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('available_3')
                    ->label(__('filament.labels.available_3'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('assigned_3')
                    ->label(__('filament.labels.assigned_3'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('available_voca')
                    ->label(__('filament.labels.available_voca'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('assigned_voca')
                    ->label(__('filament.labels.assigned_voca'))
                    ->numeric()
                    ->sortable(),
                IconColumn::make('active')
                    ->label(__('filament.labels.active'))
                    ->boolean(),
                IconColumn::make('special')
                    ->label(__('filament.labels.special'))
                    ->boolean(),
                IconColumn::make('vocational')
                    ->label(__('filament.labels.vocational'))
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
