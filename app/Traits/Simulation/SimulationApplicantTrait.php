<?php

namespace App\Traits\Simulation;

use App\Models\Simulation\ExamSimulation;
use App\Models\Simulation\SimulationApplicant;
use App\Notifications\Simulation\SimulationCompletedNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;

trait SimulationApplicantTrait
{
    /**
     * Buscar aplicante por UUID (solo del simulacro activo)
     * Método principal para las APIs
     */
    public function searchByUuid(string $uuid): ?array
    {
        $activeSimulation = $this->getActiveExamSimulation();

        if (!$activeSimulation) {
            return null;
        }

        $applicant = SimulationApplicant::where('uuid', $uuid)
            ->where('exam_simulation_id', $activeSimulation->id)
            ->with(['simulationProcess', 'examSimulation'])
            ->first();

        if (!$applicant) {
            return null;
        }

        return $this->formatApplicantData($applicant);
    }

    /**
     * Obtener aplicante por UUID (solo del simulacro activo)
     * Retorna el modelo completo
     */
    public function getApplicantByUuid(string $uuid): ?SimulationApplicant
    {
        $activeSimulation = $this->getActiveExamSimulation();

        if (!$activeSimulation) {
            return null;
        }

        return SimulationApplicant::where('uuid', $uuid)
            ->where('exam_simulation_id', $activeSimulation->id)
            ->with(['simulationProcess', 'examSimulation'])
            ->first();
    }

    /**
     * Formatear datos del aplicante para respuesta API
     */
    protected function formatApplicantData(SimulationApplicant $applicant): array
    {
        return [
            'id' => $applicant->id,
            'uuid' => $applicant->uuid,
            'code' => $applicant->code,
            'dni' => $applicant->dni,
            'last_name_father' => $applicant->last_name_father,
            'last_name_mother' => $applicant->last_name_mother,
            'first_names' => $applicant->first_names,
            'email' => $applicant->email,
            'phone_mobile' => $applicant->phone_mobile,
            'phone_other' => $applicant->phone_other,
            'photo_path' => $applicant->photo_path,
            'photo_url' => $applicant->photo_url,
            'include_vocational' => $applicant->include_vocational,
            'exam_description' => $applicant->examSimulation->description,
            'exam_is_virtual' => $applicant->examSimulation->is_virtual,
            'exam_include_vocational' => $applicant->examSimulation->include_vocational,
            'gender' => $applicant->gender?->name,
            'birth_date' => $applicant->birth_date,
            'ubigeo' => $applicant->ubigeo?->description,
            'tariff' => $applicant->tariff ? [
                'id' => $applicant->tariff->id,
                'code' => $applicant->tariff->code,
                'description' => $applicant->tariff->description,
                'amount' => $applicant->tariff->amount,
            ] : null,
            'requires_photo' => $applicant->requiresPhoto(),
            'has_photo' => $applicant->hasPhoto(),
            'classroom_assignment' => $applicant->classroom,
            'process' => $applicant->simulationProcess ? [
                'pre_registration' => $applicant->simulationProcess->pre_registration_at,
                'payment' => $applicant->simulationProcess->payment_at,
                'photo' => $applicant->simulationProcess->photo_at,
                'photo_status' => $applicant->simulationProcess->photo_status,
                'photo_rejected_reason' => $applicant->simulationProcess->photo_rejected_reason,
                'photo_reviewed_at' => $applicant->simulationProcess->photo_reviewed_at,
                'data_confirmation' => $applicant->simulationProcess->data_confirmation_at,
                'registration' => $applicant->simulationProcess->registration_at,
            ] : null,
        ];
    }

    /**
     * Buscar aplicante por DNI y email (solo del simulacro activo)
     * Retorna UUID si existe
     */
    public function searchByDniAndEmail(string $dni, string $email): ?array
    {
        $activeSimulation = $this->getActiveExamSimulation();

        if (!$activeSimulation) {
            return null;
        }

        $applicant = SimulationApplicant::where('dni', $dni)
            ->where('email', $email)
            ->where('exam_simulation_id', $activeSimulation->id)
            ->with(['simulationProcess', 'examSimulation'])
            ->first();

        if (!$applicant) {
            return null;
        }

        return [
            'uuid' => $applicant->uuid,
        ];
    }

