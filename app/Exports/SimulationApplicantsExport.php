<?php

namespace App\Exports;

use App\Models\Simulation\SimulationApplicant;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SimulationApplicantsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected Collection $applicants;
    protected string $title;

    public function __construct(Collection $applicants, string $title = 'Postulantes')
    {
        $this->applicants = $applicants;
        $this->title = $title;
    }

    public function collection(): Collection
    {
        return $this->applicants;
    }

    public function headings(): array
    {
        return [
            'Código',
            'DNI',
            'Apellido Paterno',
            'Apellido Materno',
            'Nombres',
            'Email',
            'Teléfono',
            'Tiene Foto',
            'Estado Foto',
            'Fecha Pago',
            'Fecha Inscripción',
            'Fecha Registro',
            'include_vocational'
        ];
    }

    public function map($applicant): array
    {
        return [
            $applicant->code ?? 'Pendiente',
            $applicant->dni,
            $applicant->last_name_father,
            $applicant->last_name_mother,
            $applicant->first_names,
            $applicant->email,
            $applicant->phone_mobile,
            $applicant->hasPhoto() ? 'Sí' : 'No',
            $this->getPhotoStatusLabel($applicant->simulationProcess?->photo_status),
            $applicant->simulationProcess?->payment_at?->format('d/m/Y H:i') ?? 'Pendiente',
            $applicant->simulationProcess?->registration_at?->format('d/m/Y H:i') ?? 'Pendiente',
            $applicant->created_at?->format('d/m/Y H:i'),
            $applicant->include_vocational ? 'Sí' : 'No',
        ];
    }

    protected function getPhotoStatusLabel(?string $status): string
    {
        return match ($status) {
            'pending' => 'Pendiente revisión',
            'approved' => 'Aprobada',
            'rejected' => 'Rechazada',
            default => 'Sin foto',
        };
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return $this->title;
    }
}
