<?php

namespace App\Interfaces;

use App\Models\Payment;
use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Collection;

interface PaymentServiceInterface
{
    public function createPaymentIntent(array $data): array;
    public function confirmPayment(string $paymentIntentId, string $paymentMethodId = null): Payment;
    public function capturePayment(string $paymentId): Payment;
    public function cancelPayment(string $paymentId): Payment;
    public function refundPayment(string $paymentId, ?float $amount = null): Payment;
    
    // Payment Methods
    public function createPaymentMethod(array $data): PaymentMethod;
    public function attachPaymentMethodToCustomer(string $paymentMethodId, int $userId): PaymentMethod;
    public function detachPaymentMethod(string $paymentMethodId): bool;
    public function getCustomerPaymentMethods(int $userId): Collection;
    public function setDefaultPaymentMethod(int $userId, string $paymentMethodId): bool;
    
    // Webhooks
    public function handleWebhook(array $payload): void;
}