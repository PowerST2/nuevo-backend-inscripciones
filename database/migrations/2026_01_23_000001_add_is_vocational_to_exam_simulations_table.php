<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exam_simulations', function (Blueprint $table) {
            $table->boolean('include_vocational')->default(false)->comment('true = Vocacional, false = Académico')->after('is_virtual');});
    }

    public function down(): void
    {
        Schema::table('exam_simulations', function (Blueprint $table) {
            $table->dropColumn('include_vocational');
        });
    }
};