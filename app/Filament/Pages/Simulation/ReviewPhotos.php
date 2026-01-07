<?php

namespace App\Filament\Pages\Simulation;

use App\Models\Simulation\ExamSimulation;
use App\Models\Simulation\SimulationApplicant;
use App\Models\Simulation\SimulationProcess;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Enums\MaxWidth;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Storage;
use UnitEnum;

class ReviewPhotos extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCamera;
    protected static ?string $navigationLabel = 'Revisar Fotos';
    protected static ?string $title = 'Revisar Fotos de Postulantes';
    protected static string|UnitEnum|null $navigationGroup = 'Simulacros';
    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.pages.simulation.review-photos';

    public ?int $selectedSimulationId = null;
    public array $pendingPhotos = [];
    public string $rejectReason = '';
    public ?int $rejectingPhotoId = null;

    public function mount(): void
    {
        // Seleccionar simulacro activo por defecto
        $activeSimulation = ExamSimulation::where('active', true)->first();
        $this->selectedSimulationId = $activeSimulation?->id;
        $this->loadPendingPhotos();
    }

    public function loadPendingPhotos(): void
    {
        if (!$this->selectedSimulationId) {
            $this->pendingPhotos = [];
            return;
        }

        $this->pendingPhotos = SimulationApplicant::where('exam_simulation_id', $this->selectedSimulationId)
            ->whereHas('simulationProcess', function ($query) {
                $query->where('photo_status', SimulationProcess::PHOTO_STATUS_PENDING)
                    ->whereNotNull('photo_at');
            })
            ->whereNotNull('photo_path')
            ->with('simulationProcess')
            ->get()
            ->map(function ($applicant) {
                return [
                    'id' => $applicant->id,
                    'uuid' => $applicant->uuid,
                    'dni' => $applicant->dni,
                    'full_name' => $applicant->full_name,
                    'email' => $applicant->email,
                    'photo_url' => $applicant->photo_path ? Storage::disk('public')->url($applicant->photo_path) : null,
                    'photo_at' => $applicant->simulationProcess->photo_at?->format('d/m/Y H:i'),
                ];
            })
            ->toArray();
    }

    public function updatedSelectedSimulationId(): void
    {
        $this->loadPendingPhotos();
    }

    public function approvePhoto(int $applicantId): void
    {
        $applicant = SimulationApplicant::find($applicantId);
        
        if (!$applicant || !$applicant->simulationProcess) {
            Notification::make()
                ->title('Error')
                ->body('No se encontró el postulante')
                ->danger()
                ->send();
            return;
        }

        $applicant->simulationProcess->approvePhoto();

        Notification::make()
            ->title('Foto Aprobada')
            ->body("La foto de {$applicant->full_name} ha sido aprobada.")
            ->success()
            ->send();

        $this->loadPendingPhotos();
    }

    public function openRejectModal(int $applicantId): void
    {
        $this->rejectingPhotoId = $applicantId;
        $this->rejectReason = '';
        $this->dispatch('open-modal', id: 'reject-photo-modal');
    }

    public function confirmReject(): void
    {
        if (!$this->rejectingPhotoId) {
            return;
        }

        if (empty(trim($this->rejectReason))) {
            Notification::make()
                ->title('Error')
                ->body('Debe ingresar el motivo del rechazo')
                ->danger()
                ->send();
            return;
        }

        $applicant = SimulationApplicant::find($this->rejectingPhotoId);
        
        if (!$applicant || !$applicant->simulationProcess) {
            Notification::make()
                ->title('Error')
                ->body('No se encontró el postulante')
                ->danger()
                ->send();
            return;
        }

        $applicant->simulationProcess->rejectPhoto(trim($this->rejectReason));

        Notification::make()
            ->title('Foto Rechazada')
            ->body("La foto de {$applicant->full_name} ha sido rechazada. Deberá subir una nueva.")
            ->warning()
            ->send();

        $this->rejectingPhotoId = null;
        $this->rejectReason = '';
        $this->dispatch('close-modal', id: 'reject-photo-modal');
        $this->loadPendingPhotos();
    }

    public function cancelReject(): void
    {
        $this->rejectingPhotoId = null;
        $this->rejectReason = '';
        $this->dispatch('close-modal', id: 'reject-photo-modal');
    }

    public function getRejectingApplicantProperty(): ?array
    {
        if (!$this->rejectingPhotoId) {
            return null;
        }
        
        return collect($this->pendingPhotos)->firstWhere('id', $this->rejectingPhotoId);
    }

    public function getSimulationsProperty(): array
    {
        return ExamSimulation::orderBy('created_at', 'desc')
            ->pluck('description', 'id')
            ->toArray();
    }

    public function getPendingCountProperty(): int
    {
        return count($this->pendingPhotos);
    }
}