    /**
     * Obtener el simulacro activo actual
     */
    public function getActiveExamSimulation(): ?ExamSimulation
    {
        $today = Carbon::today()->toDateString();

        return ExamSimulation::where('active', true)
            ->first();
    }

    public function getActiveExamSimulationInsert(): ?ExamSimulation
    {
        $today = Carbon::today()->toDateString();

        return ExamSimulation::where('active', true)
            ->where('exam_date_start', '<=', $today)
            ->where('exam_date_end', '>=', $today)
            ->first();
    }

    /**
     * Insertar nuevo aplicante al simulacro activo
     */
    public function insertApplicant(array $data): array
    {
        // Obtener simulacro activo
        $activeSimulation = $this->getActiveExamSimulationInsert();

        if (!$activeSimulation) {
            return [
                'success' => false,
                'message' => 'No hay un simulacro activo en este momento',
                'data' => null,
            ];
        }

        // Verificar si ya existe un registro con el mismo DNI y email en el simulacro activo
        $exists = SimulationApplicant::where('exam_simulation_id', $activeSimulation->id)
            ->where(function ($query) use ($data) {
                $query->where('dni', $data['dni'])
                    ->orWhere('email', $data['email']);
            })
            ->exists();

        if ($exists) {
            return [
                'success' => false,
                'message' => 'Ya existe un registro con este DNI y email para el simulacro actual',
                'data' => null,
            ];
        }

        // Crear el aplicante (sin foto - se sube después)
        $applicant = SimulationApplicant::create([
            'dni' => $data['dni'],
            'last_name_father' => $data['last_name_father'],
            'last_name_mother' => $data['last_name_mother'],
            'first_names' => $data['first_names'],
            'email' => $data['email'] ?? null,
            'phone_mobile' => $data['phone_mobile'] ?? null,
            'phone_other' => $data['phone_other'] ?? null,
            'exam_simulation_id' => $activeSimulation->id,
            'include_vocational' => $data['include_vocational'] ?? false,
            'tariff_id' => $data['tariff_id'],
            'genders_id' => $data['genders_id'] ?? null,
            'birth_date' => $data['birth_date'] ?? null,
            'ubigeo_id' => $data['ubigeo_id'] ?? null,
        ]);

        return [
            'success' => true,
            'message' => 'Aplicante registrado exitosamente. ' . ($activeSimulation->requiresPhoto() ? 'Recuerde subir su foto.' : ''),
            'data' => $this->searchByUuid($applicant->uuid),
        ];
    }

    /**
     * Actualizar datos del aplicante por UUID (solo si puede editar) - SIN FOTO
     */
    public function updateApplicantByUuid(string $uuid, array $data): array
    {
        $applicant = $this->getApplicantByUuid($uuid);

        if (!$applicant) {
            return [
                'success' => false,
                'message' => 'Aplicante no encontrado en el simulacro activo',
                'data' => null,
            ];
        }

        // Verificar si puede editar datos
        if ($applicant->simulationProcess && !$applicant->simulationProcess->canEditData()) {
            return [
                'success' => false,
                'message' => 'No puede editar sus datos después de confirmarlos',
                'data' => null,
            ];
        }

        // Campos permitidos para actualizar (sin photo_path - eso va por API separada)
        $allowedFields = [
            'last_name_father',
            'last_name_mother',
            'first_names',
            'phone_mobile',
            'phone_other',
            'genders_id',
            'ubigeo_id',
            'birth_date',
        ];

        $updateData = array_intersect_key($data, array_flip($allowedFields));
        $applicant->update($updateData);

        return [
            'success' => true,
            'message' => 'Datos actualizados exitosamente',
            'data' => $this->searchByUuid($uuid),
        ];
    }

