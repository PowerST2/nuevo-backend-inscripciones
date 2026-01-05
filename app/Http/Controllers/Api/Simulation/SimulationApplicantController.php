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
     * POST /api/simulation-applicants/search
     * Body: { "dni": "12345678", "email": "test@email.com" }
     */
    public function search(Request $request)
    {
        $dni = $request->input('dni');
        $email = $request->input('email');

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
            'data' => $result['data'] ?? null,
        ], Response::HTTP_CREATED);
    }

    /**
     * Actualizar datos del aplicante
     * PUT /api/simulation-applicants/update
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'dni' => 'required|string|size:8',
            'email' => 'required|email',
            'last_name_father' => 'sometimes|string|max:50',
            'last_name_mother' => 'sometimes|string|max:50',
            'first_names' => 'sometimes|string|max:100',
            'phone_mobile' => 'sometimes|nullable|string|max:20',
            'phone_other' => 'sometimes|nullable|string|max:20',
        ]);

        $result = $this->updateApplicant(
            $validated['dni'],
            $validated['email'],
            $request->only(['last_name_father', 'last_name_mother', 'first_names', 'phone_mobile', 'phone_other'])
        );

        return response()->json([
            'status' => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
        ], $result['success'] ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);
    }

    /**
     * Confirmar datos del aplicante
     * POST /api/simulation-applicants/confirm
     */
    public function confirmData(Request $request)
    {
        $validated = $request->validate([
            'dni' => 'required|string|size:8',
            'email' => 'required|email',
        ]);

        $result = $this->confirmApplicantData($validated['dni'], $validated['email']);

        return response()->json([
            'status' => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
        ], $result['success'] ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);
    }

    /**
     * Obtener estado del proceso
     * GET /api/simulation-applicants/status?dni=12345678&email=test@email.com
     */
    public function getStatus(Request $request)
    {
        $dni = $request->query('dni');
        $email = $request->query('email');

        if (!$dni || !$email) {
            return response()->json([
                'status' => 'error',
                'message' => 'Los campos dni y email son obligatorios',
            ], Response::HTTP_BAD_REQUEST);
        }

        $result = $this->getProcessStatus($dni, $email);

        return response()->json([
            'status' => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
        ], $result['success'] ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
    }

    /**
     * Marcar pago como completado (uso interno/webhook)
     * POST /api/simulation-applicants/mark-payment
     */
    public function markPayment(Request $request)
    {
        $validated = $request->validate([
            'dni' => 'required|string|size:8',
            'email' => 'required|email',
        ]);

        $result = $this->markPaymentComplete($validated['dni'], $validated['email']);

        return response()->json([
            'status' => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
        ], $result['success'] ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);
    }

    /**
     * Completar inscripción - El postulante hace clic en el botón final
     * Envía el correo con todos los datos del postulante y simulacro
     * POST /api/simulation-applicants/complete
     */
    public function complete(Request $request)
    {
        $validated = $request->validate([
            'dni' => 'required|string|size:8',
            'email' => 'required|email',
        ]);

        $result = $this->completeRegistration($validated['dni'], $validated['email']);

        return response()->json([
            'status' => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
        ], $result['success'] ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);
    }
}
