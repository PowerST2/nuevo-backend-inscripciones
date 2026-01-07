<?php

namespace App\Http\Controllers\Api;

use App\Traits\ModalityDocumentTrait;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class ModalityDocumentController extends Controller
{
    use ModalityDocumentTrait;

    /**
     * Obtener todos los documentos de la modalidad activa
     */
    public function index()
    {
        $documents = $this->getDocumentsByActiveModality();

        return response()->json([
            'status' => 'success',
            'data' => $documents,
        ], Response::HTTP_OK);
    }
}
