<?php

namespace App\Filament\Resources\Periods;

use App\Filament\Resources\Periods\Pages\ManagePeriods;
use App\Models\Period;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;
class PeriodResource extends Resource
{
    protected static ?string $model = Period::class;
    protected static ?string $modelLabel = 'Periodo';
    protected static ?string $pluralModelLabel = 'Periodos';

    protected static string | UnitEnum | null $navigationGroup = 'Configurar';
    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Calendar;

    protected static ?string $recordTitleAttribute = 'name';

     public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // --- CAMBIO AQUÍ ---
                // Agrega el campo 'code'
                TextInput::make('code')
                    ->label(__('filament.labels.code'))
                    ->required()
                    ->maxLength(255)
                    // Asegura que el código sea único, ignorando el registro actual al editar
                    ->unique(Period::class, 'code', ignoreRecord: true), 
                
                TextInput::make('name')
                    ->label(__('filament.labels.name'))
                    ->required()
                    ->maxLength(255),
            ]);
    }

      public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                // --- CAMBIO AQUÍ ---
                // Agrega la columna 'code' a la tabla
                TextColumn::make('code')
                    ->label(__('filament.labels.code'))
                    ->searchable()
                    ->sortable(), // Permite ordenar por código

                TextColumn::make('name')
                    ->label(__('filament.labels.name'))
                    ->searchable(),
                
                // Opcional: muestra cuándo se creó
                TextColumn::make('created_at')
                    ->label(__('filament.labels.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true), // Oculta por defecto
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
            'index' => ManagePeriods::route('/'),
        ];
    }
}
