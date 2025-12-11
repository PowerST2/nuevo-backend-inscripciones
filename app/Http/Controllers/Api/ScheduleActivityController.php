<?php

namespace App\Http\Controllers\Api;

use App\Traits\ScheduleActivityTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class ScheduleActivityController extends Controller
{
    use ScheduleActivityTrait;

    /**
     * Verificar si una actividad está activa
     * Recibe el nombre de la actividad en el body
     */
    public function index(Request $request)
    {
        $name = $request->input('name');

        if (!$name) {
            return response()->json([
                'status' => 'error',
                'message' => 'El campo name es requerido',
            ], Response::HTTP_BAD_REQUEST);
        }

        $isActive = $this->isActivityActive($name);

        return response()->json([
            'status' => 'success',
            'is_active' => $isActive,
        ], Response::HTTP_OK);
    }
}