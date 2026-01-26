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
     * Buscar aplicante por UUID
     * GET /api/simulation-applicants/{uuid}
     */
    public function show(string $uuid)
    {
        $applicant = $this->searchByUuid($uuid);

        if (!$applicant) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se encontró un aplicante con el UUID proporcionado',
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'status' => 'success',
            'data' => $applicant,
        ], Response::HTTP_OK);
    }

    /**
     * Verificar si el aplicante ha pagado
     * GET /api/simulation-applicants/{uuid}/has-paid
     */
    public function hasPaid(string $uuid)
    {
        $applicant = $this->getApplicantByUuid($uuid);

        if (!$applicant) {
            return response()->json([
                'status' => 'error',
                'message' => 'Postulante no encontrado en el simulacro activo',
            ], Response::HTTP_NOT_FOUND);
        }

        $hasPaid = $applicant->simulationProcess?->hasPaid() ?? false;

        return response()->json([
            'status' => 'success',
            'has_paid' => $hasPaid,
        ], Response::HTTP_OK);
    }

    /**
     * Obtener estado de la foto del aplicante
     * GET /api/simulation-applicants/{uuid}/photo-status
     */
    public function getPhotoStatus(string $uuid)
    {
        $applicant = $this->getApplicantByUuid($uuid);

        if (!$applicant) {
            return response()->json([
                'status' => 'success',
                'found' => false,
                'photo_url' => null,
                'message' => 'Postulante no encontrado en el simulacro activo',
            ], Response::HTTP_OK);
        }

        $photoStatus = $applicant->simulationProcess?->photo_status;

        $response = [
            'status' => 'success',
            'found' => true,
            'photo_status' => $photoStatus,
            'photo_url' => $applicant->photo_url,
        ];

        // Solo incluir el motivo si la foto fue rechazada
        if ($photoStatus === 'rejected') {
            $response['photo_rejected_reason'] = $applicant->simulationProcess?->photo_rejected_reason;
        }

        return response()->json($response, Response::HTTP_OK);
    }

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
                'status' => 'success',
                'message' => 'Los campos dni y email son obligatorios',
            ], Response::HTTP_OK);
        }

        $applicant = $this->searchByDniAndEmail($dni, $email);

        if (!$applicant) {
            return response()->json([
                'status' => 'success',
                'message' => 'No se encontró un aplicante con los datos proporcionados',
            ], Response::HTTP_OK);
        }

        return response()->json([
            'status' => 'success',
            'data' => $applicant,
        ], Response::HTTP_OK);
    }

    /**
     * Insertar nuevo aplicante al simulacro activo (SIN FOTO)
     * POST /api/simulation-applicants
     * Body JSON: { dni, last_name_father, last_name_mother, first_names, email, phone_mobile, phone_other?, include_vocational }
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

        // Regla para el examen vocacional
        $isVocationalRule = $activeSimulation->include_vocational ? 'required|boolean' : 'nullable|boolean';

        // Validación actualizada
        $validated = $request->validate([
            'dni'                => 'required|string|size:8',
            'last_name_father'   => 'required|string|max:50',
            'last_name_mother'   => 'required|string|max:50',
            'first_names'        => 'required|string|max:100',
            'email'              => 'required|email|max:150',
            'phone_mobile'       => 'required|string|max:15',
            'phone_other'        => 'nullable|string|max:15',
            'include_vocational' => $isVocationalRule,
            
            // --- NUEVOS CAMPOS ---
            // Se validan como nullable porque así están en tu migración.
            // exists:tabla,columna asegura que el ID enviado sea real.
            'genders_id'         => 'required|integer|exists:genders,id',
            'ubigeo_id'          => 'required|integer|exists:ubigeos,id',
            'birth_date'         => 'required|date|before:today', // before:today evita fechas futuras
        ]);

        // Lógica de tarifa (sin cambios)
        $isVocational = $activeSimulation->include_vocational ? (bool) ($validated['include_vocational'] ?? false) : false;
        $selectedTariff = $activeSimulation->getTariffForApplicant($isVocational);

        if (!$selectedTariff) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se encontró una tarifa disponible para este simulacro',
            ], Response::HTTP_BAD_REQUEST);
        }

        // Forzar valores consistentes
        $validated['include_vocational'] = $isVocational;
        $validated['tariff_id'] = $selectedTariff->id;

        // Insertar postulante (asegúrate que este método use create() con los datos validados)
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
     * Subir o actualizar foto del postulante por UUID
     * POST /api/simulation-applicants/{uuid}/upload-photo
     * Content-Type: multipart/form-data
     * Body: { photo }
     */
    public function uploadPhotoByUuid(Request $request, string $uuid)
    {
        $validated = $request->validate([
            'photo' => 'required|image|mimes:jpeg,jpg,png|max:2048',
        ], [
            'photo.required' => 'La foto es obligatoria',
            'photo.image' => 'El archivo debe ser una imagen',
            'photo.mimes' => 'La foto debe ser formato JPEG, JPG o PNG',
            'photo.max' => 'La foto no debe superar los 2MB',
        ]);

        $applicant = $this->getApplicantByUuid($uuid);

        if (!$applicant) {
            return response()->json([
                'status' => 'error',
                'message' => 'Postulante no encontrado en el simulacro activo',
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

        // Guardar nueva foto con formato dni_yymmddhhss
        $photo = $request->file('photo');
        $simulationCode = $applicant->examSimulation->code ?? $applicant->exam_simulation_id;
        $timestamp = now('America/Lima')->format('ymdHis');
        $filename = $applicant->dni . '_' . $timestamp . '.' . $photo->getClientOriginalExtension();
        $photoPath = $photo->storeAs(
            'simulation-photos/' . $simulationCode, 
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
        ], Response::HTTP_OK);
    }

    /**
     * Actualizar datos del aplicante por UUID (sin foto)
     * POST /api/simulation-applicants/{uuid}
     * Body JSON: { last_name_father?, last_name_mother?, first_names?, phone_mobile?, phone_other? }
     */
    public function updateByUuid(Request $request, string $uuid)
    {
        $validated = $request->validate([
            'last_name_father' => 'sometimes|string|max:50',
            'last_name_mother' => 'sometimes|string|max:50',
            'email' => 'sometimes|email|max:100',
            'first_names' => 'sometimes|string|max:100',
            'phone_mobile' => 'sometimes|nullable|string|max:20',
            'phone_other' => 'sometimes|nullable|string|max:20',
            'genders_id' => 'sometimes|integer|exists:genders,id',
            'birth_date' => 'sometimes|date|before:today',
            'ubigeo_id' => 'sometimes|integer|exists:ubigeos,id',
            
        ]);

        $result = $this->updateApplicantByUuid(
            $uuid,
            $request->only(['last_name_father', 'last_name_mother', 'first_names', 'phone_mobile', 'phone_other', 'genders_id', 'birth_date', 'ubigeo_id'])
        );

        return response()->json([
            'status' => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
        ], $result['success'] ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);
    }

    /**
     * Confirmar datos del aplicante por UUID
     * PUT /api/simulation-applicants/confirm
     * Body: { "uuid": "..." }
     */
    public function confirmDataByUuid(Request $request)
    {
        $validated = $request->validate([
            'uuid' => 'required|uuid',
        ]);

        $result = $this->confirmApplicantDataByUuid($validated['uuid']);

        return response()->json([
            'status' => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
        ], $result['success'] ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);
    }

    /**
     * Obtener estado del proceso por UUID
     * GET /api/simulation-applicants/{uuid}/status
     */
    public function getStatusByUuid(string $uuid)
    {
        $result = $this->getProcessStatusByUuid($uuid);

        return response()->json([
            'status' => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
        ], $result['success'] ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
    }

    /**
     * Marcar pago como completado por UUID
     * POST /api/simulation-applicants/{uuid}/mark-payment
     */
    public function markPaymentByUuid(string $uuid)
    {
        $result = $this->markPaymentCompleteByUuid($uuid);

        return response()->json([
            'status' => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
        ], $result['success'] ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);
    }

    /**
     * Completar inscripción por UUID
     * PUT /api/simulation-applicants/complete
     * Body: { "uuid": "..." }
     */
    public function completeByUuid(Request $request)
    {
        $validated = $request->validate([
            'uuid' => 'required|uuid',
        ]);

        $result = $this->completeRegistrationByUuid($validated['uuid']);

        return response()->json([
            'status' => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
        ], $result['success'] ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);
    }

    /**
     * Actualizar datos y confirmar en un solo paso por UUID
     * POST /api/simulation-applicants/{uuid}/update-and-confirm
     * Body JSON: { last_name_father?, last_name_mother?, first_names?, phone_mobile?, phone_other? }
     */
    public function updateAndConfirmByUuid(Request $request, string $uuid)
    {
        $validated = $request->validate([
            'last_name_father' => 'sometimes|string|max:50',
            'last_name_mother' => 'sometimes|string|max:50',
            'first_names' => 'sometimes|string|max:100',
            'phone_mobile' => 'sometimes|nullable|string|max:20',
            'phone_other' => 'sometimes|nullable|string|max:20',
            'genders_id' => 'sometimes|integer|exists:genders,id',
            'birth_date' => 'sometimes|date|before:today',
            'ubigeo_id' => 'sometimes|integer|exists:ubigeos,id',
        ]);

        $result = $this->updateAndConfirmApplicantDataByUuid(
            $uuid,
            $request->only(['last_name_father', 'last_name_mother', 'first_names', 'phone_mobile', 'phone_other','genders_id', 'birth_date', 'ubigeo_id'])
        );

        return response()->json([
            'status' => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
        ], $result['success'] ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);
    }

    /**
     * Subir o actualizar foto del postulante (legacy - usar uploadPhotoByUuid)
     * POST /api/simulation-applicants/upload-photo
     * Content-Type: multipart/form-data
     * Body: { dni, email, photo }
     * @deprecated Use uploadPhotoByUuid instead
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

        // Guardar nueva foto con formato dni_yymmddhhss
        $photo = $request->file('photo');
        $simulationCode = $applicant->examSimulation->code ?? $applicant->exam_simulation_id;
        $timestamp = now('America/Lima')->format('ymdHis');
        $filename = $validated['dni'] . '_' . $timestamp . '.' . $photo->getClientOriginalExtension();
        $photoPath = $photo->storeAs(
            'simulation-photos/' . $simulationCode, 
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
            'genders_id' => 'sometimes|integer|exists:genders,id',
            'birth_date' => 'sometimes|date|before:today',
            'ubigeo_id' => 'sometimes|integer|exists:ubigeos,id',
        ]);

        $result = $this->updateApplicant(
            $validated['dni'],
            $validated['email'],
            $request->only(['last_name_father', 'last_name_mother', 'first_names', 'phone_mobile', 'phone_other', 'genders_id', 'birth_date', 'ubigeo_id'])
        );

        return response()->json([
            'status' => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
        ], $result['success'] ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);
    }

    /**
     * Confirmar datos del aplicante
     * GET /api/simulation-applicants/confirm
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
