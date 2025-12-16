<?php

namespace App\Models\Simulation;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamSimulation extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'description',
        'exam_date_start',
        'exam_date_end',
        'active',
    ];

    protected $casts = [
        'exam_date_start' => 'date',
        'exam_date_end' => 'date',
        'active' => 'boolean',
    ];

    public function applicants(): HasMany
    {
        return $this->hasMany(SimulationApplicant::class);
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
}
