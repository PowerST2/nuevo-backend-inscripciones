<?php

namespace App\Filament\Pages\Simulation;

use App\Exports\PaymentExport;
use App\Models\PaymentPortfolio;
use App\Models\Simulation\ExamSimulation;
use App\Models\Simulation\SimulationApplicant;
use BackedEnum;
use Carbon\Carbon;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelType;
use Symfony\Component\HttpFoundation\StreamedResponse;
use UnitEnum;

class UploadBcpPayments extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $navigationLabel = 'Gestión de Pagos';

    protected static ?string $title = 'Gestión de Pagos';

    protected static ?string $slug = 'simulation/payment-management';

    protected static string|UnitEnum|null $navigationGroup = 'Simulacros';

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.simulation.upload-bcp-payments';

    public ?array $data = [];

    public ?array $processResults = null;

    public string $activeTab = 'upload';

    public function mount(): void
    {
        $this->form->fill();
    }

    public function getActiveSimulation(): ?ExamSimulation
    {
        $today = Carbon::today()->toDateString();

        return ExamSimulation::where('active', true)
            ->where('exam_date_start', '<=', $today)
            ->where('exam_date_end', '>=', $today)
            ->first();
    }

    public function getSubheading(): ?string
    {
        $activeSimulation = $this->getActiveSimulation();

        if ($activeSimulation) {
            return "Simulacro activo: {$activeSimulation->description} ({$activeSimulation->code})";
        }

        return 'No hay simulacro activo';
    }

    /**
     * Obtener estadísticas de cartera para el simulacro activo
     */
    public function getPortfolioStats(): array
    {
        $simulation = $this->getActiveSimulation();

        if (!$simulation) {
            return [
                'total' => 0,
                'pending' => 0,
                'sent' => 0,
                'paid' => 0,
                'total_amount' => 0,
                'pending_amount' => 0,
            ];
        }

        $query = PaymentPortfolio::where('process_type', 'simulation')
            ->where('process_id', $simulation->id);

        return [
            'total' => (clone $query)->count(),
            'pending' => (clone $query)->notSent()->count(),
            'sent' => (clone $query)->sent()->count(),
            'paid' => (clone $query)->paid()->count(),
            'total_amount' => (clone $query)->sum('amount'),
            'pending_amount' => (clone $query)->notSent()->sum('amount'),
        ];
    }

    /**
     * Tabla de cartera de pagos
     */
    public function table(Table $table): Table
    {
        $simulation = $this->getActiveSimulation();

        return $table
            ->query(
                PaymentPortfolio::query()
                    ->where('process_type', 'simulation')
                    ->where('process_id', $simulation?->id ?? 0)
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
                IconColumn::make('is_paid')
                    ->label('Pagado')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc');
    }

    /**
     * Descargar portfolios pendientes (no enviados)
     */
    public function downloadPendingPortfolios(): ?StreamedResponse
    {
        $simulation = $this->getActiveSimulation();

        if (!$simulation) {
            Notification::make()
                ->title('Error')
                ->body('No hay simulacro activo.')
                ->danger()
                ->send();
            return null;
        }

        $export = PaymentExport::fromPendingPortfolios('simulation', $simulation->id);

        if ($export->collection()->isEmpty()) {
            Notification::make()
                ->title('No hay registros pendientes')
                ->body('Todos los pagos ya han sido enviados anteriormente.')
                ->warning()
                ->send();

            return null;
        }

        $count = $export->collection()->count();

        // Marcar como enviados después de generar el archivo
        $export->markAsSent();

        Notification::make()
            ->title('Cartera descargada')
            ->body("Se han marcado {$count} registros como enviados.")
            ->success()
            ->send();

        return response()->streamDownload(function () use ($export) {
            echo Excel::raw($export, ExcelType::XLS);
        }, 'reporteocef.xls', [
            'Content-Type' => 'application/vnd.ms-excel',
        ]);
    }

    /**
     * Descargar todos los portfolios (incluyendo ya enviados)
     */
    public function downloadAllPortfolios(): ?StreamedResponse
    {
        $simulation = $this->getActiveSimulation();

        if (!$simulation) {
            Notification::make()
                ->title('Error')
                ->body('No hay simulacro activo.')
                ->danger()
                ->send();
            return null;
        }

        $portfolios = PaymentPortfolio::where('process_type', 'simulation')
            ->where('process_id', $simulation->id)
            ->with(['tariff', 'payable'])
            ->get();

        if ($portfolios->isEmpty()) {
            Notification::make()
                ->title('No hay registros')
                ->body('No se encontraron pagos para este simulacro.')
                ->warning()
                ->send();

            return null;
        }

        $data = $portfolios->map(function ($portfolio) {
            $applicant = $portfolio->payable;

            return [
                'BOL_FAC' => '2',
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

        return response()->streamDownload(function () use ($export) {
            echo Excel::raw($export, ExcelType::XLS);
        }, 'reporteocef.xls', [
            'Content-Type' => 'application/vnd.ms-excel',
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                FileUpload::make('csv_file')
                    ->label('Archivo CSV del BCP')
                    ->acceptedFileTypes(['text/csv', 'application/vnd.ms-excel', 'text/plain', '.csv'])
                    ->maxSize(5120)
                    ->required()
                    ->helperText('Sube el archivo CSV que contiene los datos de pagos del BCP.')
                    ->disk('local')
                    ->directory('bcp-uploads')
                    ->visibility('private'),
            ])
            ->statePath('data');
    }

    public function processFile(): void
    {
        $data = $this->form->getState();
        
        $activeSimulation = $this->getActiveSimulation();

        if (!$activeSimulation) {
            Notification::make()
                ->title('Error')
                ->body('No hay un simulacro activo para procesar pagos.')
                ->danger()
                ->send();
            return;
        }

        $csvFile = $data['csv_file'] ?? null;

        if (empty($csvFile)) {
            Notification::make()
                ->title('Error')
                ->body('No se ha subido ningún archivo.')
                ->danger()
                ->send();
            return;
        }

        // Obtener el path del archivo (puede ser array o string)
        $filePath = is_array($csvFile) ? ($csvFile[0] ?? null) : $csvFile;

        if (!$filePath) {
            Notification::make()
                ->title('Error')
                ->body('No se pudo obtener el archivo.')
                ->danger()
                ->send();
            return;
        }

        $fullPath = Storage::disk('local')->path($filePath);

        if (!file_exists($fullPath)) {
            Notification::make()
                ->title('Error')
                ->body('El archivo no se encontró en el servidor.')
                ->danger()
                ->send();
            return;
        }

        $results = $this->processCsvFile($fullPath, $activeSimulation);

        $this->processResults = $results;

        // Eliminar archivo después de procesar
        Storage::disk('local')->delete($filePath);

        // Limpiar formulario
        $this->form->fill();

        if ($results['processed'] > 0) {
            Notification::make()
                ->title('Pagos procesados')
                ->body("Se procesaron {$results['processed']} pagos correctamente. {$results['already_paid']} ya estaban pagados. {$results['not_found']} no encontrados.")
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Sin pagos nuevos')
                ->body("No se encontraron nuevos pagos para procesar. {$results['already_paid']} ya estaban pagados. {$results['not_found']} no encontrados.")
                ->warning()
                ->send();
        }
    }

    protected function processCsvFile(string $filePath, ExamSimulation $simulation): array
    {
        $results = [
            'total' => 0,
            'processed' => 0,
            'already_paid' => 0,
            'not_found' => 0,
            'errors' => 0,
            'details' => [],
        ];

        $handle = fopen($filePath, 'r');

        if (!$handle) {
            return $results;
        }

        // Leer encabezados
        $headers = fgetcsv($handle);

        if (!$headers) {
            fclose($handle);
            return $results;
        }

        // Normalizar encabezados (quitar BOM y espacios)
        $headers = array_map(function ($header) {
            return strtolower(trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $header)));
        }, $headers);

        // Buscar índices de columnas necesarias
        $codigoIndex = array_search('codigo', $headers);
        $fechaIndex = array_search('fecha', $headers);
        $montoIndex = array_search('monto', $headers);
        $operacionIndex = array_search('operacion', $headers);
        $reciboIndex = array_search('recibo', $headers);

        if ($codigoIndex === false) {
            fclose($handle);
            Log::error('CSV BCP: No se encontró la columna "codigo"');
            return $results;
        }

        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle)) !== false) {
                $results['total']++;

                $dni = trim($row[$codigoIndex] ?? '');
                $fechaPago = $fechaIndex !== false ? trim($row[$fechaIndex] ?? '') : null;
                $monto = $montoIndex !== false ? trim($row[$montoIndex] ?? '') : null;
                $operacion = $operacionIndex !== false ? trim($row[$operacionIndex] ?? '') : null;
                $recibo = $reciboIndex !== false ? trim($row[$reciboIndex] ?? '') : null;

                if (empty($dni)) {
                    $results['errors']++;
                    continue;
                }

                // Buscar postulante por DNI en el simulacro activo
                $applicant = SimulationApplicant::where('exam_simulation_id', $simulation->id)
                    ->where('dni', $dni)
                    ->with('simulationProcess')
                    ->first();

                if (!$applicant) {
                    $results['not_found']++;
                    $results['details'][] = [
                        'dni' => $dni,
                        'status' => 'not_found',
                        'message' => 'Postulante no encontrado',
                    ];
                    continue;
                }

                if (!$applicant->simulationProcess) {
                    $results['errors']++;
                    $results['details'][] = [
                        'dni' => $dni,
                        'status' => 'error',
                        'message' => 'Sin proceso de simulacro',
                    ];
                    continue;
                }

                // Verificar si ya pagó
                if ($applicant->simulationProcess->hasPaid()) {
                    $results['already_paid']++;
                    $results['details'][] = [
                        'dni' => $dni,
                        'name' => $applicant->full_name,
                        'status' => 'already_paid',
                        'message' => 'Ya tenía pago registrado',
                    ];
                    continue;
                }

                // Registrar pago - Usar la fecha/hora actual de Lima
                $paymentDate = now('America/Lima');

                $applicant->simulationProcess->payment_at = $paymentDate;
                $applicant->simulationProcess->save();

                $results['processed']++;
                $results['details'][] = [
                    'dni' => $dni,
                    'name' => $applicant->full_name,
                    'status' => 'processed',
                    'message' => 'Pago registrado',
                    'date' => $paymentDate->format('d/m/Y H:i'),
                    'csv_date' => $fechaPago,
                ];

                Log::info("Pago BCP registrado: DNI {$dni}, Operación: {$operacion}, Monto: {$monto}");
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error procesando CSV BCP: ' . $e->getMessage());
            $results['errors']++;
        }

        fclose($handle);

        return $results;
    }
}
