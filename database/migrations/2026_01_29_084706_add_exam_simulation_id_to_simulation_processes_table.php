<?php

use App\Models\Simulation\SimulationProcess;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('simulation_processes', function (Blueprint $table) {
            $table->foreignId('exam_simulation_id')
                ->nullable()
                ->after('simulation_applicant_id')
                ->constrained('exam_simulations')
                ->onDelete('cascade');
        });

        // Poblar el campo con los valores existentes basados en la relación
        SimulationProcess::with('simulationApplicant')->chunk(100, function ($processes) {
            foreach ($processes as $process) {
                if ($process->simulationApplicant) {
                    $process->exam_simulation_id = $process->simulationApplicant->exam_simulation_id;
                    $process->save();
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('simulation_processes', function (Blueprint $table) {
            $table->dropForeign(['exam_simulation_id']);
            $table->dropColumn('exam_simulation_id');
        });
    }
};
