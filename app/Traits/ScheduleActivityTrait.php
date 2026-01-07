<?php

namespace App\Traits;

use App\Models\ScheduleActivity;
use Carbon\Carbon;

trait ScheduleActivityTrait
{
    /**
     * Verificar si el calendario de inscripción está activo
     */
    public function isRegistrationOpen(): bool
    {
        $now = Carbon::now();
        $schedule = ScheduleActivity::where('active', true)
            ->where('start_time', '<=', $now)
            ->where('end_time', '>=', $now)
            ->exists();

        return $schedule;
    }

    /**
     * Obtener el calendario activo actual
     */
    public function getActiveSchedule(): ?ScheduleActivity
    {
        $now = Carbon::now();
        return ScheduleActivity::where('active', true)
            ->where('start_time', '<=', $now)
            ->where('end_time', '>=', $now)
            ->first();
    }

    /**
     * Obtener mensaje de estado de inscripción
     */
    public function getRegistrationStatus(): array
    {
        $now = Carbon::now();
        $activeSchedule = ScheduleActivity::where('active', true)
            ->where('start_time', '<=', $now)
            ->where('end_time', '>=', $now)
            ->first();

        if ($activeSchedule) {
            return [
                'open' => true,
                'message' => 'Inscripción abierta',
                'schedule' => $activeSchedule,
            ];
        }

        $nextSchedule = ScheduleActivity::where('active', true)
            ->where('start_time', '>', $now)
            ->orderBy('start_time', 'asc')
            ->first();

        if ($nextSchedule) {
            return [
                'open' => false,
                'message' => "Inscripción cerrada. Próxima apertura: {$nextSchedule->start_time->format('d/m/Y H:i')}",
                'schedule' => $nextSchedule,
            ];
        }

        return [
            'open' => false,
            'message' => 'No hay calendarios de inscripción programados',
            'schedule' => null,
        ];
    }

    /**
     * Verificar si una actividad específica está activa por nombre
     */
    public function isActivityActive(string $activityName): bool
    {
        $now = Carbon::now();
        $schedule = ScheduleActivity::where('active', true)
            ->where('name', $activityName)
            ->where('start_time', '<=', $now)
            ->where('end_time', '>=', $now)
            ->exists();

        return $schedule;
    }
}