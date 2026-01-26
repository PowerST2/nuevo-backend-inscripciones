<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agregar tariff_id a simulation_applicants para almacenar la tarifa específica
     * que el postulante seleccionó al momento de su registro
     */
    public function up(): void
    {
        Schema::table('simulation_applicants', function (Blueprint $table) {
            $table->foreignId('tariff_id')
                ->nullable()
                ->after('classroom')
                ->constrained('tariffs')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('simulation_applicants', function (Blueprint $table) {
            $table->dropForeign(['tariff_id']);
            $table->dropColumn('tariff_id');
        });
    }
};