    /**
     * Actualizar datos del aplicante (solo si puede editar) - SIN FOTO
     * @deprecated Use updateApplicantByUuid instead
     */
    public function updateApplicant(string $dni, string $email, array $data): array
    {
        $applicant = SimulationApplicant::where('dni', $dni)
            ->where('email', $email)
            ->with('simulationProcess')
            ->first();

        if (!$applicant) {
            return [
                'success' => false,
                'message' => 'Aplicante no encontrado',
                'data' => null,
            ];
        }

        // Verificar si puede editar datos
        if ($applicant->simulationProcess && !$applicant->simulationProcess->canEditData()) {
            return [
                'success' => false,
                'message' => 'No puede editar sus datos después de confirmarlos',
                'data' => null,
            ];
        }

        // Campos permitidos para actualizar (sin photo_path - eso va por API separada)
        $allowedFields = [
            'last_name_father',
            'last_name_mother',
            'first_names',
            'phone_mobile',
            'phone_other',
            'genders_id',
            'ubigeo_id',
            'birth_date',
        ];

        $updateData = array_intersect_key($data, array_flip($allowedFields));
        $applicant->update($updateData);

        return [
            'success' => true,
            'message' => 'Datos actualizados exitosamente',
            'data' => $this->searchByDniAndEmail($dni, $email),
        ];
    }

    /**
     * Confirmar datos del aplicante por UUID (bloquea edición)
     */
    public function confirmApplicantDataByUuid(string $uuid): array
    {
        $applicant = $this->getApplicantByUuid($uuid);

        if (!$applicant) {
            return [
                'success' => false,
                'message' => 'Aplicante no encontrado en el simulacro activo',
            ];
        }

        if (!$applicant->simulationProcess) {
            return [
                'success' => false,
                'message' => 'Proceso de simulacro no encontrado',
            ];
        }

        // Verificar que haya pagado
        if (!$applicant->simulationProcess->hasPaid()) {
            return [
                'success' => false,
                'message' => 'Debe completar el pago antes de confirmar sus datos',
            ];
        }

        // Verificar que haya subido foto si es presencial
        if ($applicant->requiresPhoto() && !$applicant->simulationProcess->hasUploadedPhoto()) {
            return [
                'success' => false,
                'message' => 'Debe subir su foto antes de confirmar sus datos (simulacro presencial)',
            ];
        }

        // Verificar que la foto esté aprobada si es presencial
        if ($applicant->requiresPhoto() && !$applicant->simulationProcess->isPhotoApproved()) {
            $status = $applicant->simulationProcess->photo_status;
            if ($status === 'pending') {
                return [
                    'success' => false,
                    'message' => 'Su foto está pendiente de revisión. Espere a que sea aprobada para continuar.',
                ];
            } elseif ($status === 'rejected') {
                return [
                    'success' => false,
                    'message' => 'Su foto fue rechazada: ' . ($applicant->simulationProcess->photo_rejected_reason ?? 'Sin motivo especificado') . '. Debe subir una nueva foto.',
                ];
            }
        }

        // Verificar si ya confirmó
        if (!is_null($applicant->simulationProcess->data_confirmation_at)) {
            return [
                'success' => false,
                'message' => 'Los datos ya fueron confirmados anteriormente',
            ];
        }

        // Confirmar datos (solo data_confirmation, NO registration)
        $applicant->simulationProcess->confirmData();

        return [
            'success' => true,
            'message' => 'Datos confirmados exitosamente',
            'data' => $this->searchByUuid($uuid),
        ];
    }

