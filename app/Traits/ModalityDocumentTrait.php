<?php

namespace App\Traits;

use App\Models\ModalityDocument;
use Illuminate\Support\Facades\Storage;

trait ModalityDocumentTrait
{
    /**
     * Obtener todos los documentos de la modalidad activa (solo id y path_document con URL pública)
     */
    public function getDocumentsByActiveModality()
    {
        $documents = ModalityDocument::whereHas('modality', function ($query) {
                $query->where('active', true);
            })
            ->where('active', true)
            ->select('id', 'path_document' , 'document_code')
            ->get();

        // Convertir path a URL pública
        return $documents->map(function ($doc) {
            return [
                'id' => $doc->id,
                'path_document' => $doc->path_document ? Storage::disk('public')->url($doc->path_document) : null,
                'document_code' => $doc->document_code,
            ];
        });
    }
}
