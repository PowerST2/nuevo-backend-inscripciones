<?php

namespace App\Filament\Resources\Applicants\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ApplicantInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('period.name')
                    ->label('Period')
                    ->placeholder('-'),
                TextEntry::make('code')
                    ->placeholder('-'),
                TextEntry::make('code_cepre')
                    ->placeholder('-'),
                TextEntry::make('paternal_surname')
                    ->placeholder('-'),
                TextEntry::make('maternal_surname')
                    ->placeholder('-'),
                TextEntry::make('names')
                    ->placeholder('-'),
                TextEntry::make('documentType.name')
                    ->label('Document type')
                    ->placeholder('-'),
                TextEntry::make('document_number')
                    ->placeholder('-'),
                TextEntry::make('email')
                    ->label('Email address')
                    ->placeholder('-'),
                TextEntry::make('size')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('weight')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('gender.name')
                    ->label('Gender')
                    ->placeholder('-'),
                TextEntry::make('cellular_phone')
                    ->placeholder('-'),
                TextEntry::make('phone')
                    ->placeholder('-'),
                TextEntry::make('other_phone')
                    ->placeholder('-'),
                TextEntry::make('ubigeo.id')
                    ->label('Ubigeo')
                    ->placeholder('-'),
                TextEntry::make('direction')
                    ->placeholder('-'),
                TextEntry::make('school.id')
                    ->label('School'),
                TextEntry::make('university.name')
                    ->label('University')
                    ->placeholder('-'),
                TextEntry::make('site.name')
                    ->label('Site')
                    ->placeholder('-'),
                TextEntry::make('start_study')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('end_study')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('date_birth')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('countryBirth.name')
                    ->label('Country birth')
                    ->placeholder('-'),
                TextEntry::make('ubigeo_birth_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('modality1.name')
                    ->label('Modality1')
                    ->placeholder('-'),
                TextEntry::make('modality2.name')
                    ->label('Modality2')
                    ->placeholder('-'),
                TextEntry::make('speciality1.name')
                    ->label('Speciality1')
                    ->placeholder('-'),
                TextEntry::make('speciality2.name')
                    ->label('Speciality2')
                    ->placeholder('-'),
                TextEntry::make('speciality3.name')
                    ->label('Speciality3')
                    ->placeholder('-'),
                TextEntry::make('speciality4.name')
                    ->label('Speciality4')
                    ->placeholder('-'),
                TextEntry::make('speciality5.name')
                    ->label('Speciality5')
                    ->placeholder('-'),
                TextEntry::make('speciality6.name')
                    ->label('Speciality6')
                    ->placeholder('-'),
                TextEntry::make('classroom1_id')
                    ->placeholder('-'),
                TextEntry::make('classroom2_id')
                    ->placeholder('-'),
                TextEntry::make('classroom3_id')
                    ->placeholder('-'),
                TextEntry::make('classroom_voca_id')
                    ->placeholder('-'),
                IconEntry::make('annulled')
                    ->boolean(),
                TextEntry::make('user.name')
                    ->label('User'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