    /**
     * Confirmar datos del aplicante (bloquea edición)
     * @deprecated Use confirmApplicantDataByUuid instead
     */
    public function confirmApplicantData(string $dni, string $email): array
    {
        $applicant = SimulationApplicant::where('dni', $dni)
            ->where('email', $email)
            ->with(['simulationProcess', 'examSimulation'])
            ->first();

        if (!$applicant) {
            return [
                'success' => false,
                'message' => 'Aplicante no encontrado',
            ];
        }

        if (!$applicant->simulationProcess) {
            return [
                'success' => false,
                'message' => 'Proceso de simulacro no encontrado',
            ];
        }

        // Verificar que haya pagado
        if (!$applicant->simulationProcess->hasPaid()) {
            return [
                'success' => false,
                'message' => 'Debe completar el pago antes de confirmar sus datos',
            ];
        }

        // Verificar que haya subido foto si es presencial
        if ($applicant->requiresPhoto() && !$applicant->simulationProcess->hasUploadedPhoto()) {
            return [
                'success' => false,
                'message' => 'Debe subir su foto antes de confirmar sus datos (simulacro presencial)',
            ];
        }

        // Verificar si ya confirmó
        if (!is_null($applicant->simulationProcess->data_confirmation_at)) {
            return [
                'success' => false,
                'message' => 'Los datos ya fueron confirmados anteriormente',
            ];
        }

        // Confirmar datos (solo data_confirmation, NO registration)
        $applicant->simulationProcess->confirmData();

        return [
            'success' => true,
            'message' => 'Datos confirmados exitosamente',
            'data' => $this->searchByDniAndEmail($dni, $email),
        ];
    }

    /**
     * Actualizar datos y confirmar en un solo paso
     * 
     * Este método permite al postulante:
     * 1. Modificar sus datos personales (si aún puede editarlos)
     * 2. Confirmar que los datos son correctos (bloquea edición futura)
     * 
     * Requisitos:
     * - El pago debe estar registrado
     * - La foto debe estar subida (si es simulacro presencial)
     * - Los datos no deben haber sido confirmados previamente
     */
    public function updateAndConfirmApplicantData(string $dni, string $email, array $data): array
    {
        $applicant = SimulationApplicant::where('dni', $dni)
            ->where('email', $email)
            ->with(['simulationProcess', 'examSimulation'])
            ->first();

        if (!$applicant) {
            return [
                'success' => false,
                'message' => 'Aplicante no encontrado',
            ];
        }

        if (!$applicant->simulationProcess) {
            return [
                'success' => false,
                'message' => 'Proceso de simulacro no encontrado',
            ];
        }

        // Verificar que haya pagado
        if (!$applicant->simulationProcess->hasPaid()) {
            return [
                'success' => false,
                'message' => 'Debe completar el pago antes de confirmar sus datos',
            ];
        }

        // Verificar si ya confirmó (no puede editar ni confirmar de nuevo)
        if (!$applicant->simulationProcess->canEditData()) {
            return [
                'success' => false,
                'message' => 'Los datos ya fueron confirmados anteriormente y no pueden ser modificados',
            ];
        }

        // Verificar que haya subido foto si es presencial
        if ($applicant->requiresPhoto() && !$applicant->simulationProcess->hasUploadedPhoto()) {
            return [
                'success' => false,
                'message' => 'Debe subir su foto antes de confirmar sus datos (simulacro presencial)',
            ];
        }

        // Verificar que la foto esté aprobada si es presencial
        if ($applicant->requiresPhoto() && !$applicant->simulationProcess->isPhotoApproved()) {
            $status = $applicant->simulationProcess->photo_status;
            if ($status === 'pending') {
                return [
                    'success' => false,
                    'message' => 'Su foto está pendiente de revisión. Espere a que sea aprobada para continuar.',
                ];
            } elseif ($status === 'rejected') {
                return [
                    'success' => false,
                    'message' => 'Su foto fue rechazada: ' . ($applicant->simulationProcess->photo_rejected_reason ?? 'Sin motivo especificado') . '. Debe subir una nueva foto.',
                ];
            }
        }

        // Actualizar datos si se proporcionaron (sin foto - eso va por API separada)
        $allowedFields = [
            'last_name_father',
            'last_name_mother',
            'first_names',
            'phone_mobile',
            'phone_other',
            'genders_id',
            'ubigeo_id',
            'birth_date',
        ];

        $updateData = array_filter(
            array_intersect_key($data, array_flip($allowedFields)),
            fn($value) => !is_null($value) && $value !== ''
        );

        if (!empty($updateData)) {
            $applicant->update($updateData);
        }

        // Confirmar datos
        $applicant->simulationProcess->confirmData();

        return [
            'success' => true,
            'message' => 'Datos actualizados y confirmados exitosamente',
            'data' => $this->searchByDniAndEmail($dni, $email),
        ];
    }

