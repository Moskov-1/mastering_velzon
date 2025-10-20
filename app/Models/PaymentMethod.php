<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'provider',
        'provider_customer_id',
        'payment_method_id',
        'card_brand',
        'last_four',
        'expiry_month',
        'expiry_year',
        'fingerprint',
        'wallet_type',
        'wallet_details',
        'bank_name',
        'bank_account_last_four',
        'is_default',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'wallet_details' => 'array',
        'metadata' => 'array',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}