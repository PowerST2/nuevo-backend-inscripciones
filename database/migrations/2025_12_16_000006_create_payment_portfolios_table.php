<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_portfolios', function (Blueprint $table) {
            $table->id();
            $table->string('receipt', 100)->comment('Número de recibo/boleta');
            $table->string('service_code', 5)->nullable();
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2)->default(0);
            $table->date('payment_date')->nullable();
            $table->string('document_number', 11)->nullable();
            $table->string('client_name', 100)->nullable();
            $table->string('client_email', 100)->nullable();
            $table->string('bank', 100)->nullable();
            $table->string('reference', 100)->nullable();
            $table->string('operation', 100)->nullable();
            
            // Relaciones polimórficas
            $table->string('payable_type')->nullable();
            $table->unsignedBigInteger('payable_id')->nullable();
            
            // Periodo y proceso
            $table->foreignId('period_id')->nullable()->constrained()->nullOnDelete();
            $table->string('process_type')->nullable();
            $table->unsignedBigInteger('process_id')->nullable();
            
            // Usuario
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            
            // Campos para control de cartera
            $table->boolean('is_paid')->default(false)->comment('Si el pago fue realizado/confirmado');
            $table->boolean('is_sent')->default(false)->comment('Si ya fue enviado en archivo Excel');
            $table->timestamp('sent_at')->nullable()->comment('Fecha de envío en archivo');
            $table->string('batch_number', 50)->nullable()->comment('Número de lote/envío');
            
            // Referencia al pago original
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();
            
            $table->timestamps();

            // Índices
            $table->index(['payable_type', 'payable_id']);
            $table->index(['process_type', 'process_id']);
            $table->index('is_sent');
            $table->index('is_paid');
            $table->index('batch_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_portfolios');
    }
};
