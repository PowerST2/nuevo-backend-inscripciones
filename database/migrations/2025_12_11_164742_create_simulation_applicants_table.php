<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('simulation_applicants', function (Blueprint $table) {
            $table->id();
            $table->string('dni', 8);
            $table->string('code', 20)->nullable()->unique();
            $table->string('last_name_father', 50);
            $table->string('last_name_mother', 50);
            $table->string('first_names', 100);
            $table->string('email', 150)->nullable();
            $table->string('phone_mobile', 20)->nullable();
            $table->string('phone_other', 20)->nullable();
            $table->string('photo_path', 255)->nullable()->comment('Ruta de la foto del postulante (obligatorio si es presencial)');
            // Foreign key to exam_simulations
            $table->unsignedBigInteger('exam_simulation_id');
            $table->foreign('exam_simulation_id')->references('id')->on('exam_simulations')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('simulation_applicants');
    }
};
