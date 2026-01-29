<?php

namespace App\Exports;

use App\Models\PaymentPortfolio;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeWriting;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PaymentExport implements WithEvents, WithTitle, WithProperties
{
    /**
     * @var Collection
     */
    private Collection $data;

    /**
     * @var string|null Número de lote para marcar como enviados
     */
    private ?string $batchNumber;

    /**
     * @var array IDs de portfolios a marcar como enviados
     */
    private array $portfolioIds = [];

    /**
     * Columnas que deben forzarse como texto (con triángulo verde)
     */
    private array $textColumns = ['A', 'B', 'I', 'J']; // BOL_FAC, DNI_RUC, PARTIDA, PROYECTO

    /**
     * Constructor
     * 
     * @param Collection $data Colección de datos formateados para el export
     * @param string|null $batchNumber Número de lote (si null, se genera automáticamente)
     */
    public function __construct(Collection $data, ?string $batchNumber = null)
    {
        $this->data = $data;
        $this->batchNumber = $batchNumber ?? PaymentPortfolio::generateBatchNumber();
    }

    /**
     * Propiedades del documento (igual que el archivo original)
     */
    public function properties(): array
    {
        return [
            'creator' => 'Laravel App',
            'lastModifiedBy' => 'DIAD',
            'title' => 'Reporte OCEF',
            'subject' => 'Reporte OCEF',
            'description' => 'Reporte generado desde el sistema.',
            'keywords' => 'reporte,ocef,pagos',
            'category' => 'Reportes',
            'manager' => 'DIAD',
            'company' => 'Universidad',
        ];
    }

    /**
     * Crear export desde portfolios no enviados
     * 
     * @param string $processType Tipo de proceso (simulation, admission, etc.)
     * @param int|null $processId ID del proceso específico
     * @return static
     */
    public static function fromPendingPortfolios(string $processType, ?int $processId = null): static
    {
        $query = PaymentPortfolio::notSent()
            ->where('process_type', $processType);

        if ($processId) {
            $query->where('process_id', $processId);
        }

        $portfolios = $query->with(['tariff', 'payable'])->get();

        // Filtrar portfolios que tengan postulante existente
        // Los portfolios huérfanos (sin postulante) no se incluyen en la descarga
        // pero se mantienen en la base de datos como registro histórico
        $validPortfolios = $portfolios->filter(function ($portfolio) {
            return $portfolio->payable !== null;
        });

        $batchNumber = PaymentPortfolio::generateBatchNumber();
        
        $data = $validPortfolios->map(function ($portfolio) {
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

        $export = new static($data, $batchNumber);
        $export->portfolioIds = $validPortfolios->pluck('id')->toArray();

        return $export;
    }

    /**
     * Marcar portfolios como enviados después de la descarga
     */
    public function markAsSent(): void
    {
        if (empty($this->portfolioIds)) {
            return;
        }

        PaymentPortfolio::whereIn('id', $this->portfolioIds)
            ->update([
                'is_sent' => true,
                'sent_at' => now('America/Lima'),
                'batch_number' => $this->batchNumber,
            ]);
    }

    /**
     * Obtener el número de lote
     */
    public function getBatchNumber(): string
    {
        return $this->batchNumber;
    }

    /**
     * Obtener IDs de portfolios
     */
    public function getPortfolioIds(): array
    {
        return $this->portfolioIds;
    }

    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        return $this->data;
    }

    /**
     * Registrar eventos para escribir manualmente las celdas
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Escribir encabezados
                $headings = [
                    'BOL_FAC', 'DNI_RUC', 'NOMBRES_RAZ_SOCIAL', 'PATERNO', 'MATERNO',
                    'DIRECCION', 'CORREO', 'DESCRIPCION', 'PARTIDA', 'PROYECTO', 'MONTO'
                ];
                
                foreach ($headings as $colIndex => $heading) {
                    $col = chr(65 + $colIndex); // A, B, C, ...
                    $sheet->setCellValue("{$col}1", $heading);
                }
                
                // Escribir datos
                $row = 2;
                foreach ($this->data as $record) {
                    $values = array_values(is_array($record) ? $record : $record->toArray());
                    
                    foreach ($values as $colIndex => $value) {
                        $col = chr(65 + $colIndex);
                        $cell = "{$col}{$row}";
                        
                        // Columnas que deben ser texto explícito (triángulo verde)
                        if (in_array($col, $this->textColumns)) {
                            $sheet->setCellValueExplicit($cell, (string) $value, DataType::TYPE_STRING);
                        }
                        // Columna MONTO (K) como número
                        elseif ($col === 'K') {
                            $sheet->setCellValue($cell, (int) $value);
                        }
                        // Otras columnas como texto normal
                        else {
                            $sheet->setCellValue($cell, $value);
                        }
                    }
                    $row++;
                }
                
                // Aplicar formato de texto a columnas A-J
                $lastRow = $row - 1;
                if ($lastRow >= 2) {
                    for ($col = 'A'; $col <= 'J'; $col++) {
                        $sheet->getStyle("{$col}2:{$col}{$lastRow}")
                            ->getNumberFormat()
                            ->setFormatCode(NumberFormat::FORMAT_TEXT);
                    }
                    
                    // Formato numérico entero para MONTO
                    $sheet->getStyle("K2:K{$lastRow}")
                        ->getNumberFormat()
                        ->setFormatCode('0');
                }
                
                // Auto-ajustar ancho de columnas
                foreach (range('A', 'K') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'BOL_FAC',
            'DNI_RUC',
            'NOMBRES_RAZ_SOCIAL',
            'PATERNO',
            'MATERNO',
            'DIRECCION',
            'CORREO',
            'DESCRIPCION',
            'PARTIDA',
            'PROYECTO',
            'MONTO'
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_TEXT,
            'G' => NumberFormat::FORMAT_TEXT,
            'H' => NumberFormat::FORMAT_TEXT,
            'I' => NumberFormat::FORMAT_TEXT,
            'J' => NumberFormat::FORMAT_TEXT,
            'K' => '0', // Formato numérico entero
        ];
    }

    public function title(): string
    {
        return 'Hoja1';
    }
}


