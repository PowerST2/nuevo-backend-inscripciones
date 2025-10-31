<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModalityDocument extends Model
{
    protected $fillable = [
        'modality_id',
        'document_name',
        'document',
    ];

    public function modality(): BelongsTo
    {
        return $this->belongsTo(Modality::class);
    }
}
