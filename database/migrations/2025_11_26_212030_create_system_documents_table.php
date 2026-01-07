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
        Schema::create('system_documents', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('path')->nullable();
            $table->string('type');
            $table->boolean('active')->default(true);
            $table->boolean('virtual')->default(false);
            $table->text('text')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_documents');
    }
};
