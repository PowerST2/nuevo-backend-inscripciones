<?php

use App\Models\Payment;
use App\Models\PaymentPortfolio;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Esta migración limpia los pagos (payments) que fueron creados incorrectamente
 * cuando los postulantes se inscribían.
 * 
 * Lógica correcta:
 * - PaymentPortfolio: Deuda/obligación que se crea al inscribirse (se envía al OCEF)
 * - Payment: Ingreso real confirmado, solo se crea cuando se procesa el CSV del banco
 * 
 * Esta migración:
 * 1. Elimina los payments que no tienen un pago real confirmado
 * 2. Desvincula los payment_portfolios de esos payments eliminados
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            // Obtener todos los payments que fueron creados automáticamente al inscribirse
            // (los que no tienen operación bancaria ni fueron verificados)
            $paymentsToDelete = Payment::whereNull('operation')
                ->whereNull('reference')
                ->where(function ($query) {
                    // Payments sin banco o con banco vacío
                    $query->whereNull('bank')
                        ->orWhere('bank', '');
                })
                ->get();

            $count = $paymentsToDelete->count();

            if ($count > 0) {
                // Primero, desvincular los portfolios de estos payments
                PaymentPortfolio::whereIn('payment_id', $paymentsToDelete->pluck('id'))
                    ->update(['payment_id' => null]);

                // Ahora eliminar los payments
                Payment::whereIn('id', $paymentsToDelete->pluck('id'))->delete();

                Log::info("Migración clean_unverified_payments: Eliminados {$count} payments no verificados");
            }
        });
    }

    public function down(): void
    {
        // No se puede revertir esta migración de forma automática
        // Los datos eliminados no se pueden recuperar
        Log::warning('La migración clean_unverified_payments no puede revertirse automáticamente');
    }
};
