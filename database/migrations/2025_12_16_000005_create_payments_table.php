<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('receipt', 100)->comment('Número de recibo/boleta');
            $table->string('service_code', 5)->nullable()->comment('Código de servicio/tarifa');
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2)->default(0);
            $table->date('payment_date')->nullable();
            $table->string('document_number', 11)->nullable()->comment('DNI/RUC del cliente');
            $table->string('client_name', 100)->nullable();
            $table->string('client_email', 100)->nullable();
            $table->string('bank', 100)->nullable();
            $table->string('reference', 100)->nullable()->comment('Referencia bancaria');
            $table->string('operation', 100)->nullable()->comment('Número de operación');
            
            // Relaciones polimórficas para soportar diferentes tipos de postulantes
            $table->string('payable_type')->nullable()->comment('Tipo de modelo: SimulationApplicant, Applicant, etc.');
            $table->unsignedBigInteger('payable_id')->nullable()->comment('ID del postulante');
            
            // Periodo y proceso
            $table->foreignId('period_id')->nullable()->constrained()->nullOnDelete();
            $table->string('process_type')->nullable()->comment('Tipo de proceso: simulation, admission, etc.');
            $table->unsignedBigInteger('process_id')->nullable()->comment('ID del proceso específico');
            
            // Usuario que registró el pago
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            
            $table->timestamps();

            // Índices
            $table->index(['payable_type', 'payable_id']);
            $table->index(['process_type', 'process_id']);
            $table->index('receipt');
            $table->index('document_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
