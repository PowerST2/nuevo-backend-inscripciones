<?php

namespace App\Observers\Simulation;

use App\Models\Simulation\ExamSimulation;

class ExamSimulationObserver
{
    /**
     * Handle the ExamSimulation "creating" event.
     * Antes de crear, si el nuevo registro será activo, desactivar los demás
     */
    public function creating(ExamSimulation $examSimulation): void
    {
        if ($examSimulation->active) {
            $this->deactivateOthers($examSimulation);
        }
    }

    /**
     * Handle the ExamSimulation "updating" event.
     * Antes de actualizar, si se está activando, desactivar los demás
     */
    public function updating(ExamSimulation $examSimulation): void
    {
        // Solo si el campo 'active' cambió a true
        if ($examSimulation->isDirty('active') && $examSimulation->active) {
            $this->deactivateOthers($examSimulation);
        }
    }

    /**
     * Desactivar todos los demás simulacros excepto el actual
     */
    protected function deactivateOthers(ExamSimulation $examSimulation): void
    {
        ExamSimulation::where('id', '!=', $examSimulation->id ?? 0)
            ->where('active', true)
            ->update(['active' => false]);
    }

}
