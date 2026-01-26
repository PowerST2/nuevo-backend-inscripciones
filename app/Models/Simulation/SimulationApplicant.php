<?php

namespace App\Models\Simulation;
use App\Models\Gender;
use App\Models\Ubigeo;

use App\Traits\GeneratesRegistrationCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class SimulationApplicant extends Model
{
    use HasFactory, GeneratesRegistrationCode, Notifiable;

    protected $fillable = [
        'uuid',
        'code',
        'dni',
        'last_name_father',
        'last_name_mother',
        'first_names',
        'email',
        'phone_mobile',
        'phone_other',
        'exam_simulation_id',
        'photo_path',
        'classroom',
        'tariff_id',
        'include_vocational',
        'genders_id',
        'birth_date',
        'ubigeo_id',
    ];
    
    protected $casts = [
        'include_vocational' => 'boolean',
    ];
    /**
     * Relación con el simulacro de examen
     */
    public function examSimulation()
    {
        return $this->belongsTo(ExamSimulation::class);
    }
    public function gender()
    {
        return $this->belongsTo(Gender::class, 'genders_id');
    }
    public function ubigeo()
    {
        return $this->belongsTo(Ubigeo::class, 'ubigeo_id');
    }

    /**
     * Relación con la tarifa asignada al postulante
     */
    public function tariff()
    {
        return $this->belongsTo(\App\Models\Tariff::class);
    }
    
    public function setLastNameFatherAttribute($value)
    {
        $this->attributes['last_name_father'] = mb_strtoupper($value);
    }

    public function setLastNameMotherAttribute($value)
    {
        $this->attributes['last_name_mother'] = mb_strtoupper($value);
    }

    public function setFirstNamesAttribute($value)
    {
        $this->attributes['first_names'] = mb_strtoupper($value);
    }

    /**
     * Relación con el proceso de simulacro
     */
    public function simulationProcess()
    {
        return $this->hasOne(SimulationProcess::class);
    }

    /**
     * Relación con el aula asignada
     */
    public function classroom()
    {
        return $this->belongsTo(\App\Models\Classroom::class);
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

    /**
     * Verificar si el postulante requiere foto (simulacro presencial)
     */
    public function requiresPhoto(): bool
    {
        return $this->examSimulation?->requiresPhoto() ?? false;
    }

    /**
     * Verificar si tiene foto
     */
    public function hasPhoto(): bool
    {
        return !empty($this->photo_path);
    }

    /**
     * Boot method para generar UUID automáticamente
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Generar UUID si no existe
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Obtener URL de la foto
     */
    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo_path ? asset('storage/' . $this->photo_path) : null;
    }
}