    /**
     * Actualizar datos y confirmar en un solo paso por UUID
     */
    public function updateAndConfirmApplicantDataByUuid(string $uuid, array $data): array
    {
        $applicant = $this->getApplicantByUuid($uuid);

        if (!$applicant) {
            return [
                'success' => false,
                'message' => 'Aplicante no encontrado en el simulacro activo',
            ];
        }

        if (!$applicant->simulationProcess) {
            return [
                'success' => false,
                'message' => 'Proceso de simulacro no encontrado',
            ];
        }

        // Verificar que haya pagado
        if (!$applicant->simulationProcess->hasPaid()) {
            return [
                'success' => false,
                'message' => 'Debe completar el pago antes de confirmar sus datos',
            ];
        }

        // Verificar si ya confirmó (no puede editar ni confirmar de nuevo)
        if (!$applicant->simulationProcess->canEditData()) {
            return [
                'success' => false,
                'message' => 'Los datos ya fueron confirmados anteriormente y no pueden ser modificados',
            ];
        }

        // Verificar que haya subido foto si es presencial
        if ($applicant->requiresPhoto() && !$applicant->simulationProcess->hasUploadedPhoto()) {
            return [
                'success' => false,
                'message' => 'Debe subir su foto antes de confirmar sus datos (simulacro presencial)',
            ];
        }

        // Verificar que la foto esté aprobada si es presencial
        if ($applicant->requiresPhoto() && !$applicant->simulationProcess->isPhotoApproved()) {
            $status = $applicant->simulationProcess->photo_status;
            if ($status === 'pending') {
                return [
                    'success' => false,
                    'message' => 'Su foto está pendiente de revisión. Espere a que sea aprobada para continuar.',
                ];
            } elseif ($status === 'rejected') {
                return [
                    'success' => false,
                    'message' => 'Su foto fue rechazada: ' . ($applicant->simulationProcess->photo_rejected_reason ?? 'Sin motivo especificado') . '. Debe subir una nueva foto.',
                ];
            }
        }

        // Actualizar datos si se proporcionaron
        $allowedFields = [
            'last_name_father',
            'last_name_mother',
            'first_names',
            'phone_mobile',
            'phone_other',
            'genders_id',
            'ubigeo_id',
            'birth_date',
        ];

        $updateData = array_filter(
            array_intersect_key($data, array_flip($allowedFields)),
            fn($value) => !is_null($value) && $value !== ''
        );

        if (!empty($updateData)) {
            $applicant->update($updateData);
        }

        // Confirmar datos
        $applicant->simulationProcess->confirmData();

        return [
            'success' => true,
            'message' => 'Datos actualizados y confirmados exitosamente',
            'data' => $this->searchByUuid($uuid),
        ];
    }

