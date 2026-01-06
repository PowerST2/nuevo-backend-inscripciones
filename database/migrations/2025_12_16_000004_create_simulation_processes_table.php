<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('simulation_processes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('simulation_applicant_id');
            
            // Usamos datetime para que guarde la hora literal (UTC-5) sin conversiones
            $table->dateTime('pre_registration_at')->nullable();
            $table->dateTime('payment_at')->nullable();
            $table->dateTime('photo_at')->nullable();
            $table->dateTime('data_confirmation_at')->nullable();
            $table->dateTime('registration_at')->nullable();
            
            // NOTA: Se eliminó $table->timestamps(); por tu solicitud

            $table->foreign('simulation_applicant_id')
                ->references('id')
                ->on('simulation_applicants')
                ->onDelete('cascade');

            $table->unique('simulation_applicant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('simulation_processes');
    }
};