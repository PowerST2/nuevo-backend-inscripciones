<?php

namespace App\Filament\Pages\Simulation;

use App\Models\Simulation\ExamSimulation;
use App\Models\Simulation\SimulationApplicant;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\Action;
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

class ActiveSimulationApplicants extends Page implements HasTable
{
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
            $withoutPhoto = $total - $withPhoto;
            $modalityText = $activeSimulation->is_virtual ? 'Virtual' : 'Presencial';

            return "Modalidad: {$modalityText} | Total: {$total} | Con foto: {$withPhoto} | Sin foto: {$withoutPhoto}";
        }

        return 'Configure un simulacro activo para ver los postulantes';
    }

    public function getActiveSimulation(): ?ExamSimulation
    {
        $today = Carbon::today()->toDateString();

        return ExamSimulation::where('active', true)
            ->where('exam_date_start', '<=', $today)
            ->where('exam_date_end', '>=', $today)
            ->first();
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
                TextColumn::make('simulationProcess.registration_at')
                    ->label('Inscrito')
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
            ])
            ->actions([
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
                    ->action(fn(SimulationApplicant $record) => $record->delete()),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->poll('30s');
    }
}
