<?php

namespace App\Filament\Resources\Tariffs;

use App\Filament\Resources\Tariffs\Pages\ManageTariffs;
use App\Models\Tariff;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class TariffResource extends Resource
{
    protected static ?string $model = Tariff::class;
    protected static ?string $modelLabel = 'Tarifario';
    protected static ?string $pluralModelLabel = 'Tarifarios';

    protected static string | UnitEnum | null $navigationGroup = 'Configuración Basica';
    protected static ?int $navigationSort = 10;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $recordTitleAttribute = 'code';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Código')
                    ->required()
                    ->maxLength(5)
                    ->unique(ignoreRecord: true),
                Textarea::make('description')
                    ->label('Descripción')
                    ->columnSpanFull(),
                TextInput::make('item')
                    ->label('Partida')
                    ->maxLength(10),
                TextInput::make('project')
                    ->label('Proyecto')
                    ->maxLength(10),
                TextInput::make('amount')
                    ->label('Monto')
                    ->required()
                    ->numeric()
                    ->prefix('S/')
                    ->default(0),
                TextInput::make('sort_order')
                    ->label('Orden')
                    ->numeric()
                    ->default(null),
                Toggle::make('active')
                    ->label('Activo')
                    ->default(true),
                Toggle::make('is_admission')
                    ->label('¿Es para Admisión?')
                    ->helperText('Activar si es para admisión, desactivar si es para simulacro')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('code')
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('code')
                    ->label('Código')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Descripción')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('item')
                    ->label('Partida')
                    ->searchable(),
                TextColumn::make('project')
                    ->label('Proyecto')
                    ->searchable(),
                TextColumn::make('amount')
                    ->label('Monto')
                    ->money('PEN')
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->label('Orden')
                    ->sortable(),
                IconColumn::make('active')
                    ->label('Activo')
                    ->boolean(),
                IconColumn::make('is_admission')
                    ->label('Admisión')
                    ->boolean()
                    ->trueIcon('heroicon-o-academic-cap')
                    ->falseIcon('heroicon-o-clipboard-document-list')
                    ->trueColor('primary')
                    ->falseColor('warning'),
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Actualizado')
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
            'index' => ManageTariffs::route('/'),
        ];
    }
}
