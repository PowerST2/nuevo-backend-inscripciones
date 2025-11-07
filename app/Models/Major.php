<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Major extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'channel',
        'faculty_id',
    ];

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function applicantsSpeciality1(): HasMany
    {
        return $this->hasMany(Applicant::class, 'speciality1_id');
    }

    public function applicantsSpeciality2(): HasMany
    {
        return $this->hasMany(Applicant::class, 'speciality2_id');
    }

    public function applicantsSpeciality3(): HasMany
    {
        return $this->hasMany(Applicant::class, 'speciality3_id');
    }

    public function applicantsSpeciality4(): HasMany
    {
        return $this->hasMany(Applicant::class, 'speciality4_id');
    }

    public function applicantsSpeciality5(): HasMany
    {
        return $this->hasMany(Applicant::class, 'speciality5_id');
    }

    public function applicantsSpeciality6(): HasMany
    {
        return $this->hasMany(Applicant::class, 'speciality6_id');
    }
}
