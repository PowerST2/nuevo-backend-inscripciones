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
        'process_type',
        'process_id',
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
     * Crear registro de cartera (deuda/obligación) directamente.
     * Se usa cuando un postulante se inscribe para generar la deuda que se enviará al OCEF.
     */
    public static function createObligation(array $data): self
    {
        return static::create([
            'receipt' => $data['receipt'],
            'service_code' => $data['service_code'] ?? null,
            'description' => $data['description'] ?? null,
            'amount' => $data['amount'],
            'payment_date' => $data['payment_date'] ?? null,
            'document_number' => $data['document_number'],
            'client_name' => $data['client_name'],
            'client_email' => $data['client_email'] ?? null,
            'bank' => $data['bank'] ?? null,
            'reference' => $data['reference'] ?? null,
            'operation' => $data['operation'] ?? null,
            'payable_type' => $data['payable_type'],
            'payable_id' => $data['payable_id'],
            'process_type' => $data['process_type'],
            'process_id' => $data['process_id'],
            'payment_id' => null, // Se llena cuando se confirma el pago del banco
            'is_paid' => false,
            'is_sent' => false,
        ]);
    }

    /**
     * Generar número de recibo único para cartera
     */
    public static function generateReceiptNumber(string $prefix = 'OBL'): string
    {
        $date = now()->format('Ymd');
        $lastReceipt = static::where('receipt', 'like', "{$prefix}{$date}%")
            ->orderBy('receipt', 'desc')
            ->first();

        if ($lastReceipt) {
            $lastNumber = (int) substr($lastReceipt->receipt, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "{$prefix}{$date}{$newNumber}";
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