    /**
     * Completar inscripción por UUID - El postulante hace clic en el botón final
     */
    public function completeRegistrationByUuid(string $uuid): array
    {
        $applicant = $this->getApplicantByUuid($uuid);

        if (!$applicant) {
            return [
                'success' => false,
                'message' => 'Aplicante no encontrado en el simulacro activo',
            ];
        }

        if (!$applicant->simulationProcess) {
            return [
                'success' => false,
                'message' => 'Proceso de simulacro no encontrado',
            ];
        }

        // Verificar que haya pagado
        if (!$applicant->simulationProcess->hasPaid()) {
            return [
                'success' => false,
                'message' => 'Debe completar el pago antes de finalizar la inscripción',
            ];
        }

        // Verificar que haya confirmado datos
        if (is_null($applicant->simulationProcess->data_confirmation_at)) {
            return [
                'success' => false,
                'message' => 'Debe confirmar sus datos antes de finalizar la inscripción',
            ];
        }

        // Verificar si ya completó la inscripción
        if (!is_null($applicant->simulationProcess->registration_at)) {
            return [
                'success' => false,
                'message' => 'La inscripción ya fue completada anteriormente',
            ];
        }

        // Completar inscripción
        $applicant->simulationProcess->completeRegistration();

        // Refrescar el applicant para obtener el código generado
        $applicant->refresh();

        // Enviar correo con datos del postulante y simulacro
        $this->sendCompletedNotification($applicant);

        return [
            'success' => true,
            'message' => 'Inscripción completada exitosamente. Se ha enviado un correo con tu código: ' . $applicant->code,
            'data' => $this->searchByUuid($uuid),
        ];
    }

    /**
     * Marcar pago como completado por UUID
     */
    public function markPaymentCompleteByUuid(string $uuid): array
    {
        $applicant = $this->getApplicantByUuid($uuid);

        if (!$applicant) {
            return [
                'success' => false,
                'message' => 'Aplicante no encontrado en el simulacro activo',
            ];
        }

        if (!$applicant->simulationProcess) {
            return [
                'success' => false,
                'message' => 'Proceso de simulacro no encontrado',
            ];
        }

        if (!is_null($applicant->simulationProcess->payment_at)) {
            return [
                'success' => false,
                'message' => 'El pago ya fue registrado anteriormente',
            ];
        }

        $applicant->simulationProcess->markPaymentComplete();

        return [
            'success' => true,
            'message' => 'Pago registrado exitosamente',
            'data' => $this->searchByUuid($uuid),
        ];
    }

    /**
     * Verificar estado del proceso de un aplicante por UUID
     */
    public function getProcessStatusByUuid(string $uuid): array
    {
        $applicant = $this->getApplicantByUuid($uuid);

        if (!$applicant) {
            return [
                'success' => false,
                'message' => 'Aplicante no encontrado en el simulacro activo',
                'data' => null,
            ];
        }

        $process = $applicant->simulationProcess;

        return [
            'success' => true,
            'message' => 'Estado del proceso obtenido exitosamente',
            'data' => [
                'process' => $process ? [
                    'pre_registration' => $process->pre_registration_at ? $process->pre_registration_at->format('Y-m-d H:i') : null,
                    'payment' => $process->payment_at ? $process->payment_at->format('Y-m-d H:i') : null,
                    'photo_reviewed_at' => $process->photo_reviewed_at ? $process->photo_reviewed_at->format('Y-m-d H:i') : null,
                    'confirmation' => $process->data_confirmation_at ? $process->data_confirmation_at->format('Y-m-d H:i') : null,
                ] : null,
            ],
        ];
    }

    /**
     * Completar inscripción - El postulante hace clic en el botón final
     * Marca registration como true y envía el correo con todos los datos
     * @deprecated Use completeRegistrationByUuid instead
     */
    public function completeRegistration(string $dni, string $email): array
    {
        $applicant = SimulationApplicant::where('dni', $dni)
            ->where('email', $email)
            ->with(['simulationProcess', 'examSimulation'])
            ->first();

        if (!$applicant) {
            return [
                'success' => false,
                'message' => 'Aplicante no encontrado',
            ];
        }

        if (!$applicant->simulationProcess) {
            return [
                'success' => false,
                'message' => 'Proceso de simulacro no encontrado',
            ];
        }

        // Verificar que haya pagado
        if (!$applicant->simulationProcess->hasPaid()) {
            return [
                'success' => false,
                'message' => 'Debe completar el pago antes de finalizar la inscripción',
            ];
        }

        // Verificar que haya confirmado datos
        if (is_null($applicant->simulationProcess->data_confirmation_at)) {
            return [
                'success' => false,
                'message' => 'Debe confirmar sus datos antes de finalizar la inscripción',
            ];
        }

        // Verificar si ya completó la inscripción
        if (!is_null($applicant->simulationProcess->registration_at)) {
            return [
                'success' => false,
                'message' => 'La inscripción ya fue completada anteriormente',
            ];
        }

        // Completar inscripción
        $applicant->simulationProcess->completeRegistration();

        // Refrescar el applicant para obtener el código generado
        $applicant->refresh();

        // Enviar correo con datos del postulante y simulacro
        $this->sendCompletedNotification($applicant);

        return [
            'success' => true,
            'message' => 'Inscripción completada exitosamente. Se ha enviado un correo con tu código: ' . $applicant->code,
            'data' => $this->searchByDniAndEmail($dni, $email),
        ];
    }

