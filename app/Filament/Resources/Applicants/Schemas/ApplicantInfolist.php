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
                    ->label(__('filament.labels.period')),
                TextEntry::make('code')
                    ->label(__('filament.labels.code')),
                TextEntry::make('code_cepre')
                    ->label(__('filament.labels.code_cepre')),
                TextEntry::make('paternal_surname')
                    ->label(__('filament.labels.paternal_surname')),
                TextEntry::make('maternal_surname')
                    ->label(__('filament.labels.maternal_surname')),
                TextEntry::make('names')
                    ->label(__('filament.labels.names')),
                TextEntry::make('documentType.name')
                    ->label(__('filament.labels.document_type')),
                TextEntry::make('document_number')
                    ->label(__('filament.labels.document_number')),
                TextEntry::make('email')
                    ->label(__('filament.labels.email_address')),
                TextEntry::make('size')
                    ->label(__('filament.labels.size')),
                TextEntry::make('weight')
                    ->label(__('filament.labels.weight')),
                TextEntry::make('gender.name')
                    ->label(__('filament.labels.gender')),
                TextEntry::make('cellular_phone')
                    ->label(__('filament.labels.cellular_phone')),
                TextEntry::make('phone')
                    ->label(__('filament.labels.phone')),
                TextEntry::make('other_phone')
                    ->label(__('filament.labels.other_phone')),
                TextEntry::make('ubigeo.description')
                    ->label(__('filament.labels.ubigeo')),
                TextEntry::make('direction')
                    ->label(__('filament.labels.direction')),
                TextEntry::make('school.name')
                    ->label(__('filament.labels.school')),
                TextEntry::make('university.name')
                    ->label(__('filament.labels.university')),
                TextEntry::make('site.name')
                    ->label(__('filament.labels.site')),
                TextEntry::make('start_study')
                    ->label(__('filament.labels.start_study')),
                TextEntry::make('end_study')
                    ->label(__('filament.labels.end_study')),
                TextEntry::make('date_birth')
                    ->label(__('filament.labels.date_birth'))
                    ->date(),
                TextEntry::make('countryBirth.name')
                    ->label(__('filament.labels.country_birth')),
                TextEntry::make('ubigaoBirth.description')
                    ->label(__('filament.labels.ubigeo_birth_id')),
                TextEntry::make('modality1.name')
                    ->label(__('filament.labels.modality1')),
                //TextEntry::make('modality2.name')
                  //  ->label(__('filament.labels.modality2')),
                TextEntry::make('speciality1.name')
                    ->label(__('filament.labels.speciality1')),
                TextEntry::make('speciality2.name')
                    ->label(__('filament.labels.speciality2')),
                //TextEntry::make('speciality3.name')
                 //   ->label(__('filament.labels.speciality3')),
                //TextEntry::make('speciality4.name')
                 //   ->label(__('filament.labels.speciality4')),
                //TextEntry::make('speciality5.name')
                  //  ->label(__('filament.labels.speciality5')),
               // TextEntry::make('speciality6.name')
                   // ->label(__('filament.labels.speciality6')),
                TextEntry::make('classroom1.code')
                    ->label(__('filament.labels.classroom1')),
                TextEntry::make('classroom2.code')
                    ->label(__('filament.labels.classroom2')),
                TextEntry::make('classroom3.code')
                    ->label(__('filament.labels.classroom3')),
                TextEntry::make('classroomVoca.code')
                    ->label(__('filament.labels.classroom_voca')),
                /*IconEntry::make('annulled')
                    ->label(__('filament.labels.annulled'))
                    ->boolean(),
                //TextEntry::make('user.name')
                   // ->label(__('filament.labels.user')),
                TextEntry::make('created_at')
                    ->label(__('filament.labels.created_at'))
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->label(__('filament.labels.updated_at'))
                    ->dateTime(),*/
            ]);
    }
}
