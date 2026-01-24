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
        'is_virtual',
        'is_vocational',
    ];

    protected $casts = [
        'exam_date_start' => 'date',
        'exam_date_end' => 'date',
        'exam_date' => 'date',
        'active' => 'boolean',
        'is_virtual' => 'boolean',
        'is_vocational' => 'boolean',
    ];

    public function applicants(): HasMany
    {
        return $this->hasMany(SimulationApplicant::class);
    }

    /**
     * Obtener las tarifas disponibles para este examen según su modalidad y vocacional
     */
    public function getAvailableTariffsAttribute()
    {
        // Códigos de tarifas según modalidad y vocacional:
        // 520: Presencial sin vocacional
        // 521: Presencial con vocacional
        // 522: Virtual con vocacional
        // 523: Virtual sin vocacional
        
        $codes = [];
        
        if (!$this->is_virtual && !$this->is_vocational) {
            // Presencial sin vocacional
            $codes = ['520'];
        } elseif (!$this->is_virtual && $this->is_vocational) {
            // Presencial vocacional: ofrece ambas opciones
            $codes = ['520', '521'];
        } elseif ($this->is_virtual && !$this->is_vocational) {
            // Virtual sin vocacional
            $codes = ['523'];
        } elseif ($this->is_virtual && $this->is_vocational) {
            // Virtual vocacional: ofrece ambas opciones
            $codes = ['522', '523'];
        }
        
        return Tariff::whereIn('code', $codes)
            ->where('active', true)
            ->where('is_admission', false)
            ->get();
    }

    /**
     * Obtener la tarifa que corresponde al postulante según su elección de vocacional
     */
    public function getTariffForApplicant(bool $wantsVocational)
    {
        // Si el examen no es vocacional, forzar a false
        $wantsVocational = $this->is_vocational ? $wantsVocational : false;

        if (!$this->is_virtual && !$wantsVocational) {
            $code = '520'; // Presencial sin vocacional
        } elseif (!$this->is_virtual && $wantsVocational) {
            $code = '521'; // Presencial con vocacional
        } elseif ($this->is_virtual && $wantsVocational) {
            $code = '522'; // Virtual con vocacional
        } else {
            $code = '523'; // Virtual sin vocacional
        }

        return $this->available_tariffs->firstWhere('code', $code);
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
