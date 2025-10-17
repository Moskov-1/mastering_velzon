<?php

namespace App\DTO;

class PaymentIntentData
{
    public function __construct(
        public float $amount,
        public string $currency,
        public int $userId,
        public string $description,
        public array $metadata = [],
        public ?string $customerId = null,
        public ?string $paymentMethod = null,
        public bool $setupFutureUsage = false,
        public ?string $returnUrl = null
    ) {}

    public function toArray(): array
    {
        return [
            'amount' => $this->amount * 100, // Convert to cents
            'currency' => $this->currency,
            'description' => $this->description,
            'metadata' => array_merge($this->metadata, ['user_id' => $this->userId]),
            'customer' => $this->customerId,
            'payment_method' => $this->paymentMethod,
            'setup_future_usage' => $this->setupFutureUsage ? 'on_session' : null,
            'return_url' => $this->returnUrl,
        ];
    }
}