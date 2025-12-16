<?php

namespace App\Models\Simulation;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SimulationProcess extends Model
{
    use HasFactory;

    protected $fillable = [
        'simulation_applicant_id',
        'pre_registration',
        'payment',
        'data_confirmation',
        'registration',
    ];

    protected $casts = [
        'pre_registration' => 'boolean',
        'payment' => 'boolean',
        'data_confirmation' => 'boolean',
        'registration' => 'boolean',
    ];

    /**
     * Relación con el aplicante al simulacro
     */
    public function simulationApplicant()
    {
        return $this->belongsTo(SimulationApplicant::class);
    }

    /**
     * Verificar si el postulante puede editar sus datos
     * Solo puede editar si no ha confirmado sus datos
     */
    public function canEditData(): bool
    {
        return !$this->data_confirmation;
    }

    /**
     * Verificar si el postulante completó el pago
     */
    public function hasPaid(): bool
    {
        return $this->payment;
    }

    /**
     * Confirmar datos del postulante
     * Esto también marca la inscripción como completada
     */
    public function confirmData(): bool
    {
        // Solo puede confirmar si ya realizó el pago
        if (!$this->payment) {
            return false;
        }

        $this->data_confirmation = true;
        $this->registration = true;
        return $this->save();
    }

    /**
     * Marcar el pago como realizado
     */
    public function markPaymentComplete(): bool
    {
        $this->payment = true;
        return $this->save();
    }
}
