<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Modality extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'name_regulation',
        'description',
        'start_studies',
        'active',
    ];

    protected $casts = [
        'start_studies' => 'boolean',
        'active' => 'boolean',
    ];

    public function applicants(): HasMany
    {
        return $this->hasMany(Applicant::class);
    }

    public function modalityDocuments(): HasMany
    {
        return $this->hasMany(ModalityDocument::class);
    }
}
