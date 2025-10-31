<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ubigeo extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'code',
        'description',
        'departamento',
        'province',
        'district',
        'reniec',
    ];

    public function schools(): HasMany
    {
        return $this->hasMany(School::class);
    }

    public function universities(): HasMany
    {
        return $this->hasMany(University::class);
    }

    public function applicants(): HasMany
    {
        return $this->hasMany(Applicant::class);
    }

    public function applicantsBirth(): HasMany
    {
        return $this->hasMany(Applicant::class, 'ubigeo_birth_id');
    }
}
