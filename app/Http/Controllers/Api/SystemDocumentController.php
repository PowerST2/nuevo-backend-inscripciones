<?php

namespace App\Http\Controllers\Api;

use App\Traits\SystemDocumentTrait;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class SystemDocumentController extends Controller
{
    use SystemDocumentTrait;

    /**
     * Obtener el path de un documento por nombre
     * Recibe el nombre del documento en la URL
     */
    public function index(string $code)
    {
        $path = $this->getDocumentPathByCode($code);

        if (!$path) {
            return response()->json([
                'status' => 'error',
                'message' => 'Documento no encontrado',
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'status' => 'success',
            'path' => $path,
        ], Response::HTTP_OK);
    }
}
