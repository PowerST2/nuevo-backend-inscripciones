<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class DocumentHelper
{
    /**
     * Obtener la ruta pública de un documento almacenado
     */
    public static function getDocumentUrl(string $path): string
    {
        return Storage::disk('public')->url($path);
    }

    /**
     * Guardar un documento del usuario
     */
    public static function storeDocument($file, string $directory = 'documents'): string
    {
        return Storage::disk('public')->putFile($directory, $file);
    }

    /**
     * Eliminar un documento
     */
    public static function deleteDocument(string $path): bool
    {
        return Storage::disk('public')->delete($path);
    }

    /**
     * Verificar si existe un documento
     */
    public static function documentExists(string $path): bool
    {
        return Storage::disk('public')->exists($path);
    }

    /**
     * Obtener el tamaño de un documento en bytes
     */
    public static function getDocumentSize(string $path): int
    {
        return Storage::disk('public')->size($path);
    }

    /**
     * Obtener la extensión del archivo
     */
    public static function getFileExtension(string $path): string
    {
        return pathinfo($path, PATHINFO_EXTENSION);
    }

    /**
     * Obtener el tipo MIME del archivo
     */
    public static function getFileMimeType(string $path): string
    {
        return Storage::disk('public')->mimeType($path);
    }
}
