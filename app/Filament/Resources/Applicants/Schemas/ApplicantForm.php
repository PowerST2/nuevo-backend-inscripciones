<?php

namespace App\Filament\Resources\Applicants\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ApplicantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('period_id')
                    ->label(__('filament.labels.period_id'))
                    ->relationship('period', 'name'),
                /*TextInput::make('code')
                    ->label(__('filament.labels.code')),*/
                TextInput::make('code_cepre')
                    ->label(__('filament.labels.code_cepre')),
                TextInput::make('paternal_surname')
                    ->label(__('filament.labels.paternal_surname')),
                TextInput::make('maternal_surname')
                    ->label(__('filament.labels.maternal_surname')),
                TextInput::make('names')
                    ->label(__('filament.labels.names')),
                Select::make('document_type_id')
                    ->label(__('filament.labels.document_type_id'))
                    ->relationship('documentType', 'name'),
                TextInput::make('document_number')
                    ->label(__('filament.labels.document_number')),
                TextInput::make('email')
                    ->label(__('filament.labels.email_address'))
                    ->email(),
                TextInput::make('size')
                    ->label(__('filament.labels.size'))
                    ->numeric(),
                TextInput::make('weight')
                    ->label(__('filament.labels.weight'))
                    ->numeric(),
                Select::make('gender_id')
                    ->label(__('filament.labels.gender_id'))
                    ->relationship('gender', 'name'),
                TextInput::make('cellular_phone')
                    ->label(__('filament.labels.cellular_phone'))
                    ->tel(),
                TextInput::make('phone')
                    ->label(__('filament.labels.phone'))
                    ->tel(),
                TextInput::make('other_phone')
                    ->label(__('filament.labels.other_phone'))
                    ->tel(),
                Select::make('ubigeo_id')
                    ->label(__('filament.labels.ubigeo_id'))
                    ->relationship('ubigeo', 'id'),
                TextInput::make('direction')
                    ->label(__('filament.labels.direction')),
                Select::make('school_id')
                    ->label(__('filament.labels.school_id'))
                    ->relationship('school', 'id')
                    ->required(),
                Select::make('university_id')
                    ->label(__('filament.labels.university_id'))
                    ->relationship('university', 'name'),
                Select::make('site_id')
                    ->label(__('filament.labels.site_id'))
                    ->relationship('site', 'name'),
                TextInput::make('start_study')
                    ->label(__('filament.labels.start_study'))
                    ->numeric(),
                TextInput::make('end_study')
                    ->label(__('filament.labels.end_study'))
                    ->numeric(),
                DatePicker::make('date_birth')
                    ->label(__('filament.labels.date_birth')),
                Select::make('country_birth_id')
                    ->label(__('filament.labels.country_birth_id'))
                    ->relationship('countryBirth', 'name'),
                TextInput::make('ubigeo_birth_id')
                    ->label(__('filament.labels.ubigeo_birth_id'))
                    ->numeric(),
                Select::make('modality1_id')
                    ->label(__('filament.labels.modality1_id'))
                    ->relationship('modality1', 'name'),
                Select::make('modality2_id')
                    ->label(__('filament.labels.modality2_id'))
                    ->relationship('modality2', 'name'),
                Select::make('speciality1_id')
                    ->label(__('filament.labels.speciality1_id'))
                    ->relationship('speciality1', 'name'),
                Select::make('speciality2_id')
                    ->label(__('filament.labels.speciality2_id'))
                    ->relationship('speciality2', 'name'),
                Select::make('speciality3_id')
                    ->label(__('filament.labels.speciality3_id'))
                    ->relationship('speciality3', 'name'),
                Select::make('speciality4_id')
                    ->label(__('filament.labels.speciality4_id'))
                    ->relationship('speciality4', 'name'),
                Select::make('speciality5_id')
                    ->label(__('filament.labels.speciality5_id'))
                    ->relationship('speciality5', 'name'),
                Select::make('speciality6_id')
                    ->label(__('filament.labels.speciality6_id'))
                    ->relationship('speciality6', 'name'),
                TextInput::make('classroom1_id')
                    ->label(__('filament.labels.classroom1_id')),
                TextInput::make('classroom2_id')
                    ->label(__('filament.labels.classroom2_id')),
                TextInput::make('classroom3_id')
                    ->label(__('filament.labels.classroom3_id')),
                TextInput::make('classroom_voca_id')
                    ->label(__('filament.labels.classroom_voca_id')),
                Toggle::make('annulled')
                    ->label(__('filament.labels.annulled'))
                    ->required(),
                Select::make('user_id')
                    ->label(__('filament.labels.user_id'))
                    ->relationship('user', 'name')
                    ->required(),
            ]);
    }
}
