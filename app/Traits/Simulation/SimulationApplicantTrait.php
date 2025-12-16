<?php

namespace App\Traits\Simulation;

use App\Models\Simulation\ExamSimulation;
use App\Models\Simulation\SimulationApplicant;
use Carbon\Carbon;

trait SimulationApplicantTrait
{
    /**
     * Buscar aplicante por DNI y email (ambos obligatorios)
     * Retorna datos sin teléfonos
     */
    public function searchByDniAndEmail(string $dni, string $email): ?array
    {
        $applicant = SimulationApplicant::where('dni', $dni)
            ->where('email', $email)
            ->with('simulationProcess')
            ->first();

        if (!$applicant) {
            return null;
        }

        return [
            'id' => $applicant->id,
            'code' => $applicant->code,
            'dni' => $applicant->dni,
            'last_name_father' => $applicant->last_name_father,
            'last_name_mother' => $applicant->last_name_mother,
            'first_names' => $applicant->first_names,
            'email' => $applicant->email,
            'exam_description' => $applicant->examSimulation->description,
            'process' => $applicant->simulationProcess ? [
                'pre_registration' => $applicant->simulationProcess->pre_registration,
                'payment' => $applicant->simulationProcess->payment,
                'data_confirmation' => $applicant->simulationProcess->data_confirmation,
                'registration' => $applicant->simulationProcess->registration,
            ] : null,
        ];
    }

    /**
     * Obtener el simulacro activo actual
     */
    public function getActiveExamSimulation(): ?ExamSimulation
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
        $activeSimulation = $this->getActiveExamSimulation();

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

        // Crear el aplicante
        $applicant = SimulationApplicant::create([
            'dni' => $data['dni'],
            'last_name_father' => $data['last_name_father'],
            'last_name_mother' => $data['last_name_mother'],
            'first_names' => $data['first_names'],
            'email' => $data['email'] ?? null,
            'phone_mobile' => $data['phone_mobile'] ?? null,
            'phone_other' => $data['phone_other'] ?? null,
            'exam_simulation_id' => $activeSimulation->id,
        ]);

        return [
            'success' => true,
            'message' => 'Aplicante registrado exitosamente',
        ];
    }
}
