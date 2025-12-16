<?php

namespace App\Http\Controllers\Api\Simulation;

use App\Traits\Simulation\SimulationApplicantTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class SimulationApplicantController extends Controller
{
    use SimulationApplicantTrait;

    /**
     * Buscar aplicante por DNI y email (ambos obligatorios)
     * GET /api/simulation-applicants/search?dni=12345678&email=test@email.com
     */
    public function search(Request $request)
    {
        $dni = $request->query('dni');
        $email = $request->query('email');

        if (!$dni || !$email) {
            return response()->json([
                'status' => 'error',
                'message' => 'Los campos dni y email son obligatorios',
            ], Response::HTTP_BAD_REQUEST);
        }

        $applicant = $this->searchByDniAndEmail($dni, $email);

        if (!$applicant) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se encontró un aplicante con los datos proporcionados',
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'status' => 'success',
            'data' => $applicant,
        ], Response::HTTP_OK);
    }

    /**
     * Insertar nuevo aplicante al simulacro activo
     * POST /api/simulation-applicants
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'dni' => 'required|string|max:8',
            'last_name_father' => 'required|string|max:50',
            'last_name_mother' => 'required|string|max:50',
            'first_names' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'phone_mobile' => 'required|string|max:10',
            'phone_other' => 'nullable|string|max:10',
        ]);

        $result = $this->insertApplicant($validated);

        if (!$result['success']) {
            return response()->json([
                'status' => 'error',
                'message' => $result['message'],
            ], Response::HTTP_BAD_REQUEST);
        }

        return response()->json([
            'status' => 'success',
            'message' => $result['message'],
        ], Response::HTTP_CREATED);
    }
}
