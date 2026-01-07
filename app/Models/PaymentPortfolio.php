<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PaymentPortfolio extends Model
{
    use HasFactory;

    protected $fillable = [
        'receipt',
        'service_code',
        'description',
        'amount',
        'payment_date',
        'document_number',
        'client_name',
        'client_email',
        'bank',
        'reference',
        'operation',
        'payable_type',
        'payable_id',
        'period_id',
        'process_type',
        'process_id',
        'user_id',
        'is_paid',
        'is_sent',
        'sent_at',
        'batch_number',
        'payment_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
        'is_paid' => 'boolean',
        'is_sent' => 'boolean',
        'sent_at' => 'datetime',
    ];

    /**
     * Relación polimórfica con el postulante
     */
    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Relación con el periodo
     */
    public function period(): BelongsTo
    {
        return $this->belongsTo(Period::class);
    }

    /**
     * Relación con el usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con el pago original
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Relación con la tarifa
     */
    public function tariff(): BelongsTo
    {
        return $this->belongsTo(Tariff::class, 'service_code', 'code');
    }

    /**
     * Scope para registros no enviados
     */
    public function scopeNotSent($query)
    {
        return $query->where('is_sent', false);
    }

    /**
     * Scope para registros enviados
     */
    public function scopeSent($query)
    {
        return $query->where('is_sent', true);
    }

    /**
     * Scope para registros pagados
     */
    public function scopePaid($query)
    {
        return $query->where('is_paid', true);
    }

    /**
     * Scope para registros pendientes de pago
     */
    public function scopePending($query)
    {
        return $query->where('is_paid', false);
    }

    /**
     * Crear registro de cartera desde un pago
     */
    public static function createFromPayment(Payment $payment): self
    {
        return static::create([
            'receipt' => $payment->receipt,
            'service_code' => $payment->service_code,
            'description' => $payment->description,
            'amount' => $payment->amount,
            'payment_date' => $payment->payment_date,
            'document_number' => $payment->document_number,
            'client_name' => $payment->client_name,
            'client_email' => $payment->client_email,
            'bank' => $payment->bank,
            'reference' => $payment->reference,
            'operation' => $payment->operation,
            'payable_type' => $payment->payable_type,
            'payable_id' => $payment->payable_id,
            'period_id' => $payment->period_id,
            'process_type' => $payment->process_type,
            'process_id' => $payment->process_id,
            'user_id' => $payment->user_id,
            'payment_id' => $payment->id,
            'is_paid' => false,
            'is_sent' => false,
        ]);
    }

    /**
     * Marcar como enviado
     */
    public function markAsSent(string $batchNumber = null): bool
    {
        $this->is_sent = true;
        $this->sent_at = now('America/Lima');
        $this->batch_number = $batchNumber;
        
        return $this->save();
    }

    /**
     * Marcar como pagado
     */
    public function markAsPaid(): bool
    {
        $this->is_paid = true;
        
        return $this->save();
    }

    /**
     * Generar número de lote único
     */
    public static function generateBatchNumber(): string
    {
        return 'BATCH-' . now()->format('YmdHis');
    }
}
