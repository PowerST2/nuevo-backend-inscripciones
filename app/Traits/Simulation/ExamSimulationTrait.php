<?php

namespace App\Traits\Simulation;

use App\Models\Simulation\ExamSimulation;
use Carbon\Carbon;

trait ExamSimulationTrait
{
    /**
     * Verificar si hay algún simulacro activo (active=true y fecha actual entre exam_date_start y exam_date_end)
     */
    public function isSimulationOpen(): bool
    {
        $today = Carbon::today()->toDateString();
        
        return ExamSimulation::where('active', true)
            ->where('exam_date_start', '<=', $today)
            ->where('exam_date_end', '>=', $today)
            ->exists();
    }

    /**
     * Obtener el simulacro marcado como activo.
     * Por defecto valida que la fecha actual esté dentro del rango; se puede desactivar con $mustBeInDateRange=false.
     */
    public function getActiveSimulation(bool $mustBeInDateRange = true): ?ExamSimulation
    {
        $query = ExamSimulation::where('active', true);

        if ($mustBeInDateRange) {
            $today = Carbon::today()->toDateString();

            $query->where('exam_date_start', '<=', $today)
                ->where('exam_date_end', '>=', $today);
        }

        return $query->first();
    }

    /**
     * Obtener estado del simulacro con información detallada
     */
    public function getSimulationStatus(): array
    {
        $today = Carbon::today()->toDateString();
        
        $activeSimulation = ExamSimulation::where('active', true)
            ->where('exam_date_start', '<=', $today)
            ->where('exam_date_end', '>=', $today)
            ->first();

        if ($activeSimulation) {
            return [
                'is_active' => true,
                'message' => 'Simulacro abierto',
                'simulation' => [
                    'id' => $activeSimulation->id,
                    'code' => $activeSimulation->code,
                    'description' => $activeSimulation->description,
                    'exam_date_start' => $activeSimulation->exam_date_start->format('Y-m-d'),
                    'exam_date_end' => $activeSimulation->exam_date_end->format('Y-m-d'),
                        'exam_date' => $activeSimulation->exam_date?->format('Y-m-d'),
                    'is_virtual' => $activeSimulation->is_virtual,
                ],
            ];
        }

        // Buscar próximo simulacro
        $nextSimulation = ExamSimulation::where('active', true)
            ->where('exam_date_start', '>', $today)
            ->orderBy('exam_date_start', 'asc')
            ->first();
        
        if ($nextSimulation) {
            return [
                'is_active' => false,
                'message' => "Simulacro cerrado. Próxima apertura: {$nextSimulation->exam_date_start->format('d/m/Y')}",
                'simulation' => [
                    'id' => $nextSimulation->id,
                    'code' => $nextSimulation->code,
                    'description' => $nextSimulation->description,
                    'exam_date_start' => $nextSimulation->exam_date_start->format('Y-m-d'),
                    'exam_date_end' => $nextSimulation->exam_date_end->format('Y-m-d'),
                        'exam_date' => $nextSimulation->exam_date?->format('Y-m-d'),
                    'is_virtual' => $nextSimulation->is_virtual,
                ],
            ];
        }

        return [
            'is_active' => false,
            'message' => 'No hay simulacros programados',
            'simulation' => null,
        ];
    }

    /**
     * Verificar si un simulacro específico está activo por código
     */
    public function isSimulationActiveByCode(string $code): bool
    {
        $today = Carbon::today()->toDateString();
        
        return ExamSimulation::where('active', true)
            ->where('code', $code)
            ->where('exam_date_start', '<=', $today)
            ->where('exam_date_end', '>=', $today)
            ->exists();
    }
}
