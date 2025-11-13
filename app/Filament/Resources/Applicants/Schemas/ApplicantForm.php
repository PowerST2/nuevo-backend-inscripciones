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
                    ->relationship('period', 'name'),
                TextInput::make('code'),
                TextInput::make('code_cepre'),
                TextInput::make('paternal_surname'),
                TextInput::make('maternal_surname'),
                TextInput::make('names'),
                Select::make('document_type_id')
                    ->relationship('documentType', 'name'),
                TextInput::make('document_number'),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('size')
                    ->numeric(),
                TextInput::make('weight')
                    ->numeric(),
                Select::make('gender_id')
                    ->relationship('gender', 'name'),
                TextInput::make('cellular_phone')
                    ->tel(),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('other_phone')
                    ->tel(),
                Select::make('ubigeo_id')
                    ->relationship('ubigeo', 'id'),
                TextInput::make('direction'),
                Select::make('school_id')
                    ->relationship('school', 'id')
                    ->required(),
                Select::make('university_id')
                    ->relationship('university', 'name'),
                Select::make('site_id')
                    ->relationship('site', 'name'),
                TextInput::make('start_study')
                    ->numeric(),
                TextInput::make('end_study')
                    ->numeric(),
                DatePicker::make('date_birth'),
                Select::make('country_birth_id')
                    ->relationship('countryBirth', 'name'),
                TextInput::make('ubigeo_birth_id')
                    ->numeric(),
                Select::make('modality1_id')
                    ->relationship('modality1', 'name'),
                Select::make('modality2_id')
                    ->relationship('modality2', 'name'),
                Select::make('speciality1_id')
                    ->relationship('speciality1', 'name'),
                Select::make('speciality2_id')
                    ->relationship('speciality2', 'name'),
                Select::make('speciality3_id')
                    ->relationship('speciality3', 'name'),
                Select::make('speciality4_id')
                    ->relationship('speciality4', 'name'),
                Select::make('speciality5_id')
                    ->relationship('speciality5', 'name'),
                Select::make('speciality6_id')
                    ->relationship('speciality6', 'name'),
                TextInput::make('classroom1_id'),
                TextInput::make('classroom2_id'),
                TextInput::make('classroom3_id'),
                TextInput::make('classroom_voca_id'),
                Toggle::make('annulled')
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
            ]);
    }
}
