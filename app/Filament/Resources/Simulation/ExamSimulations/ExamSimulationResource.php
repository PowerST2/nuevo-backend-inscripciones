<?php

namespace App\Filament\Resources\Simulation\ExamSimulations;

use App\Filament\Resources\Simulation\ExamSimulations\Pages\ManageExamSimulations;
use App\Filament\Resources\Simulation\ExamSimulations\Pages\DownloadPortfolio;
use App\Models\Simulation\ExamSimulation;
use App\Models\Tariff;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
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

class ExamSimulationResource extends Resource
{
    protected static ?string $model = ExamSimulation::class;
    protected static ?string $modelLabel = 'Examen Simulacro';
    protected static ?string $pluralModelLabel = 'Exámenes Simulacros';

    protected static string | UnitEnum | null $navigationGroup = 'Simulacros';
    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $recordTitleAttribute = 'code';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Código')
                    ->required(),
                Textarea::make('description')
                    ->label('Descripción')
                    ->columnSpanFull(),
                Select::make('tariff_id')
                    ->label('Tarifario')
                    ->relationship('tariff', 'description')
                    ->options(
                        Tariff::active()
                            ->forSimulation()
                            ->ordered()
                            ->get()
                            ->mapWithKeys(fn ($tariff) => [
                                $tariff->id => "{$tariff->code} - {$tariff->description} (S/ {$tariff->amount})"
                            ])
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->helperText('Seleccione el tarifario que se cobrará por este simulacro'),
                DatePicker::make('exam_date_start')
                    ->label('Fecha inicio')
                    ->required(),
                DatePicker::make('exam_date_end')
                    ->label('Fecha fin')
                    ->required(),
                DatePicker::make('exam_date')
                    ->label('Fecha del examen')
                    ->helperText('Fecha principal del examen (opcional si ya usa rango).'),
                Toggle::make('active')
                    ->label('Activo')
                    ->default(true),
                Toggle::make('is_virtual')
                    ->label('¿Es Virtual?')
                    ->helperText('Activado = Virtual (sin foto), Desactivado = Presencial (requiere foto)')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('code')
            ->columns([
                TextColumn::make('code')
                    ->label('Código')
                    ->searchable(),
                TextColumn::make('description')
                    ->label('Descripción')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('tariff.code')
                    ->label('Servicio')
                    ->badge()
                    ->searchable(),
                TextColumn::make('tariff.amount')
                    ->label('Monto')
                    ->money('PEN'),
                TextColumn::make('exam_date_start')
                    ->label('Fecha inicio')
                    ->date()
                    ->sortable(),
                TextColumn::make('exam_date_end')
                    ->label('Fecha fin')
                    ->date()
                    ->sortable(),
                TextColumn::make('exam_date')
                    ->label('Fecha examen')
                    ->date()
                    ->sortable(),
                IconColumn::make('active')
                    ->label('Activo')
                    ->boolean(),
                IconColumn::make('is_virtual')
                    ->label('Modalidad')
                    ->boolean()
                    ->trueIcon('heroicon-o-computer-desktop')
                    ->falseIcon('heroicon-o-building-library')
                    ->trueColor('info')
                    ->falseColor('warning')
                    ->tooltip(fn ($record) => $record->is_virtual ? 'Virtual' : 'Presencial'),
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
                Action::make('downloadPortfolio')
                    ->label('Descargar Cartera')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn (ExamSimulation $record): string => route('filament.admin.resources.simulation.exam-simulations.download-portfolio', ['record' => $record])),
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
                'index' => ManageExamSimulations::route('/'),
                'download-portfolio' => DownloadPortfolio::route('/{record}/download-portfolio'),
            ];
        }

}
