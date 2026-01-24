<?php

namespace App\Http\Controllers\Api\Simulation;

use App\Traits\Simulation\ExamSimulationTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class ExamSimulationController extends Controller
{
    use ExamSimulationTrait;

    /**
     * Obtener estado del simulacro (si hay uno activo o no) y si las inscripciones están abiertas.
     */
    public function index()
    {
        $simulation = $this->getActiveSimulation(false); // Traer el simulacro activo sin validar fechas
        $isActive = $simulation !== null;
        $isInscriptionOpen = $isActive && $simulation->is_within_range; // Inscripción abierta solo si está en rango

        // Si está activo, enviar datos
        if ($isActive) {
            return response()->json([
                'data' => [
                    'status' => 'success',
                    'is_active' => $isActive,
                    'is_inscription_open' => $isInscriptionOpen,
                    'description' => $simulation->description,
                    'exam_date_start' => $simulation->exam_date_start->format('d/m/Y'),
                    'exam_date_end' => $simulation->exam_date_end->format('d/m/Y'),
                    'exam_date' => $simulation->exam_date?->format('d/m/Y'),
                    'is_virtual' => $simulation->is_virtual,
                ]
            ], Response::HTTP_OK);
        }

        // Si NO está activo, solo enviar esto
        return response()->json([
            'data' => [
                'status' => 'success',
                'is_active' => false,
                'is_inscription_open' => false,
            ]
        ], Response::HTTP_OK);
    }
}
