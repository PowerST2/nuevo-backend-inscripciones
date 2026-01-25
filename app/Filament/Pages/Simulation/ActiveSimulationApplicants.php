<?php

namespace App\Filament\Pages\Simulation;

use App\Exports\SimulationApplicantsExport;
use App\Models\Simulation\ExamSimulation;
use App\Models\Simulation\SimulationApplicant;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Facades\Filament;
use UnitEnum;

class ActiveSimulationApplicants extends Page implements HasTable
{
    use HasPageShield;
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $navigationLabel = 'Postulantes Simulacro';

    protected static ?string $title = 'Postulantes del Simulacro';

    protected static ?string $slug = 'simulation/active-applicants';

    protected static string|UnitEnum|null $navigationGroup = 'Simulacros';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.simulation.active-simulation-applicants';

    public function getTitle(): string
    {
        $activeSimulation = $this->getActiveSimulation();

        if ($activeSimulation) {
            return "Postulantes: {$activeSimulation->description}";
        }

        return 'No hay simulacro activo';
    }

    public function getSubheading(): ?string
    {
        $activeSimulation = $this->getActiveSimulation();

        if ($activeSimulation) {
            $total = SimulationApplicant::where('exam_simulation_id', $activeSimulation->id)->count();
            $withPhoto = SimulationApplicant::where('exam_simulation_id', $activeSimulation->id)
                ->whereNotNull('photo_path')
                ->where('photo_path', '!=', '')
                ->count();
            $paid = SimulationApplicant::where('exam_simulation_id', $activeSimulation->id)
                ->whereHas('simulationProcess', fn ($q) => $q->whereNotNull('payment_at'))
                ->count();
            $confirmed = SimulationApplicant::where('exam_simulation_id', $activeSimulation->id)
                ->whereHas('simulationProcess', fn ($q) => $q->whereNotNull('data_confirmation_at'))
                ->count();
            $modalityText = $activeSimulation->is_virtual ? 'Virtual' : 'Presencial';

            return "Modalidad: {$modalityText} | Total: {$total} | Con foto: {$withPhoto} | Pagados: {$paid} | Confirmados: {$confirmed}";
        }

        return 'Configure un simulacro activo para ver los postulantes';
    }

