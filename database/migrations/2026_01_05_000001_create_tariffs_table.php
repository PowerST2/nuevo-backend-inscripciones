<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tariffs', function (Blueprint $table) {
            $table->id();
            $table->string('code', 5)->unique();
            $table->text('description')->nullable();
            $table->string('item', 10)->nullable()->comment('Partida contable');
            $table->string('project', 10)->nullable()->comment('Proyecto contable');
            $table->decimal('amount', 10, 2)->default(0);
            $table->boolean('active')->default(true);
            $table->integer('sort_order')->nullable()->default(null);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tariffs');
    }
};
