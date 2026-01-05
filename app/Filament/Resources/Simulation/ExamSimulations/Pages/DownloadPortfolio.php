<?php

namespace App\Filament\Resources\Simulation\ExamSimulations\Pages;

use App\Exports\PaymentExport;
use App\Filament\Resources\Simulation\ExamSimulations\ExamSimulationResource;
use App\Models\PaymentPortfolio;
use App\Models\Simulation\ExamSimulation;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelType;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DownloadPortfolio extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = ExamSimulationResource::class;

    protected string $view = 'filament.resources.simulation.exam-simulations.pages.download-portfolio';

    public ExamSimulation $record;

    public function getTitle(): string
    {
        return "Descargar Cartera - {$this->record->code}";
    }

    public function getBreadcrumbs(): array
    {
        return [
            ExamSimulationResource::getUrl() => 'Exámenes Simulacros',
            '#' => "Descargar Cartera - {$this->record->code}",
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadPending')
                ->label('Descargar Pendientes')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    return $this->downloadPendingPortfolios();
                })
                ->requiresConfirmation()
                ->modalHeading('Descargar Cartera Pendiente')
                ->modalDescription('Se descargará un archivo Excel con todos los pagos pendientes de envío. Una vez descargado, estos registros se marcarán como enviados.')
                ->modalSubmitActionLabel('Descargar'),

            Action::make('downloadAll')
                ->label('Descargar Todo')
                ->icon('heroicon-o-document-arrow-down')
                ->color('gray')
                ->action(function () {
                    return $this->downloadAllPortfolios();
                }),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PaymentPortfolio::query()
                    ->where('process_type', 'simulation')
                    ->where('process_id', $this->record->id)
            )
            ->columns([
                TextColumn::make('receipt')
                    ->label('Recibo')
                    ->searchable(),
                TextColumn::make('document_number')
                    ->label('DNI')
                    ->searchable(),
                TextColumn::make('client_name')
                    ->label('Cliente')
                    ->searchable()
                    ->limit(30),
                TextColumn::make('amount')
                    ->label('Monto')
                    ->money('PEN'),
                TextColumn::make('service_code')
                    ->label('Servicio')
                    ->badge(),
                IconColumn::make('is_sent')
                    ->label('Enviado')
                    ->boolean(),
                TextColumn::make('sent_at')
                    ->label('Fecha Envío')
                    ->dateTime()
                    ->placeholder('—'),
                TextColumn::make('batch_number')
                    ->label('Lote')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_paid')
                    ->label('Pagado')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ]);
    }

    /**
     * Descargar portfolios pendientes (no enviados)
     */
    public function downloadPendingPortfolios(): ?BinaryFileResponse
    {
        $export = PaymentExport::fromPendingPortfolios('simulation', $this->record->id);

        if ($export->collection()->isEmpty()) {
            Notification::make()
                ->title('No hay registros pendientes')
                ->body('Todos los pagos ya han sido enviados anteriormente.')
                ->warning()
                ->send();

            return null;
        }

        // Marcar como enviados después de generar el archivo
        $export->markAsSent();

        Notification::make()
            ->title('Cartera descargada')
            ->body("Se han marcado {$export->collection()->count()} registros como enviados.")
            ->success()
            ->send();

        return Excel::download($export, 'reporteocef.xls', ExcelType::XLS);
    }

    /**
     * Descargar todos los portfolios (incluyendo ya enviados)
     */
    public function downloadAllPortfolios(): BinaryFileResponse
    {
        $portfolios = PaymentPortfolio::where('process_type', 'simulation')
            ->where('process_id', $this->record->id)
            ->with(['tariff', 'payable'])
            ->get();

        if ($portfolios->isEmpty()) {
            Notification::make()
                ->title('No hay registros')
                ->body('No se encontraron pagos para este simulacro.')
                ->warning()
                ->send();

            return back();
        }

        $data = $portfolios->map(function ($portfolio) {
            // Obtener datos del postulante
            $applicant = $portfolio->payable;
            
            return [
                'BOL_FAC' => '2', // 2 = Boleta
                'DNI_RUC' => $portfolio->document_number ?? '',
                'NOMBRES_RAZ_SOCIAL' => $applicant?->first_names ?? '',
                'PATERNO' => $applicant?->last_name_father ?? '',
                'MATERNO' => $applicant?->last_name_mother ?? '',
                'DIRECCION' => '',
                'CORREO' => $portfolio->client_email ?? '',
                'DESCRIPCION' => $portfolio->description ?? '',
                'PARTIDA' => $portfolio->tariff?->item ?? '',
                'PROYECTO' => $portfolio->tariff?->project ?? '',
                'MONTO' => (int) $portfolio->amount,
            ];
        });

        $export = new PaymentExport($data);

        return Excel::download($export, 'reporteocef.xls', ExcelType::XLS);
    }

    /**
     * Obtener estadísticas para mostrar en la vista
     */
    public function getStats(): array
    {
        $query = PaymentPortfolio::where('process_type', 'simulation')
            ->where('process_id', $this->record->id);

        return [
            'total' => (clone $query)->count(),
            'pending' => (clone $query)->notSent()->count(),
            'sent' => (clone $query)->sent()->count(),
            'paid' => (clone $query)->paid()->count(),
            'total_amount' => (clone $query)->sum('amount'),
            'pending_amount' => (clone $query)->notSent()->sum('amount'),
        ];
    }
}
