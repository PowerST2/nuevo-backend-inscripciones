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
                    ->searchable(),
                TextColumn::make('sector')
                    ->searchable(),
                TextColumn::make('capacity')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('available_1')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('assigned_1')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('available_2')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('assigned_2')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('available_3')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('assigned_3')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('available_voca')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('assigned_voca')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('active')
                    ->boolean(),
                IconColumn::make('special')
                    ->boolean(),
                IconColumn::make('vocational')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
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
