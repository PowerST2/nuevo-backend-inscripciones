<?php

namespace App\Models\Simulation;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SimulationProcess extends Model
{
    use HasFactory;

    // Desactivamos created_at y updated_at
    public $timestamps = false;

    protected $fillable = [
        'simulation_applicant_id',
        'pre_registration_at',
        'payment_at',
        'photo_at',
        'data_confirmation_at',
        'registration_at',
    ];

    protected $casts = [
        'pre_registration_at' => 'datetime',
        'payment_at' => 'datetime',
        'photo_at' => 'datetime',
        'data_confirmation_at' => 'datetime',
        'registration_at' => 'datetime',
    ];

    public function simulationApplicant()
    {
        return $this->belongsTo(SimulationApplicant::class);
    }

    public function canEditData(): bool
    {
        return is_null($this->data_confirmation_at);
    }

    public function hasPaid(): bool
    {
        return !is_null($this->payment_at);
    }

    public function hasUploadedPhoto(): bool
    {
        return !is_null($this->photo_at);
    }

    /**
     * Marcar que la foto fue subida
     */
    public function markPhotoUploaded(): bool
    {
        $this->photo_at = now('America/Lima');
        return $this->save();
    }

    public function confirmData(): bool
    {
        if (is_null($this->payment_at)) {
            return false;
        }

        // Forzamos hora de Lima (UTC-5)
        $now = now('America/Lima');

        // Solo confirma datos, NO completa registration
        $this->data_confirmation_at = $now;
        
        return $this->save();
    }

    /**
     * Completar inscripción - paso final cuando el postulante hace clic
     */
    public function completeRegistration(): bool
    {
        // Verificar que haya pagado y confirmado datos
        if (is_null($this->payment_at) || is_null($this->data_confirmation_at)) {
            return false;
        }

        // Forzamos hora de Lima (UTC-5)
        $this->registration_at = now('America/Lima');
        
        return $this->save();
    }

    public function markPaymentComplete(): bool
    {
        if (is_null($this->payment_at)) {
            // Forzamos hora de Lima (UTC-5)
            $this->payment_at = now('America/Lima');
            return $this->save();
        }
        
        return true;
    }

    /**
     * Verificar si todos los pasos están completados
     */
    public function isComplete(): bool
    {
        return !is_null($this->pre_registration_at)
            && !is_null($this->payment_at)
            && !is_null($this->data_confirmation_at)
            && !is_null($this->registration_at);
    }
}