    /**
     * Marcar pago como completado (para uso interno/webhook de pagos)
     */
    public function markPaymentComplete(string $dni, string $email): array
    {
        $applicant = SimulationApplicant::where('dni', $dni)
            ->where('email', $email)
            ->with('simulationProcess')
            ->first();

        if (!$applicant) {
            return [
                'success' => false,
                'message' => 'Aplicante no encontrado',
            ];
        }

        if (!$applicant->simulationProcess) {
            return [
                'success' => false,
                'message' => 'Proceso de simulacro no encontrado',
            ];
        }

        if (!is_null($applicant->simulationProcess->payment_at)) {
            return [
                'success' => false,
                'message' => 'El pago ya fue registrado anteriormente',
            ];
        }

        $applicant->simulationProcess->markPaymentComplete();

        return [
            'success' => true,
            'message' => 'Pago registrado exitosamente',
            'data' => $this->searchByDniAndEmail($dni, $email),
        ];
    }

    /**
     * Verificar estado del proceso de un aplicante
     */
    public function getProcessStatus(string $dni, string $email): array
    {
        $applicant = SimulationApplicant::where('dni', $dni)
            ->where('email', $email)
            ->with(['simulationProcess', 'examSimulation'])
            ->first();

        if (!$applicant) {
            return [
                'success' => false,
                'message' => 'Aplicante no encontrado',
                'data' => null,
            ];
        }

        $process = $applicant->simulationProcess;

        return [
            'success' => true,
            'message' => 'Estado del proceso obtenido',
            'data' => [
                'code' => $applicant->code,
                'full_name' => $applicant->getFullNameAttribute(),
                'exam_simulation' => $applicant->examSimulation->description,
                'can_edit_data' => $process ? $process->canEditData() : false,
                'is_complete' => $process ? $process->isComplete() : false,
                'steps' => [
                    'pre_registration' => [
                        'completed' => !is_null($process?->pre_registration_at),
                        'completed_at' => $process?->pre_registration_at?->format('d/m/Y H:i'),
                        'label' => 'Pre-inscripción',
                    ],
                    'payment' => [
                        'completed' => !is_null($process?->payment_at),
                        'completed_at' => $process?->payment_at?->format('d/m/Y H:i'),
                        'label' => 'Pago',
                    ],
                    'data_confirmation' => [
                        'completed' => !is_null($process?->data_confirmation_at),
                        'completed_at' => $process?->data_confirmation_at?->format('d/m/Y H:i'),
                        'label' => 'Confirmación de datos',
                    ],
                    'registration' => [
                        'completed' => !is_null($process?->registration_at),
                        'completed_at' => $process?->registration_at?->format('d/m/Y H:i'),
                        'label' => 'Inscripción',
                    ],
                ],
            ],
        ];
    }

    /**
     * Enviar correo de inscripción completada con datos del postulante y simulacro
     */
    protected function sendCompletedNotification(SimulationApplicant $applicant): void
    {
        try {
            Notification::route('mail', $applicant->email)
                ->notify(new SimulationCompletedNotification($applicant));
        } catch (\Exception $e) {
            \Log::error('Error enviando email de inscripción completada: ' . $e->getMessage());
        }
    }
}
