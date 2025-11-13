<?php

namespace App\Filament\Resources\Applicants\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ApplicantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('period.name')
                    ->searchable(),
                TextColumn::make('code')
                    ->searchable(),
                TextColumn::make('code_cepre')
                    ->searchable(),
                TextColumn::make('paternal_surname')
                    ->searchable(),
                TextColumn::make('maternal_surname')
                    ->searchable(),
                TextColumn::make('names')
                    ->searchable(),
                TextColumn::make('documentType.name')
                    ->searchable(),
                TextColumn::make('document_number')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('size')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('weight')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('gender.name')
                    ->searchable(),
                TextColumn::make('cellular_phone')
                    ->searchable(),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('other_phone')
                    ->searchable(),
                TextColumn::make('ubigeo.id')
                    ->searchable(),
                TextColumn::make('direction')
                    ->searchable(),
                TextColumn::make('school.id')
                    ->searchable(),
                TextColumn::make('university.name')
                    ->searchable(),
                TextColumn::make('site.name')
                    ->searchable(),
                TextColumn::make('start_study')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('end_study')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('date_birth')
                    ->date()
                    ->sortable(),
                TextColumn::make('countryBirth.name')
                    ->searchable(),
                TextColumn::make('ubigeo_birth_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('modality1.name')
                    ->searchable(),
                TextColumn::make('modality2.name')
                    ->searchable(),
                TextColumn::make('speciality1.name')
                    ->searchable(),
                TextColumn::make('speciality2.name')
                    ->searchable(),
                TextColumn::make('speciality3.name')
                    ->searchable(),
                TextColumn::make('speciality4.name')
                    ->searchable(),
                TextColumn::make('speciality5.name')
                    ->searchable(),
                TextColumn::make('speciality6.name')
                    ->searchable(),
                TextColumn::make('classroom1_id')
                    ->searchable(),
                TextColumn::make('classroom2_id')
                    ->searchable(),
                TextColumn::make('classroom3_id')
                    ->searchable(),
                TextColumn::make('classroom_voca_id')
                    ->searchable(),
                IconColumn::make('annulled')
                    ->boolean(),
                TextColumn::make('user.name')
                    ->searchable(),
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
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
