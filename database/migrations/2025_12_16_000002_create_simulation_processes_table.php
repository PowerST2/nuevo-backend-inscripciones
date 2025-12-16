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
            $table->boolean('pre_registration')->default(false);
            $table->boolean('payment')->default(false);
            $table->boolean('data_confirmation')->default(false);
            $table->boolean('registration')->default(false);
            $table->timestamps();

            $table->foreign('simulation_applicant_id')
                ->references('id')
                ->on('simulation_applicants')
                ->onDelete('cascade');

            // Un applicant solo puede tener un proceso
            $table->unique('simulation_applicant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('simulation_processes');
    }
};