    public function getActiveSimulation(): ?ExamSimulation
    {
        $today = Carbon::today()->toDateString();

        return ExamSimulation::where('active', true)
            ->first();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('confirm_all_data')
                ->label('Confirmar Datos Masivos')
                ->icon('heroicon-o-check-circle')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Confirmar Datos Masivos')
                ->modalDescription('Se confirmarán los datos de todos los postulantes que aún no estén confirmados. Esta acción establecerá la fecha y hora actual.')
                ->modalSubmitActionLabel('Confirmar')
                ->action(function () {
                    $this->confirmAllData();
                })
                ->visible(fn () => $this->getActiveSimulation() !== null && $this->canGenerateCode()),
            Action::make('generate_all_codes')
                ->label('Generar Códigos Masivos')
                ->icon('heroicon-o-key')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Generar Códigos Masivos')
                ->modalDescription('Se generarán códigos para todos los postulantes confirmados que no tengan código. Esta acción no se puede deshacer.')
                ->modalSubmitActionLabel('Generar')
                ->action(function () {
                    $this->generateAllCodes();
                })
                ->visible(fn () => $this->getActiveSimulation() !== null && $this->canGenerateCode()),
            Action::make('export')
                ->label('Exportar Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    return $this->exportToExcel();
                })
                ->visible(fn () => $this->getActiveSimulation() !== null),
        ];
    }

    /**
     * Confirmar datos masivos solo para postulantes que ya pagaron (payment_at no sea null)
     */
    public function confirmAllData(): void
    {
        $activeSimulation = $this->getActiveSimulation();

        if (!$activeSimulation) {
            Notification::make()
                ->title('Error')
                ->body('No hay simulacro activo.')
                ->danger()
                ->send();
            return;
        }

        // Obtener postulantes que pagaron pero aún no tienen data_confirmation_at
        $applicants = SimulationApplicant::where('exam_simulation_id', $activeSimulation->id)
            ->whereHas('simulationProcess', fn ($q) => $q->whereNotNull('payment_at')->whereNull('data_confirmation_at'))
            ->get();

        if ($applicants->isEmpty()) {
            Notification::make()
                ->title('Sin cambios')
                ->body('No hay postulantes pagados sin confirmar.')
                ->warning()
                ->send();
            return;
        }

        $count = 0;
        $now = now();

        foreach ($applicants as $applicant) {
            if ($applicant->simulationProcess && is_null($applicant->simulationProcess->data_confirmation_at)) {
                $applicant->simulationProcess->update([
                    'data_confirmation_at' => $now,
                ]);
                $count++;
            }
        }

        Notification::make()
            ->title('Datos confirmados')
            ->body("Se confirmaron {$count} postulantes a las " . $now->format('d/m/Y H:i:s') . '.')
            ->success()
            ->send();
    }

    /**
     * Generar códigos de forma masiva para todos los postulantes confirmados sin código
     */
    public function generateAllCodes(): void
    {
        $activeSimulation = $this->getActiveSimulation();

        if (!$activeSimulation) {
            Notification::make()
                ->title('Error')
                ->body('No hay simulacro activo.')
                ->danger()
                ->send();
            return;
        }

        // Obtener todos los postulantes confirmados sin código
        $applicants = SimulationApplicant::where('exam_simulation_id', $activeSimulation->id)
            ->whereHas('simulationProcess', fn ($q) => $q->whereNotNull('data_confirmation_at'))
            ->where(function ($q) {
                $q->whereNull('code')->orWhere('code', '');
            })
            ->get();

        if ($applicants->isEmpty()) {
            Notification::make()
                ->title('Sin cambios')
                ->body('No hay postulantes para generar códigos.')
                ->warning()
                ->send();
            return;
        }

        $count = 0;
        foreach ($applicants as $applicant) {
            if (empty($applicant->code)) {
                $sequence = $applicant->getKey();
                $applicant->code = SimulationApplicant::generateRegistrationCode((int) $sequence);
                $applicant->save();
                $count++;
            }
        }

        Notification::make()
            ->title('Códigos generados')
            ->body("Se generaron {$count} códigos de registro.")
            ->success()
            ->send();
    }

    public function exportToExcel()
    {
        $activeSimulation = $this->getActiveSimulation();

        if (!$activeSimulation) {
            Notification::make()
                ->title('Error')
                ->body('No hay simulacro activo.')
                ->danger()
                ->send();
            return null;
        }

        $query = SimulationApplicant::where('exam_simulation_id', $activeSimulation->id)
            ->with('simulationProcess');

        // Aplicar filtros activos
        $filters = $this->tableFilters;
        
        if (!empty($filters['has_photo']['value'])) {
            $query = match ($filters['has_photo']['value']) {
                'with_photo' => $query->whereNotNull('photo_path')->where('photo_path', '!=', ''),
                'without_photo' => $query->where(function ($q) {
                    $q->whereNull('photo_path')->orWhere('photo_path', '');
                }),
                default => $query,
            };
        }

        if (!empty($filters['payment_status']['value'])) {
            $query = match ($filters['payment_status']['value']) {
                'paid' => $query->whereHas('simulationProcess', fn ($q) => $q->whereNotNull('payment_at')),
                'not_paid' => $query->where(function ($q) {
                    $q->whereDoesntHave('simulationProcess')
                        ->orWhereHas('simulationProcess', fn ($sq) => $sq->whereNull('payment_at'));
                }),
                default => $query,
            };
        }

        if (!empty($filters['confirmation_status']['value'])) {
            $query = match ($filters['confirmation_status']['value']) {
                'confirmed' => $query->whereHas('simulationProcess', fn ($q) => $q->whereNotNull('data_confirmation_at')),
                'not_confirmed' => $query->where(function ($q) {
                    $q->whereDoesntHave('simulationProcess')
                        ->orWhereHas('simulationProcess', fn ($sq) => $sq->whereNull('data_confirmation_at'));
                }),
                default => $query,
            };
        }

        $applicants = $query->orderBy('created_at', 'desc')->get();

        if ($applicants->isEmpty()) {
            Notification::make()
                ->title('Sin datos')
                ->body('No hay postulantes para exportar con los filtros seleccionados.')
                ->warning()
                ->send();
            return null;
        }

        $filename = 'postulantes_' . $activeSimulation->code . '_' . now()->format('Ymd_His') . '.xlsx';

        Notification::make()
            ->title('Exportación iniciada')
            ->body("Se exportarán {$applicants->count()} registros.")
            ->success()
            ->send();

        return Excel::download(
            new SimulationApplicantsExport($applicants, $activeSimulation->code),
            $filename
        );
    }

    public function table(Table $table): Table
    {
        $activeSimulation = $this->getActiveSimulation();

        return $table
            ->query(
                SimulationApplicant::query()
                    ->when($activeSimulation, function (Builder $query) use ($activeSimulation) {
                        $query->where('exam_simulation_id', $activeSimulation->id);
                    })
                    ->when(!$activeSimulation, function (Builder $query) {
                        $query->whereRaw('1 = 0');
                    })
                    ->with(['examSimulation', 'simulationProcess'])
            )
            ->columns([
                TextColumn::make('code')
                    ->label('Código')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
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
                    ->toggleable(),
                IconColumn::make('has_photo')
                    ->label('Foto')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->getStateUsing(fn(SimulationApplicant $record): bool => $record->hasPhoto()),
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
                    ->label('Registro')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
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
            ])
            ->actions([
                Action::make('generate_code')
                    ->label('')
                    ->icon('heroicon-o-key')
                    ->color('success')
                    ->tooltip('Generar código')
                    ->requiresConfirmation()
                    ->modalHeading('Generar Código')
                    ->modalDescription(fn(SimulationApplicant $record): string => "Generar código de inscripción para {$record->full_name}")
                    ->modalSubmitActionLabel('Generar')
                    ->action(function (SimulationApplicant $record) {
                        if (empty($record->code)) {
                            $sequence = $record->getKey();
                            $record->code = SimulationApplicant::generateRegistrationCode((int) $sequence);
                            $record->save();
                            Notification::make()
                                ->title('Código generado')
                                ->body("Código: {$record->code}")
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Ya tiene código')
                                ->body("Código existente: {$record->code}")
                                ->warning()
                                ->send();
                        }
                    })
                    ->visible(fn(SimulationApplicant $record): bool => 
                        $this->canGenerateCode() &&
                        $record->simulationProcess?->data_confirmation_at !== null && 
                        empty($record->code)
                    ),
                Action::make('view_photo')
                    ->label('')
                    ->icon('heroicon-o-photo')
                    ->color('info')
                    ->modalHeading(fn(SimulationApplicant $record): string => "Foto de {$record->full_name}")
                    ->modalContent(fn(SimulationApplicant $record) => view('filament.pages.simulation.partials.view-photo', [
                        'applicant' => $record,
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar')
                    ->visible(fn(SimulationApplicant $record): bool => $record->hasPhoto()),
                Action::make('view_details')
                    ->label('')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalHeading(fn(SimulationApplicant $record): string => "Detalles de {$record->full_name}")
                    ->modalContent(fn(SimulationApplicant $record) => view('filament.pages.simulation.partials.view-applicant-details', [
                        'applicant' => $record,
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar'),
                Action::make('delete')
                    ->label('')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Eliminar Postulante')
                    ->modalDescription(fn(SimulationApplicant $record): string => "¿Está seguro que desea eliminar a {$record->full_name} ({$record->dni})? Esta acción no se puede deshacer.")
                    ->modalSubmitActionLabel('Sí, eliminar')
                    ->action(fn(SimulationApplicant $record) => $record->delete())
                    ->visible(fn(): bool => $this->canDeleteApplicants()),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->poll('30s');
    }

    protected function canDeleteApplicants(): bool
    {
        $user = Filament::auth()?->user();

        if (!$user) {
            return false;
        }

        // Super admin siempre puede eliminar
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Rol de sistemas puede eliminar
        if ($user->hasRole('sistemas')) {
            return true;
        }


        return false;
    }

    protected function canGenerateCode(): bool
    {
        $user = Filament::auth()?->user();

        if (!$user) {
            return false;
        }

        // Super admin siempre puede generar códigos
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Rol de sistemas puede generar códigos
        if ($user->hasRole('sistemas')) {
            return true;
        }

        return false;
    }
}
