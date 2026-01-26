<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('simulation_applicants', function (Blueprint $table) {

            // Nuevos campos
            $table->foreignId('genders_id')
                ->nullable()
                ->after('email')
                ->constrained('genders')
                ->nullOnDelete();

            $table->date('birth_date')
                ->nullable()
                ->after('genders_id');
                
            $table->foreignId('ubigeo_id')
                ->nullable()
                ->after('birth_date')
                ->constrained('ubigeos')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('simulation_applicants', function (Blueprint $table) {

            // Eliminar FK primero
            $table->dropForeign(['ubigeo_id']);

            // Eliminar columnas agregadas
            $table->dropColumn([
                'sex',
                'birth_date',
                'ubigeo_id',
            ]);

            // Volver al nombre original
            $table->renameColumn('document_number', 'dni');
        });
    }
};
