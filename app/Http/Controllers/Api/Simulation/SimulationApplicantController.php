<?php

namespace App\Http\Controllers\Api\Simulation;

use App\Models\Simulation\ExamSimulation;
use App\Models\Simulation\SimulationApplicant;
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
     * Insertar nuevo aplicante al simulacro activo (SIN FOTO)
     * POST /api/simulation-applicants
     * Body JSON: { dni, last_name_father, last_name_mother, first_names, email, phone_mobile, phone_other? }
     */
    public function store(Request $request)
    {
        $activeSimulation = $this->getActiveExamSimulation();
        
        if (!$activeSimulation) {
            return response()->json([
                'status' => 'error',
                'message' => 'No hay un simulacro activo en este momento',
            ], Response::HTTP_BAD_REQUEST);
        }

        $validated = $request->validate([
            'dni' => 'required|string|size:8',
            'last_name_father' => 'required|string|max:50',
            'last_name_mother' => 'required|string|max:50',
            'first_names' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'phone_mobile' => 'required|string|max:15',
            'phone_other' => 'nullable|string|max:15',
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
     * Subir o actualizar foto del postulante
     * POST /api/simulation-applicants/upload-photo
     * Content-Type: multipart/form-data
     * Body: { dni, email, photo }
     */
    public function uploadPhoto(Request $request)
    {
        $validated = $request->validate([
            'dni' => 'required|string|size:8',
            'email' => 'required|email',
            'photo' => 'required|image|mimes:jpeg,jpg,png|max:2048',
        ], [
            'photo.required' => 'La foto es obligatoria',
            'photo.image' => 'El archivo debe ser una imagen',
            'photo.mimes' => 'La foto debe ser formato JPEG, JPG o PNG',
            'photo.max' => 'La foto no debe superar los 2MB',
        ]);

        $applicant = SimulationApplicant::where('dni', $validated['dni'])
            ->where('email', $validated['email'])
            ->with(['simulationProcess', 'examSimulation'])
            ->first();

        if (!$applicant) {
            return response()->json([
                'status' => 'error',
                'message' => 'Postulante no encontrado',
            ], Response::HTTP_NOT_FOUND);
        }

        // Verificar si puede editar (no ha confirmado datos)
        if ($applicant->simulationProcess && !$applicant->simulationProcess->canEditData()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No puede actualizar la foto después de confirmar sus datos',
            ], Response::HTTP_BAD_REQUEST);
        }

        // Eliminar foto anterior si existe
        if ($applicant->photo_path) {
            Storage::disk('public')->delete($applicant->photo_path);
        }

        // Guardar nueva foto
        $photo = $request->file('photo');
        $filename = $validated['dni'] . '_' . time() . '.' . $photo->getClientOriginalExtension();
        $photoPath = $photo->storeAs(
            'simulation-photos/' . $applicant->exam_simulation_id, 
            $filename, 
            'public'
        );

        // Actualizar postulante
        $applicant->photo_path = $photoPath;
        $applicant->save();

        // Marcar en el proceso que subió foto
        if ($applicant->simulationProcess) {
            $applicant->simulationProcess->markPhotoUploaded();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Foto subida exitosamente',
            'data' => $this->searchByDniAndEmail($validated['dni'], $validated['email']),
        ], Response::HTTP_OK);
    }

    /**
     * Actualizar datos del aplicante (sin foto)
     * PUT /api/simulation-applicants/update
     * Body JSON: { dni, email, last_name_father?, last_name_mother?, first_names?, phone_mobile?, phone_other? }
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
     * Body JSON: { dni, email }
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
     * Body JSON: { dni, email }
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
     * Body JSON: { dni, email }
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
     * Body JSON: { dni, email, last_name_father?, last_name_mother?, first_names?, phone_mobile?, phone_other? }
     * 
     * Requisitos:
     * - El pago debe estar registrado
     * - La foto debe estar subida (si es simulacro presencial)
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
        ]);

        $result = $this->updateAndConfirmApplicantData(
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
}
