<?php

namespace App\Filament\Pages\Simulation;

use App\Models\Simulation\ExamSimulation;
use App\Models\Simulation\SimulationApplicant;
use App\Models\Simulation\SimulationProcess;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
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
use Livewire\WithPagination;

class ReviewPhotos extends Page
{
    use HasPageShield;
    use WithPagination;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCamera;
    protected static ?string $navigationLabel = 'Revisar Fotos';
    protected static ?string $title = 'Revisar Fotos de Postulantes';
    protected static string|UnitEnum|null $navigationGroup = 'Simulacros';
    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.pages.simulation.review-photos';

    public ?int $selectedSimulationId = null;
    public array $pendingPhotos = [];
    public int $currentIndex = 0;
    public string $searchDni = '';
    public string $rejectReason = '';
    public ?int $rejectingPhotoId = null;

    public int $perPage = 50;
    public int $totalPending = 0;
    public int $currentPage = 1;
    public int $lastPage = 1;

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
            $this->currentIndex = 0;
            $this->totalPending = 0;
            $this->currentPage = 1;
            $this->lastPage = 1;
            return;
        }

        $query = SimulationApplicant::where('exam_simulation_id', $this->selectedSimulationId)
            ->whereHas('simulationProcess', function ($query) {
                $query->where('photo_status', SimulationProcess::PHOTO_STATUS_PENDING)
                    ->whereNotNull('photo_at');
            })
            ->whereNotNull('photo_path')
            ->with('simulationProcess');

        // Filtrar por DNI si hay búsqueda
        if (!empty($this->searchDni)) {
            $query->where('dni', 'like', '%' . $this->searchDni . '%');
        }

        $paginator = $query->orderBy('created_at', 'asc')
            ->paginate($this->perPage, pageName: 'photos');

        $this->totalPending = (int) $paginator->total();
        $this->currentPage = (int) $paginator->currentPage();
        $this->lastPage = (int) max(1, $paginator->lastPage());

        $this->pendingPhotos = collect($paginator->items())
            ->map(function ($applicant) {
                return [
                    'id' => $applicant->id,
                    'uuid' => $applicant->uuid,
                    'dni' => $applicant->dni,
                    'full_name' => $applicant->full_name,
                    'email' => $applicant->email,
                    'phone' => $applicant->phone_mobile,
                    'photo_url' => $applicant->photo_path ? Storage::disk('public')->url($applicant->photo_path) : null,
                    'photo_at' => $applicant->simulationProcess->photo_at?->format('d/m/Y H:i'),
                ];
            })
            ->values()
            ->toArray();

        // Resetear índice si está fuera de rango
        if ($this->currentIndex >= count($this->pendingPhotos)) {
            $this->currentIndex = max(0, count($this->pendingPhotos) - 1);
        }
    }

    public function updatedSelectedSimulationId(): void
    {
        $this->currentIndex = 0;
        $this->searchDni = '';
        $this->resetPage('photos');
        $this->loadPendingPhotos();
    }

    public function updatedSearchDni(): void
    {
        $this->currentIndex = 0;
        $this->resetPage('photos');
        $this->loadPendingPhotos();
    }

    public function clearSearch(): void
    {
        $this->searchDni = '';
        $this->currentIndex = 0;
        $this->resetPage('photos');
        $this->loadPendingPhotos();
    }

    public function nextPagePhotos(): void
    {
        $this->nextPage('photos');
        $this->currentIndex = 0;
        $this->loadPendingPhotos();
    }

    public function previousPagePhotos(): void
    {
        $this->previousPage('photos');
        $this->currentIndex = 0;
        $this->loadPendingPhotos();
    }

    public function gotoPagePhotos(int $page): void
    {
        $this->gotoPage($page, 'photos');
        $this->currentIndex = 0;
        $this->loadPendingPhotos();
    }

    public function nextPhoto(): void
    {
        if ($this->currentIndex < count($this->pendingPhotos) - 1) {
            $this->currentIndex++;
        }
    }

    public function previousPhoto(): void
    {
        if ($this->currentIndex > 0) {
            $this->currentIndex--;
        }
    }

    public function goToPhoto(int $index): void
    {
        if ($index >= 0 && $index < count($this->pendingPhotos)) {
            $this->currentIndex = $index;
        }
    }

    public function getCurrentPhotoProperty(): ?array
    {
        return $this->pendingPhotos[$this->currentIndex] ?? null;
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

    public function approveCurrent(): void
    {
        $current = $this->currentPhoto;
        if ($current) {
            $this->approvePhoto($current['id']);
        }
    }

    public function rejectCurrent(): void
    {
        $current = $this->currentPhoto;
        if ($current) {
            $this->openRejectModal($current['id']);
        }
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
        return $this->totalPending;
    }
}
