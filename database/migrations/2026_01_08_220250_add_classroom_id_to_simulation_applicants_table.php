<?php

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
        Schema::table('simulation_applicants', function (Blueprint $table) {
            $table->foreignId('classroom_id')->nullable()->constrained('classrooms')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('simulation_applicants', function (Blueprint $table) {
            $table->dropForeignIdFor('classrooms', 'classroom_id');
            $table->dropColumn('classroom_id');
        });
    }
};
