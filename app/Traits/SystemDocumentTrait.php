<?php

namespace App\Traits;

use App\Models\SystemDocument;
use Illuminate\Support\Facades\Storage;

trait SystemDocumentTrait
{
    /**
     * Obtener la URL pública de un documento por código
     */
    public function getDocumentPathByCode(string $code): ?string
    {
        $document = SystemDocument::where('code', $code)
            ->where('active', true)
            ->first();

        if (!$document || !$document->path) {
            return null;
        }

        // Devuelve la URL pública del archivo
        return Storage::disk('public')->url($document->path);
    }
}
