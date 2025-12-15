<?php

namespace App\Filament\Resources\ModalityDocuments;

use App\Filament\Resources\ModalityDocuments\Pages\ManageModalityDocuments;
use App\Models\ModalityDocument;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class ModalityDocumentResource extends Resource
{
    protected static ?string $model = ModalityDocument::class;
    protected static ?string $modelLabel = 'Documento por Modalidad';
    protected static ?string $pluralModelLabel = 'Documentos por Modalidad';

    protected static string | UnitEnum | null $navigationGroup = 'Documentos';
    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('modality_id')
                    ->relationship('modality', 'name')
                    ->required()
                    ->label('Modalidad'),
                TextInput::make('document_code')
                    ->required()
                    ->label('Código del Documento'),
                FileUpload::make('path_document')
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
                Toggle::make('active')
                    ->default(true)
                    ->label('Activo'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('modality.name')
                    ->searchable(),
                TextColumn::make('document_code')
                    ->searchable(),
                TextColumn::make('path_document')
                    ->searchable(),
                IconColumn::make('active')
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
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageModalityDocuments::route('/'),
        ];
    }
}
