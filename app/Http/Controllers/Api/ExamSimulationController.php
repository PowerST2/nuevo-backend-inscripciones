<?php

namespace App\Http\Controllers\Api;

use App\Traits\ExamSimulationTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class ExamSimulationController extends Controller
{
    use ExamSimulationTrait;

    /**
     * Obtener estado del simulacro (si hay uno activo o no)
     * Valida: active=true AND fecha actual entre exam_date_start y exam_date_end
     */
    public function index()
{
    $simulation = $this->getActiveSimulation();
    $isActive = $simulation !== null;

    // Si está activo, enviar datos
    if ($isActive) {
        return response()->json([
            'data' => [
                'status' => 'success',
                'is_active' => true,
                'description' => $simulation->description,
                'exam_date_start' => $simulation->exam_date_start->format('d/m/Y'),
                'exam_date_end' => $simulation->exam_date_end->format('d/m/Y'),
            ]
        ], Response::HTTP_OK);
    }

    // Si NO está activo, solo enviar esto
    return response()->json([
        'data' => [
            'status' => 'success',
            'is_active' => false,
        ]
    ], Response::HTTP_OK);
}


}