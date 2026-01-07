<?php

namespace App\Filament\Resources\Modalities;

use App\Filament\Resources\Modalities\Pages\ManageModalities;
use App\Models\Modality;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class ModalityResource extends Resource
{
    protected static ?string $model = Modality::class;
    protected static ?string $modelLabel = 'Modalidad';
    protected static ?string $pluralModelLabel = 'Modalidades';

    protected static string | UnitEnum | null $navigationGroup = 'Academico';
    protected static ?int $navigationSort = 5;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label(__('filament.labels.code'))
                    ->required(),
                TextInput::make('name')
                    ->label(__('filament.labels.name'))
                    ->required(),
                TextInput::make('name_regulation')
                    ->label(__('filament.labels.name_regulation'))
                    ->required(),
                TextInput::make('description')
                    ->label(__('filament.labels.description')),
                Toggle::make('start_studies')
                    ->label(__('filament.labels.start_studies'))
                    ->required(),
                Toggle::make('active')
                    ->label(__('filament.labels.active'))
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('code')
                    ->label(__('filament.labels.code'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(__('filament.labels.name'))
                    ->searchable(),
                TextColumn::make('name_regulation')
                    ->label(__('filament.labels.name_regulation'))
                    ->searchable(),
                TextColumn::make('description')
                    ->label(__('filament.labels.description'))
                    ->searchable(),
                IconColumn::make('start_studies')
                    ->label(__('filament.labels.start_studies'))
                    ->boolean(),
                IconColumn::make('active')
                    ->label(__('filament.labels.active'))
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label(__('filament.labels.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('filament.labels.updated_at'))
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
            'index' => ManageModalities::route('/'),
        ];
    }
}
