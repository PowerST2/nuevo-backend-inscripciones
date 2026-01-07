<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Payment extends Model
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
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    /**
     * Relación polimórfica con el postulante (SimulationApplicant, Applicant, etc.)
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
     * Relación con el usuario que registró
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con la tarifa
     */
    public function tariff(): BelongsTo
    {
        return $this->belongsTo(Tariff::class, 'service_code', 'code');
    }

    /**
     * Relación con registros de cartera
     */
    public function portfolios(): HasMany
    {
        return $this->hasMany(PaymentPortfolio::class);
    }

    /**
     * Generar número de recibo único
     */
    public static function generateReceiptNumber(string $prefix = 'REC'): string
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
     * Verificar si el pago ya fue enviado en cartera
     */
    public function isSentInPortfolio(): bool
    {
        return $this->portfolios()->where('is_sent', true)->exists();
    }
}
