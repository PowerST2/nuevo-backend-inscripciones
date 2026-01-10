<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Classroom extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'sector',
        'capacity',
        'available_1',
        'assigned_1',
        'available_2',
        'assigned_2',
        'available_3',
        'assigned_3',
        'available_voca',
        'assigned_voca',
        'active',
        'special',
        'vocational',
    ];

    protected $casts = [
        'active' => 'boolean',
        'special' => 'boolean',
        'vocational' => 'boolean',
    ];

    /**
     * Relación con los postulantes del simulacro asignados a esta aula
     */
    public function simulationApplicants()
    {
        return $this->hasMany(\App\Models\Simulation\SimulationApplicant::class);
    }
}
