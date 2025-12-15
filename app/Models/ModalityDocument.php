<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ModalityDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'modality_id',
        'document_code',
        'path_document',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Boot del modelo - eventos
     */
    protected static function boot()
    {
        parent::boot();

        // Eliminar archivo cuando se elimina el registro
        static::deleting(function ($document) {
            if ($document->path_document) {
                Storage::disk('public')->delete($document->path_document);
            }
        });

        // Eliminar archivo anterior cuando se actualiza con uno nuevo
        static::updating(function ($document) {
            if ($document->isDirty('path_document') && $document->getOriginal('path_document')) {
                Storage::disk('public')->delete($document->getOriginal('path_document'));
            }
        });
    }

    public function modality(): BelongsTo
    {
        return $this->belongsTo(Modality::class);
    }
}
