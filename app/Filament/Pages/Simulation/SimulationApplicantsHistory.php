<?php

namespace App\Filament\Pages\Simulation;

use App\Models\Simulation\ExamSimulation;
use App\Models\Simulation\SimulationApplicant;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class SimulationApplicantsHistory extends Page implements HasTable
{
    use HasPageShield;
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static ?string $navigationLabel = 'Histórico Postulantes';

    protected static ?string $title = 'Histórico de Postulantes a Simulacros';

    protected static ?string $slug = 'simulation/applicants-history';

    protected static string|UnitEnum|null $navigationGroup = 'Simulacros';

    protected static ?int $navigationSort = 6;

    protected string $view = 'filament.pages.simulation.simulation-applicants-history';

    public function getSubheading(): ?string
    {
        $totalSimulations = ExamSimulation::count();
        $totalApplicants = SimulationApplicant::count();

        return "Total de Simulacros: {$totalSimulations} | Total de Postulantes: {$totalApplicants}";
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                SimulationApplicant::query()
                    ->with(['examSimulation', 'simulationProcess'])
            )
            ->columns([
                TextColumn::make('examSimulation.description')
                    ->label('Simulacro')
                    ->sortable()
                    ->searchable()
                    ->wrap()
                    ->toggleable(),
                TextColumn::make('examSimulation.exam_date')
                    ->label('Fecha Examen')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('code')
                    ->label('Código')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->placeholder('Sin código'),
                TextColumn::make('dni')
                    ->label('DNI')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('full_name')
                    ->label('Nombre Completo')
                    ->searchable(['first_names', 'last_name_father', 'last_name_mother'])
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Correo')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('phone_mobile')
                    ->label('Teléfono')
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('has_photo')
                    ->label('Foto')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->getStateUsing(fn(SimulationApplicant $record): bool => $record->hasPhoto())
                    ->toggleable(),
                TextColumn::make('simulationProcess.payment_at')
                    ->label('Pagado')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Pendiente')
                    ->sortable(),
                TextColumn::make('simulationProcess.data_confirmation_at')
                    ->label('Confirmado')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Pendiente')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Fecha Registro')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('exam_simulation_id')
                    ->label('Simulacro')
                    ->options(
                        ExamSimulation::query()
                            ->orderBy('exam_date', 'desc')
                            ->get()
                            ->pluck('description', 'id')
                    ),
                SelectFilter::make('has_photo')
                    ->label('Estado de Foto')
                    ->options([
                        'with_photo' => 'Con foto',
                        'without_photo' => 'Sin foto',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value']) {
                            'with_photo' => $query->whereNotNull('photo_path')->where('photo_path', '!=', ''),
                            'without_photo' => $query->where(function ($q) {
                                $q->whereNull('photo_path')->orWhere('photo_path', '');
                            }),
                            default => $query,
                        };
                    }),
                SelectFilter::make('payment_status')
                    ->label('Estado de Pago')
                    ->options([
                        'paid' => 'Pagados',
                        'not_paid' => 'Sin pagar',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value']) {
                            'paid' => $query->whereHas('simulationProcess', fn ($q) => $q->whereNotNull('payment_at')),
                            'not_paid' => $query->where(function ($q) {
                                $q->whereDoesntHave('simulationProcess')
                                    ->orWhereHas('simulationProcess', fn ($sq) => $sq->whereNull('payment_at'));
                            }),
                            default => $query,
                        };
                    }),
                SelectFilter::make('confirmation_status')
                    ->label('Estado de Confirmación')
                    ->options([
                        'confirmed' => 'Confirmados',
                        'not_confirmed' => 'Sin confirmar',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value']) {
                            'confirmed' => $query->whereHas('simulationProcess', fn ($q) => $q->whereNotNull('data_confirmation_at')),
                            'not_confirmed' => $query->where(function ($q) {
                                $q->whereDoesntHave('simulationProcess')
                                    ->orWhereHas('simulationProcess', fn ($sq) => $sq->whereNull('data_confirmation_at'));
                            }),
                            default => $query,
                        };
                    }),
                SelectFilter::make('modality')
                    ->label('Modalidad')
                    ->options([
                        'virtual' => 'Virtual',
                        'presencial' => 'Presencial',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value']) {
                            'virtual' => $query->whereHas('examSimulation', fn ($q) => $q->where('is_virtual', true)),
                            'presencial' => $query->whereHas('examSimulation', fn ($q) => $q->where('is_virtual', false)),
                            default => $query,
                        };
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->searchPlaceholder('Buscar por DNI, código, nombre o correo...')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }
}
