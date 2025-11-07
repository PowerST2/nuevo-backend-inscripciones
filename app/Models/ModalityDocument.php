<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModalityDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'modality_id',
        'document_name',
        'path_document',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function modality(): BelongsTo
    {
        return $this->belongsTo(Modality::class);
    }
}
