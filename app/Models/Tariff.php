<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tariff extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'description',
        'item',
        'project',
        'amount',
        'active',
        'sort_order',
        'is_admission',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'active' => 'boolean',
        'sort_order' => 'integer',
        'is_admission' => 'boolean',
    ];

    /**
     * Relación con pagos
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'service_code', 'code');
    }

    /**
     * Scope para tarifas activas
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope ordenado
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('code');
    }

    /**
     * Scope para tarifas de admisión
     */
    public function scopeForAdmission($query)
    {
        return $query->where('is_admission', true);
    }

    /**
     * Scope para tarifas de simulacro
     */
    public function scopeForSimulation($query)
    {
        return $query->where('is_admission', false);
    }

    /**
     * Obtener descripción completa para selects
     */
    public function getFullDescriptionAttribute(): string
    {
        return "{$this->code} - {$this->description}";
    }
}
