<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_simulations', function (Blueprint $table) {
            $table->id();
            $table->string('code', 100);
            $table->text('description')->nullable();
            $table->date('exam_date_start');
            $table->date('exam_date_end');
            $table->boolean('active')->default(true);
            $table->foreignId('tariff_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_virtual')->default(false)->comment('true = Virtual, false = Presencial');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_simulations');
    }
};
