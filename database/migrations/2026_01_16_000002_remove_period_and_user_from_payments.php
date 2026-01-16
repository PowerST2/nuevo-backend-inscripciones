<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Elimina period_id de payments y payment_portfolios, y user_id de payment_portfolios.
 * Los simulacros no necesitan estar ligados a períodos académicos.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Eliminar period_id y user_id de payment_portfolios
        Schema::table('payment_portfolios', function (Blueprint $table) {
            $table->dropForeign(['period_id']);
            $table->dropColumn('period_id');
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        // Eliminar period_id de payments
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['period_id']);
            $table->dropColumn('period_id');
        });
    }

    public function down(): void
    {
        // Restaurar las columnas
        Schema::table('payment_portfolios', function (Blueprint $table) {
            $table->foreignId('period_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('period_id')->nullable()->constrained()->nullOnDelete();
        });
    }
};
