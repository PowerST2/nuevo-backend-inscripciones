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
                    ->label(__('filament.labels.period_id'))
                    ->searchable(),
                TextColumn::make('code')
                    ->label(__('filament.labels.code'))
                    ->searchable(),
                TextColumn::make('code_cepre')
                    ->label(__('filament.labels.code_cepre'))
                    ->searchable(),
                TextColumn::make('paternal_surname')
                    ->label(__('filament.labels.paternal_surname'))
                    ->searchable(),
                TextColumn::make('maternal_surname')
                    ->label(__('filament.labels.maternal_surname'))
                    ->searchable(),
                TextColumn::make('names')
                    ->label(__('filament.labels.names'))
                    ->searchable(),
                TextColumn::make('documentType.name')
                    ->label(__('filament.labels.document_type_id'))
                    ->searchable(),
                TextColumn::make('document_number')
                    ->label(__('filament.labels.document_number'))
                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('filament.labels.email_address'))
                    ->searchable(),
                TextColumn::make('size')
                    ->label(__('filament.labels.size'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('weight')
                    ->label(__('filament.labels.weight'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('gender.name')
                    ->label(__('filament.labels.gender_id'))
                    ->searchable(),
                TextColumn::make('cellular_phone')
                    ->label(__('filament.labels.cellular_phone'))
                    ->searchable(),
                TextColumn::make('phone')
                    ->label(__('filament.labels.phone'))
                    ->searchable(),
                TextColumn::make('other_phone')
                    ->label(__('filament.labels.other_phone'))
                    ->searchable(),
                TextColumn::make('ubigeo.id')
                    ->label(__('filament.labels.ubigeo_id'))
                    ->searchable(),
                TextColumn::make('direction')
                    ->label(__('filament.labels.direction'))
                    ->searchable(),
                TextColumn::make('school.id')
                    ->label(__('filament.labels.school_id'))
                    ->searchable(),
                TextColumn::make('university.name')
                    ->label(__('filament.labels.university_id'))
                    ->searchable(),
                TextColumn::make('site.name')
                    ->label(__('filament.labels.site_id'))
                    ->searchable(),
                TextColumn::make('start_study')
                    ->label(__('filament.labels.start_study'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('end_study')
                    ->label(__('filament.labels.end_study'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('date_birth')
                    ->label(__('filament.labels.date_birth'))
                    ->date()
                    ->sortable(),
                TextColumn::make('countryBirth.name')
                    ->label(__('filament.labels.country_birth_id'))
                    ->searchable(),
                TextColumn::make('ubigeo_birth_id')
                    ->label(__('filament.labels.ubigeo_birth_id'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('modality1.name')
                    ->label(__('filament.labels.modality1_id'))
                    ->searchable(),
                TextColumn::make('modality2.name')
                    ->label(__('filament.labels.modality2_id'))
                    ->searchable(),
                TextColumn::make('speciality1.name')
                    ->label(__('filament.labels.speciality1_id'))
                    ->searchable(),
                TextColumn::make('speciality2.name')
                    ->label(__('filament.labels.speciality2_id'))
                    ->searchable(),
                TextColumn::make('speciality3.name')
                    ->label(__('filament.labels.speciality3_id'))
                    ->searchable(),
                TextColumn::make('speciality4.name')
                    ->label(__('filament.labels.speciality4_id'))
                    ->searchable(),
                TextColumn::make('speciality5.name')
                    ->label(__('filament.labels.speciality5_id'))
                    ->searchable(),
                TextColumn::make('speciality6.name')
                    ->label(__('filament.labels.speciality6_id'))
                    ->searchable(),
                TextColumn::make('classroom1_id')
                    ->label(__('filament.labels.classroom1_id'))
                    ->searchable(),
                TextColumn::make('classroom2_id')
                    ->label(__('filament.labels.classroom2_id'))
                    ->searchable(),
                TextColumn::make('classroom3_id')
                    ->label(__('filament.labels.classroom3_id'))
                    ->searchable(),
                TextColumn::make('classroom_voca_id')
                    ->label(__('filament.labels.classroom_voca_id'))
                    ->searchable(),
                IconColumn::make('annulled')
                    ->label(__('filament.labels.annulled'))
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
