<?php

namespace App\Filament\Resources\SystemDocuments\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;

class SystemDocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->label('Nombre del Documento')
                    ->placeholder('Ej: contrato, reglamento, ficha'),
                FileUpload::make('path')
                    ->label('Seleccionar Archivo')
                    ->disk('public')
                    ->directory('documents')
                    ->visibility('public')
                    ->preserveFilenames()
                    ->previewable(true)
                    ->downloadable(true)
                    ->openable(true)
                    ->acceptedFileTypes([
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'image/jpeg',
                        'image/png',
                        'image/gif',
                        'text/plain',
                    ])
                    ->maxSize(10240)
                    ->hint('Máximo 10MB. Formatos: PDF, DOC, DOCX, XLS, XLSX, PNG, JPG, GIF, TXT'),
                TextInput::make('type')
                    ->required()
                    ->label('Tipo de Documento')
                    ->placeholder('Ej: certificado, contrato, etc.'),
                Toggle::make('active')
                    ->default(true)
                    ->label('Activo'),
                Toggle::make('virtual')
                    ->default(false)
                    ->label('Virtual'),
                RichEditor::make('text')
                    ->nullable()
                    ->columnSpanFull()
                    ->label('Texto Adicional')
                    ->placeholder('Texto adicional o notas sobre el documento'),
            ]);
    }
}
