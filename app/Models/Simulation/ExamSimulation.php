<?php

namespace App\Models\Simulation;

use App\Models\Tariff;
use App\Observers\Simulation\ExamSimulationObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy([ExamSimulationObserver::class])]
class ExamSimulation extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'description',
        'exam_date_start',
        'exam_date_end',
        'exam_date',
        'active',
        'tariff_id',
        'is_virtual',
    ];

    protected $casts = [
        'exam_date_start' => 'date',
        'exam_date_end' => 'date',
        'exam_date' => 'date',
        'active' => 'boolean',
        'is_virtual' => 'boolean',
    ];

    public function applicants(): HasMany
    {
        return $this->hasMany(SimulationApplicant::class);
    }

    /**
     * Relación con la tarifa/servicio
     */
    public function tariff(): BelongsTo
    {
        return $this->belongsTo(Tariff::class);
    }

    /**
     * Obtener si el simulacro está dentro del rango de fechas
     */
    public function getIsWithinRangeAttribute(): bool
    {
        $now = now()->toDateString();
        return $now >= $this->exam_date_start->toDateString() && $now <= $this->exam_date_end->toDateString();
    }

    /**
     * Obtener si el simulacro está realmente activo (dentro de rango Y activo en BD)
     */
    public function getIsReallyActiveAttribute(): bool
    {
        return $this->active && $this->is_within_range;
    }

    /**
     * Verificar si requiere foto (presencial)
     */
    public function requiresPhoto(): bool
    {
        return !$this->is_virtual;
    }

    /**
     * Obtener el tipo de modalidad como texto
     */
    public function getModalityTextAttribute(): string
    {
        return $this->is_virtual ? 'Virtual' : 'Presencial';
    }
}
