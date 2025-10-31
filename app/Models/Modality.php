<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Modality extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'code',
        'name',
        'name_regulation',
        'description',
        'previous_modality',
        'start_studies',
        'active',
    ];

    protected $casts = [
        'start_studies' => 'boolean',
        'active' => 'boolean',
    ];

    public function documents(): HasMany
    {
        return $this->hasMany(ModalityDocument::class);
    }

    public function applicantsModality1(): HasMany
    {
        return $this->hasMany(Applicant::class, 'modality1_id');
    }

    public function applicantsModality2(): HasMany
    {
        return $this->hasMany(Applicant::class, 'modality2_id');
    }
}
