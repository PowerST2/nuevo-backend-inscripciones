<?php

namespace App\Models\Simulation;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SimulationApplicant extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'dni',
        'last_name_father',
        'last_name_mother',
        'first_names',
        'email',
        'phone_mobile',
        'phone_other',
        'exam_simulation_id',
    ];

    /**
     * Relación con el simulacro de examen
     */
    public function examSimulation()
    {
        return $this->belongsTo(ExamSimulation::class);
    }

    /**
     * Relación con el proceso de simulacro
     */
    public function simulationProcess()
    {
        return $this->hasOne(SimulationProcess::class);
    }

    /**
     * Verificar si el postulante puede editar sus datos
     */
    public function canEditData(): bool
    {
        return $this->simulationProcess?->canEditData() ?? true;
    }

    /**
     * Verificar si el postulante ha completado su inscripción
     */
    public function isRegistered(): bool
    {
        return $this->simulationProcess?->registration ?? false;
    }

    /**
     * Obtener nombre completo
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_names} {$this->last_name_father} {$this->last_name_mother}");
    }
}
