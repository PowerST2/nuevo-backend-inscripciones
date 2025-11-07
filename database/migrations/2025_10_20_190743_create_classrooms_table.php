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
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10);
            $table->string('sector', 10);
            $table->integer('capacity');
            $table->integer('available_1');
            $table->integer('assigned_1');
            $table->integer('available_2');
            $table->integer('assigned_2');
            $table->integer('available_3');
            $table->integer('assigned_3');
            $table->integer('available_voca');
            $table->integer('assigned_voca');
            $table->boolean('active')->default(true);
            $table->boolean('special')->default(true);
            $table->boolean('vocational')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classrooms');
    }
};
