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
        Schema::create('modality_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('modality_id')->constrained('modalities')->onDelete('cascade');
            $table->string('document_code', 255);
            $table->string('path_document', 255);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modality_documents');
    }
};
