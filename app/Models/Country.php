<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $fillable = [
        'code',
        'name',
    ];

    public function schools(): HasMany
    {
        return $this->hasMany(School::class, 'pais_id');
    }

    public function universities(): HasMany
    {
        return $this->hasMany(University::class);
    }

    public function applicantsBirth(): HasMany
    {
        return $this->hasMany(Applicant::class, 'country_birth_id');
    }
}
