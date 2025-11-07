<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
    ];

    public function schools(): HasMany
    {
        return $this->hasMany(School::class);
    }

    public function universities(): HasMany
    {
        return $this->hasMany(University::class);
    }

    public function applicantsBirth(): HasMany
    {
        return $this->hasMany(Applicant::class, 'country_birth_id');
    }

    public function applicants(): HasMany
    {
        return $this->hasMany(Applicant::class);
    }
}
