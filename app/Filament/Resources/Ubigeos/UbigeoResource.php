<?php

namespace App\Filament\Resources\Ubigeos;

use App\Filament\Resources\Ubigeos\Pages\ManageUbigeos;
use App\Models\Ubigeo;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class UbigeoResource extends Resource
{
    protected static ?string $model = Ubigeo::class;
    protected static ?string $modelLabel = 'Ubigeo';
    protected static ?string $pluralModelLabel = 'Ubigeos';

    protected static string | UnitEnum | null $navigationGroup = 'Configuración Basica';
    protected static ?int $navigationSort = 4;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label(__('filament.labels.code'))
                    ->required(),
                TextInput::make('description')
                    ->label(__('filament.labels.description'))
                    ->required(),
                TextInput::make('department')
                    ->label(__('filament.labels.department'))
                    ->required(),
                TextInput::make('province')
                    ->label(__('filament.labels.province'))
                    ->required(),
                TextInput::make('district')
                    ->label(__('filament.labels.district'))
                    ->required(),
                TextInput::make('code_reniec')
                    ->label(__('filament.labels.code_reniec')),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('code')
                    ->label(__('filament.labels.code')),
                TextEntry::make('description')
                    ->label(__('filament.labels.description')),
                TextEntry::make('department')
                    ->label(__('filament.labels.department')),
                TextEntry::make('province')
                    ->label(__('filament.labels.province')),
                TextEntry::make('district')
                    ->label(__('filament.labels.district')),
                TextEntry::make('code_reniec')
                    ->label(__('filament.labels.code_reniec'))
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->label(__('filament.labels.created_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->label(__('filament.labels.updated_at'))
                    ->dateTime()
                    ->placeholder('-'),
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
                TextColumn::make('description')
                    ->label(__('filament.labels.description'))
                    ->searchable(),
                TextColumn::make('department')
                    ->label(__('filament.labels.department'))
                    ->searchable(),
                TextColumn::make('province')
                    ->label(__('filament.labels.province'))
                    ->searchable(),
                TextColumn::make('district')
                    ->label(__('filament.labels.district'))
                    ->searchable(),
                TextColumn::make('code_reniec')
                    ->label(__('filament.labels.code_reniec'))
                    ->searchable(),
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
                ViewAction::make(),
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
            'index' => ManageUbigeos::route('/'),
        ];
    }
}
