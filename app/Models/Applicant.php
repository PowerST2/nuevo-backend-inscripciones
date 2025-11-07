<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Applicant extends Model
{
    use HasFactory;

    protected $fillable = [
        'period_id',
        'code',
        'code_cepre',
        'paternal_surname',
        'maternal_surname',
        'names',
        'document_type_id',
        'document_number',
        'email',
        'size',
        'weight',
        'gender_id',
        'cellular_phone',
        'phone',
        'other_phone',
        'ubigeo_id',
        'direction',
        'school_id',
        'university_id',
        'site_id',
        'start_study',
        'end_study',
        'date_birth',
        'country_birth_id',
        'ubigeo_birth_id',
        'modality1_id',
        'modality2_id',
        'speciality1_id',
        'speciality2_id',
        'speciality3_id',
        'speciality4_id',
        'speciality5_id',
        'speciality6_id',
        'classroom1_id',
        'classroom2_id',
        'classroom3_id',
        'classroom_voca_id',
        'annulled',
        'user_id',
    ];

    protected $casts = [
        'size' => 'decimal:2',
        'weight' => 'decimal:2',
        'date_birth' => 'date',
        'annulled' => 'boolean',
    ];

    public function period(): BelongsTo
    {
        return $this->belongsTo(Period::class);
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function gender(): BelongsTo
    {
        return $this->belongsTo(Gender::class);
    }

    public function ubigeo(): BelongsTo
    {
        return $this->belongsTo(Ubigeo::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function countryBirth(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_birth_id');
    }

    public function ubigaoBirth(): BelongsTo
    {
        return $this->belongsTo(Ubigeo::class, 'ubigeo_birth_id');
    }

    public function modality1(): BelongsTo
    {
        return $this->belongsTo(Modality::class, 'modality1_id');
    }

    public function modality2(): BelongsTo
    {
        return $this->belongsTo(Modality::class, 'modality2_id');
    }

    public function speciality1(): BelongsTo
    {
        return $this->belongsTo(Major::class, 'speciality1_id');
    }

    public function speciality2(): BelongsTo
    {
        return $this->belongsTo(Major::class, 'speciality2_id');
    }

    public function speciality3(): BelongsTo
    {
        return $this->belongsTo(Major::class, 'speciality3_id');
    }

    public function speciality4(): BelongsTo
    {
        return $this->belongsTo(Major::class, 'speciality4_id');
    }

    public function speciality5(): BelongsTo
    {
        return $this->belongsTo(Major::class, 'speciality5_id');
    }

    public function speciality6(): BelongsTo
    {
        return $this->belongsTo(Major::class, 'speciality6_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ApplicantDocument::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(PhotoApplicant::class);
    }
}
