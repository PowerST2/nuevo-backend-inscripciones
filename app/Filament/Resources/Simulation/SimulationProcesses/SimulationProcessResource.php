<?php

namespace App\Filament\Resources\Simulation\SimulationProcesses;

use App\Filament\Resources\Simulation\SimulationProcesses\Pages\ManageSimulationProcesses;
use App\Models\Simulation\SimulationProcess;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use UnitEnum;

class SimulationProcessResource extends Resource
{
    protected static ?string $model = SimulationProcess::class;
    protected static ?string $modelLabel = 'Proceso de Simulacro';
    protected static ?string $pluralModelLabel = 'Procesos de Simulacro';

    protected static string | UnitEnum | null $navigationGroup = 'Simulacros';
    protected static ?int $navigationSort = 3;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Aplicante')
                    ->schema([
                        Placeholder::make('applicant_info')
                            ->label('Aplicante')
                            ->content(fn ($record) => $record?->simulationApplicant 
                                ? "{$record->simulationApplicant->dni} - {$record->simulationApplicant->full_name}"
                                : 'N/A'),
                        Placeholder::make('applicant_code')
                            ->label('Código de Inscripción')
                            ->content(fn ($record) => $record?->simulationApplicant?->code ?? 'Pendiente'),
                        Placeholder::make('applicant_email')
                            ->label('Correo')
                            ->content(fn ($record) => $record?->simulationApplicant?->email ?? 'N/A'),
                    ])
                    ->columns(3),

                Section::make('Estado del Proceso')
                    ->schema([
                        DateTimePicker::make('pre_registration_at')
                            ->label('Pre-inscripción')
                            ->disabled()
                            ->displayFormat('d/m/Y H:i'),
                        DateTimePicker::make('payment_at')
                            ->label('Pago')
                            ->displayFormat('d/m/Y H:i')
                            ->helperText('Marcar cuando el pago haya sido verificado'),
                        DateTimePicker::make('data_confirmation_at')
                            ->label('Confirmación de Datos')
                            ->disabled()
                            ->displayFormat('d/m/Y H:i'),
                        DateTimePicker::make('photo_at')
                            ->label('Foto Cargada')
                            ->disabled()
                            ->displayFormat('d/m/Y H:i'),    
                        DateTimePicker::make('photo_reviewed_at')
                            ->label('Foto Verificada')
                            ->disabled()
                            ->displayFormat('d/m/Y H:i')
                            ->helperText('Fecha y hora en que la foto fue aprobada'),
                        DateTimePicker::make('registration_at')
                            ->label('Inscripción')
                            ->disabled()
                            ->displayFormat('d/m/Y H:i'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('simulationApplicant.code')
                    ->label('Código')
                    ->searchable()
                    ->sortable()
                    ->default('Pendiente')
                    ->badge()
                    ->color(fn ($state) => $state === 'Pendiente' ? 'warning' : 'success'),
                TextColumn::make('simulationApplicant.dni')
                    ->label('DNI')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('simulationApplicant.full_name')
                    ->label('Nombre Completo')
                    ->searchable()
                    ->limit(30),
                TextColumn::make('simulationApplicant.email')
                    ->label('Correo')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('pre_registration_at')
                    ->label('Pre-inscripción')
                    ->boolean()
                    ->getStateUsing(fn ($record) => !is_null($record->pre_registration_at))
                    ->trueIcon(Heroicon::OutlinedCheckCircle)
                    ->falseIcon(Heroicon::OutlinedXCircle)
                    ->trueColor('success')
                    ->falseColor('danger'),
                IconColumn::make('photo_at')
                    ->label('Foto')
                    ->boolean()
                    ->getStateUsing(fn ($record) => !is_null($record->photo_at))
                    ->trueIcon(Heroicon::OutlinedCheckCircle)
                    ->falseIcon(Heroicon::OutlinedXCircle)
                    ->trueColor('success')
                    ->falseColor('danger'),
                IconColumn::make('photo_reviewed_at')
                    ->label('Foto Verificada')
                    ->boolean()
                    ->getStateUsing(fn ($record) => !is_null($record->photo_reviewed_at))
                    ->trueIcon(Heroicon::OutlinedCheckCircle)
                    ->falseIcon(Heroicon::OutlinedXCircle)
                    ->trueColor('success')
                    ->falseColor('danger'),
                IconColumn::make('payment_at')
                    ->label('Pago')
                    ->boolean()
                    ->getStateUsing(fn ($record) => !is_null($record->payment_at))
                    ->trueIcon(Heroicon::OutlinedCheckCircle)
                    ->falseIcon(Heroicon::OutlinedXCircle)
                    ->trueColor('success')
                    ->falseColor('danger'),
                IconColumn::make('data_confirmation_at')
                    ->label('Confirmación')
                    ->boolean()
                    ->getStateUsing(fn ($record) => !is_null($record->data_confirmation_at))
                    ->trueIcon(Heroicon::OutlinedCheckCircle)
                    ->falseIcon(Heroicon::OutlinedXCircle)
                    ->trueColor('success')
                    ->falseColor('danger'),
                IconColumn::make('data_confirmation_at')
                    ->label('Confirmación')
                    ->boolean()
                    ->getStateUsing(fn ($record) => !is_null($record->data_confirmation_at))
                    ->trueIcon(Heroicon::OutlinedCheckCircle)
                    ->falseIcon(Heroicon::OutlinedXCircle)
                    ->trueColor('success')
                    ->falseColor('danger'),
                TextColumn::make('simulationApplicant.examSimulation.code')
                    ->label('Simulacro')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true), 
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                TernaryFilter::make('payment_status')
                    ->label('Estado de Pago')
                    ->placeholder('Todos')
                    ->trueLabel('Pagado')
                    ->falseLabel('Sin pagar')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('payment_at'),
                        false: fn ($query) => $query->whereNull('payment_at'),
                    ),
                TernaryFilter::make('confirmation_status')
                    ->label('Estado de Confirmación')
                    ->placeholder('Todos')
                    ->trueLabel('Confirmado')
                    ->falseLabel('Sin confirmar')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('data_confirmation_at'),
                        false: fn ($query) => $query->whereNull('data_confirmation_at'),
                    ),
            ])
            ->recordActions([
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
            'index' => ManageSimulationProcesses::route('/'),
        ];
    }
}
