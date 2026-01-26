<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Remover tariff_id de exam_simulations ya que ahora la relación es many-to-many
     * Los exámenes ahora tendrán múltiples tarifas asociadas
     */
    public function up(): void
    {
        Schema::table('exam_simulations', function (Blueprint $table) {
            $table->dropForeign(['tariff_id']);
            $table->dropColumn('tariff_id');
        });
    }

    public function down(): void
    {
        Schema::table('exam_simulations', function (Blueprint $table) {
            $table->foreignId('tariff_id')
                ->nullable()
                ->after('active')
                ->constrained('tariffs')
                ->nullOnDelete();
        });
    }
};
