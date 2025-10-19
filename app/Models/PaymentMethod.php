<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'expiry_month' => 'integer',
        'expiry_year' => 'integer',
        'wallet_details' => 'array',
        'metadata' => 'array',
    ];

    // Type constants
    const TYPE_CARD = 'card';
    const TYPE_DIGITAL_WALLET = 'digital_wallet';
    const TYPE_BANK_REDIRECT = 'bank_redirect';
    const TYPE_UPI = 'upi';
    const TYPE_BANK_TRANSFER = 'bank_transfer';

    // Provider constants
    const PROVIDER_STRIPE = 'stripe';
    const PROVIDER_PAYPAL = 'paypal';
    const PROVIDER_RAZORPAY = 'razorpay';

    // Wallet type constants
    const WALLET_GOOGLE_PAY = 'google_pay';
    const WALLET_APPLE_PAY = 'apple_pay';

    // Card brand constants
    const CARD_VISA = 'visa';
    const CARD_MASTERCARD = 'mastercard';
    const CARD_AMEX = 'amex';
    const CARD_DISCOVER = 'discover';
    const CARD_DINERS = 'diners';
    const CARD_JCB = 'jcb';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeCards($query)
    {
        return $query->where('type', self::TYPE_CARD);
    }

    public function scopeDigitalWallets($query)
    {
        return $query->where('type', self::TYPE_DIGITAL_WALLET);
    }

    public function scopeByProvider($query, string $provider)
    {
        return $query->where('provider', $provider);
    }

    // Helper methods
    public function isCard(): bool
    {
        return $this->type === self::TYPE_CARD;
    }

    public function isDigitalWallet(): bool
    {
        return $this->type === self::TYPE_DIGITAL_WALLET;
    }

    public function isBankRedirect(): bool
    {
        return $this->type === self::TYPE_BANK_REDIRECT;
    }

    public function isUpi(): bool
    {
        return $this->type === self::TYPE_UPI;
    }

    public function isBankTransfer(): bool
    {
        return $this->type === self::TYPE_BANK_TRANSFER;
    }

    public function isStripe(): bool
    {
        return $this->provider === self::PROVIDER_STRIPE;
    }

    public function isPaypal(): bool
    {
        return $this->provider === self::PROVIDER_PAYPAL;
    }

    public function isRazorpay(): bool
    {
        return $this->provider === self::PROVIDER_RAZORPAY;
    }

    // Card helper methods
    public function getCardDisplay(): string
    {
        if (!$this->isCard()) {
            return '';
        }

        $brand = strtoupper($this->card_brand ?? 'card');
        return "{$brand} •••• {$this->last_four}";
    }

    public function getExpiryDisplay(): string
    {
        if (!$this->isCard() || !$this->expiry_month || !$this->expiry_year) {
            return '';
        }

        return sprintf('%02d/%d', $this->expiry_month, $this->expiry_year);
    }

    public function isExpired(): bool
    {
        if (!$this->isCard() || !$this->expiry_month || !$this->expiry_year) {
            return false;
        }

        $now = now();
        return $now->year > $this->expiry_year || 
               ($now->year === $this->expiry_year && $now->month > $this->expiry_month);
    }

    public function willExpireSoon(int $months = 3): bool
    {
        if (!$this->isCard() || !$this->expiry_month || !$this->expiry_year) {
            return false;
        }

        $expiryDate = \Carbon\Carbon::createFromDate($this->expiry_year, $this->expiry_month, 1)->endOfMonth();
        return $expiryDate->diffInMonths(now()) <= $months;
    }

    // Default payment method management
    public function setAsDefault(): void
    {
        // Remove default status from other payment methods of this user
        self::where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        $this->update(['is_default' => true]);
    }

    public function deactivate(): void
    {
        $this->update(['is_active' => false]);

        // If this was the default payment method, set another one as default
        if ($this->is_default) {
            $newDefault = self::where('user_id', $this->user_id)
                ->where('id', '!=', $this->id)
                ->active()
                ->first();

            if ($newDefault) {
                $newDefault->setAsDefault();
            }
        }
    }

    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    // Wallet helper methods
    public function getWalletDisplay(): string
    {
        if (!$this->isDigitalWallet()) {
            return '';
        }

        return match($this->wallet_type) {
            self::WALLET_GOOGLE_PAY => 'Google Pay',
            self::WALLET_APPLE_PAY => 'Apple Pay',
            default => ucfirst(str_replace('_', ' ', $this->wallet_type)),
        };
    }

    // Validation methods
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->isCard() && $this->isExpired()) {
            return false;
        }

        return true;
    }

    // Get display name based on type
    public function getDisplayName(): string
    {
        return match($this->type) {
            self::TYPE_CARD => $this->getCardDisplay(),
            self::TYPE_DIGITAL_WALLET => $this->getWalletDisplay(),
            self::TYPE_BANK_REDIRECT => "Bank Transfer •••• {$this->bank_account_last_four}",
            self::TYPE_UPI => 'UPI Payment',
            self::TYPE_BANK_TRANSFER => "{$this->bank_name} •••• {$this->bank_account_last_four}",
            default => ucfirst(str_replace('_', ' ', $this->type)),
        };
    }
}