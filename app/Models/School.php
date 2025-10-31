<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class School extends Model
{
    protected $fillable = [
        'code',
        'annexed',
        'level',
        'nombre',
        'management_minedu',
        'management',
        'director',
        'direction',
        'ubigeo_id',
        'pais_id',
    ];

    public function ubigeo(): BelongsTo
    {
        return $this->belongsTo(Ubigeo::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'pais_id');
    }

    public function applicants(): HasMany
    {
        return $this->hasMany(Applicant::class);
    }
}
