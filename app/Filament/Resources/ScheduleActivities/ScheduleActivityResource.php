<?php

namespace App\Filament\Resources\ScheduleActivities;

use App\Filament\Resources\ScheduleActivities\Pages\ManageScheduleActivities;
use App\Models\ScheduleActivity;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class ScheduleActivityResource extends Resource
{
    protected static ?string $model = ScheduleActivity::class;
    protected static ?string $modelLabel = 'Calendario de Activ.';
    protected static ?string $pluralModelLabel = 'Calendarios de Activ.';

    protected static string | UnitEnum | null $navigationGroup = 'Configuración Basica';
    protected static ?int $navigationSort = 7;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                DateTimePicker::make('start_time')
                    ->required(),
                DateTimePicker::make('end_time')
                    ->required(),
                Select::make('period_id')
                    ->relationship('period', 'name')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('code')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('start_time')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('end_time')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('period.name')
                    ->searchable(),
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
            'index' => ManageScheduleActivities::route('/'),
        ];
    }
}
