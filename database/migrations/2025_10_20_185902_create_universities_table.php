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
        Schema::create('universities', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10);
            $table->string('name', 100)->index();
            $table->string('management', 100);
            $table->foreignId('ubigeo_id')->nullable()->references('id')->on('ubigeo');
            $table->foreignId('country_id')->index()->references('id')->on('countries');
            $table->boolean('activo')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('universities');
    }
};
