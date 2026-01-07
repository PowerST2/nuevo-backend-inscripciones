<?php

namespace App\Models;

use App\Helpers\DocumentHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SystemDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'path',
        'type',
        'active',
        'virtual',
        'text',
    ];

    protected $casts = [
        'active' => 'boolean',
        'virtual' => 'boolean',
    ];

    /**
     * Boot del modelo - eventos
     */
    protected static function boot()
    {
        parent::boot();

        // Eliminar archivo cuando se elimina el registro
        static::deleting(function ($document) {
            if ($document->path) {
                Storage::disk('public')->delete($document->path);
            }
        });

        // Eliminar archivo anterior cuando se actualiza con uno nuevo
        static::updating(function ($document) {
            if ($document->isDirty('path') && $document->getOriginal('path')) {
                Storage::disk('public')->delete($document->getOriginal('path'));
            }
        });
    }

    /**
     * Obtener la URL pública del documento
     */
    public function getDocumentUrl(): string
    {
        if ($this->path) {
            return DocumentHelper::getDocumentUrl($this->path);
        }
        return '';
    }

    /**
     * Obtener el tamaño del documento
     */
    public function getDocumentSize(): int
    {
        if ($this->path && DocumentHelper::documentExists($this->path)) {
            return DocumentHelper::getDocumentSize($this->path);
        }
        return 0;
    }

    /**
     * Obtener la extensión del documento
     */
    public function getFileExtension(): string
    {
        if ($this->path) {
            return DocumentHelper::getFileExtension($this->path);
        }
        return '';
    }
}
