<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class University extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'management',
        'ubigeo_id',
        'country_id',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    protected $appends = [
        'department',
        'province',
        'district',
    ];

    public function ubigeo(): BelongsTo
    {
        return $this->belongsTo(Ubigeo::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function applicants(): HasMany
    {
        return $this->hasMany(Applicant::class);
    }

    /**
     * Get the department value from the related ubigeo
     */
    public function getDepartmentAttribute(): ?string
    {
        return $this->ubigeo?->department;
    }

    /**
     * Get the province value from the related ubigeo
     */
    public function getProvinceAttribute(): ?string
    {
        return $this->ubigeo?->province;
    }

    /**
     * Get the district value from the related ubigeo
     */
    public function getDistrictAttribute(): ?string
    {
        return $this->ubigeo?->district;
    }
}
