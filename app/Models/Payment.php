<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        'next_action' => 'array',
        'wallet_details' => 'array',
        'metadata' => 'array',
        'gateway_response' => 'array',
        'confirmed_at' => 'datetime',
        'captured_at' => 'datetime',
        'canceled_at' => 'datetime',
        'failed_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}