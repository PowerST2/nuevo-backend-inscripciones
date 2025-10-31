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
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->index();
            $table->integer('annexed')->nullable();
            $table->string('level', 100)->nullable();
            $table->string('nombre', 100)->index()->nullable();
            $table->string('management_minedu', 100)->nullable();
            $table->string('management', 100)->index();
            $table->string('director')->nullable();
            $table->string('direction')->nullable();
            $table->foreignId('ubigeo_id')->nullable()->references('id')->on('ubigeos');
            $table->foreignId('pais_id')->index()->nullable()->references('id')->on('countries');
            $table->unique(['code', 'annexed']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
