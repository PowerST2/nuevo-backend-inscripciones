<?php

namespace App\Filament\Resources\Simulation\SimulationApplicants;

use App\Filament\Resources\Simulation\SimulationApplicants\Pages\ManageSimulationApplicants;
use App\Models\Simulation\ExamSimulation;
use App\Models\Simulation\SimulationApplicant;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class SimulationApplicantResource extends Resource
{
    protected static ?string $model = SimulationApplicant::class;
    protected static ?string $modelLabel = 'Aplicante al Simulacro';
    protected static ?string $pluralModelLabel = 'Aplicantes al Simulacro';

    protected static bool $shouldRegisterNavigation = false;

    protected static string | UnitEnum | null $navigationGroup = 'Simulacros';
    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Código')
                    ->disabled()
                    ->dehydrated(false)
                    ->visibleOn('edit'),
                TextInput::make('dni')
                    ->required()
                    ->maxLength(8)
                    ->numeric(),
                TextInput::make('last_name_father')
                    ->required(),
                TextInput::make('last_name_mother')
                    ->required(),
                TextInput::make('first_names')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('phone_mobile')
                    ->tel()
                    ->maxLength(9),
                TextInput::make('phone_other')
                    ->tel()
                    ->maxLength(9),
                Select::make('exam_simulation_id')
                    ->relationship('examSimulation', 'code')
                    ->required()
                    ->searchable()
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('code')
                    ->label('Código')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('dni')
                    ->searchable(),
                TextColumn::make('last_name_father')
                    ->searchable(),
                TextColumn::make('last_name_mother')
                    ->searchable(),
                TextColumn::make('first_names')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('phone_mobile')
                    ->searchable(),
                TextColumn::make('phone_other')
                    ->searchable(),
                TextColumn::make('examSimulation.code')
                    ->label('Simulacro')
                    ->sortable(),
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
                SelectFilter::make('exam_simulation_id')
                    ->label('Simulacro')
                    ->options(fn () => ExamSimulation::orderBy('created_at', 'desc')
                        ->pluck('description', 'id')
                        ->toArray())
                    ->default(fn () => ExamSimulation::where('active', true)->first()?->id)
                    ->selectablePlaceholder(false),
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
            'index' => ManageSimulationApplicants::route('/'),
        ];
    }
}
