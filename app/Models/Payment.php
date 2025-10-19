<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'payment_method_id',
        'payment_gateway',
        'gateway_payment_intent_id',
        'gateway_setup_intent_id',
        'gateway_charge_id',
        'gateway_customer_id',
        'amount',
        'amount_received',
        'currency',
        'application_fee_amount',
        'gateway_fee',
        'net_amount',
        'status',
        'cancellation_reason',
        'failure_message',
        'failure_code',
        'next_action',
        'client_secret',
        'wallet_details',
        'confirmed_at',
        'captured_at',
        'canceled_at',
        'failed_at',
        'refunded_at',
        'metadata',
        'description',
        'gateway_response',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'amount_received' => 'decimal:2',
        'application_fee_amount' => 'decimal:2',
        'gateway_fee' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'metadata' => 'array',
        'next_action' => 'array',
        'wallet_details' => 'array',
        'gateway_response' => 'array',
        'confirmed_at' => 'datetime',
        'captured_at' => 'datetime',
        'canceled_at' => 'datetime',
        'failed_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    // Status constants for better type safety
    const STATUS_REQUIRES_PAYMENT_METHOD = 'requires_payment_method';
    const STATUS_REQUIRES_CONFIRMATION = 'requires_confirmation';
    const STATUS_REQUIRES_ACTION = 'requires_action';
    const STATUS_PROCESSING = 'processing';
    const STATUS_REQUIRES_CAPTURE = 'requires_capture';
    const STATUS_CANCELED = 'canceled';
    const STATUS_SUCCEEDED = 'succeeded';
    const STATUS_FAILED = 'failed';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    // Status helper methods
    public function isSuccessful(): bool
    {
        return $this->status === self::STATUS_SUCCEEDED;
    }

    public function isPending(): bool
    {
        return in_array($this->status, [
            self::STATUS_REQUIRES_PAYMENT_METHOD,
            self::STATUS_REQUIRES_CONFIRMATION,
            self::STATUS_REQUIRES_ACTION,
            self::STATUS_PROCESSING,
            self::STATUS_REQUIRES_CAPTURE,
        ]);
    }

    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function isCanceled(): bool
    {
        return $this->status === self::STATUS_CANCELED;
    }

    public function requiresAction(): bool
    {
        return $this->status === self::STATUS_REQUIRES_ACTION;
    }

    // Lifecycle helper methods
    public function markAsSucceeded(): void
    {
        $this->update([
            'status' => self::STATUS_SUCCEEDED,
            'confirmed_at' => $this->confirmed_at ?? now(),
        ]);
    }

    public function markAsFailed(string $message = null, string $code = null): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'failed_at' => now(),
            'failure_message' => $message,
            'failure_code' => $code,
        ]);
    }

    public function markAsCanceled(string $reason = null): void
    {
        $this->update([
            'status' => self::STATUS_CANCELED,
            'canceled_at' => now(),
            'cancellation_reason' => $reason,
        ]);
    }

    // Amount helper methods
    public function getFormattedAmount(): string
    {
        return number_format($this->amount, 2);
    }

    public function calculateNetAmount(): float
    {
        return $this->amount_received - $this->gateway_fee;
    }
}