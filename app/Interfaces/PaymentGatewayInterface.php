<?php

namespace App\Interfaces;

use App\DTO\PaymentIntentData;

interface PaymentGatewayInterface
{
    public function createPaymentIntent(PaymentIntentData $paymentData): array;
    public function retrievePaymentIntent(string $paymentIntentId): array;
    public function confirmPaymentIntent(string $paymentIntentId): array;
    public function cancelPaymentIntent(string $paymentIntentId): array;
    public function refundPayment(string $paymentIntentId, ?float $amount = null): array;
    public function handleWebhook(array $payload, string $signature): array;
}