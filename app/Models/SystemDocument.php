<?php

namespace App\Models;

use App\Helpers\DocumentHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
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
