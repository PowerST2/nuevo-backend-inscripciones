<?php

namespace App\Http\Controllers\Api\Simulation;

use App\Models\Simulation\ExamSimulation;
use App\Traits\Simulation\SimulationApplicantTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

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
        // Verificar si hay simulacro activo y si requiere foto
        $activeSimulation = $this->getActiveExamSimulation();
        
        if (!$activeSimulation) {
            return response()->json([
                'status' => 'error',
                'message' => 'No hay un simulacro activo en este momento',
            ], Response::HTTP_BAD_REQUEST);
        }

        // Reglas de validación base
        $rules = [
            'dni' => 'required|string|max:8',
            'last_name_father' => 'required|string|max:50',
            'last_name_mother' => 'required|string|max:50',
            'first_names' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'phone_mobile' => 'required|string|max:10',
            'phone_other' => 'nullable|string|max:10',
        ];

        // Si el simulacro es presencial (no virtual), la foto es obligatoria
        if ($activeSimulation->requiresPhoto()) {
            $rules['photo'] = 'required|image|mimes:jpeg,jpg,png|max:2048';
        } else {
            $rules['photo'] = 'nullable|image|mimes:jpeg,jpg,png|max:2048';
        }

        $validated = $request->validate($rules, [
            'photo.required' => 'La foto es obligatoria para simulacros presenciales',
            'photo.image' => 'El archivo debe ser una imagen',
            'photo.mimes' => 'La foto debe ser formato JPEG, JPG o PNG',
            'photo.max' => 'La foto no debe superar los 2MB',
        ]);

        // Procesar foto si existe
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $filename = $validated['dni'] . '_' . time() . '.' . $photo->getClientOriginalExtension();
            $photoPath = $photo->storeAs('simulation-photos/' . $activeSimulation->id, $filename, 'public');
        }

        $validated['photo_path'] = $photoPath;

        $result = $this->insertApplicant($validated);

        if (!$result['success']) {
            // Si falla, eliminar la foto subida
            if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }
            
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
            'photo' => 'sometimes|nullable|image|mimes:jpeg,jpg,png|max:2048',
        ], [
            'photo.image' => 'El archivo debe ser una imagen',
            'photo.mimes' => 'La foto debe ser formato JPEG, JPG o PNG',
            'photo.max' => 'La foto no debe superar los 2MB',
        ]);

        // Procesar foto si se envió
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $filename = $validated['dni'] . '_' . time() . '.' . $photo->getClientOriginalExtension();
            
            // Obtener simulacro activo para la carpeta
            $activeSimulation = $this->getActiveExamSimulation();
            $simulationId = $activeSimulation?->id ?? 'unknown';
            
            $photoPath = $photo->storeAs('simulation-photos/' . $simulationId, $filename, 'public');
        }

        $dataToUpdate = $request->only(['last_name_father', 'last_name_mother', 'first_names', 'phone_mobile', 'phone_other']);
        
        if ($photoPath) {
            $dataToUpdate['photo_path'] = $photoPath;
        }

        $result = $this->updateApplicant(
            $validated['dni'],
            $validated['email'],
            $dataToUpdate
        );

        // Si falló y subimos foto, eliminarla
        if (!$result['success'] && $photoPath) {
            Storage::disk('public')->delete($photoPath);
        }

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

    /**
     * Actualizar datos y confirmar en un solo paso
     * POST /api/simulation-applicants/update-and-confirm
     * 
     * Este endpoint permite al postulante:
     * 1. Modificar sus datos personales (si aún puede)
     * 2. Actualizar su foto (si el simulacro es presencial)
     * 3. Confirmar que los datos son correctos (bloquea edición futura)
     * 
     * Requisitos:
     * - El pago debe estar registrado
     * - Los datos no deben haber sido confirmados previamente
     */
    public function updateAndConfirm(Request $request)
    {
        $validated = $request->validate([
            'dni' => 'required|string|size:8',
            'email' => 'required|email',
            'last_name_father' => 'sometimes|string|max:50',
            'last_name_mother' => 'sometimes|string|max:50',
            'first_names' => 'sometimes|string|max:100',
            'phone_mobile' => 'sometimes|nullable|string|max:20',
            'phone_other' => 'sometimes|nullable|string|max:20',
            'photo' => 'sometimes|nullable|image|mimes:jpeg,jpg,png|max:2048',
        ], [
            'photo.image' => 'El archivo debe ser una imagen',
            'photo.mimes' => 'La foto debe ser formato JPEG, JPG o PNG',
            'photo.max' => 'La foto no debe superar los 2MB',
        ]);

        // Procesar foto si se envió
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $filename = $validated['dni'] . '_' . time() . '.' . $photo->getClientOriginalExtension();
            
            // Obtener simulacro activo para la carpeta
            $activeSimulation = $this->getActiveExamSimulation();
            $simulationId = $activeSimulation?->id ?? 'unknown';
            
            $photoPath = $photo->storeAs('simulation-photos/' . $simulationId, $filename, 'public');
        }

        $dataToUpdate = $request->only(['last_name_father', 'last_name_mother', 'first_names', 'phone_mobile', 'phone_other']);
        
        if ($photoPath) {
            $dataToUpdate['photo_path'] = $photoPath;
        }

        $result = $this->updateAndConfirmApplicantData(
            $validated['dni'],
            $validated['email'],
            $dataToUpdate
        );

        // Si falló y subimos foto, eliminarla
        if (!$result['success'] && $photoPath) {
            Storage::disk('public')->delete($photoPath);
        }

        return response()->json([
            'status' => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
        ], $result['success'] ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);
    }
}
