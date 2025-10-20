<?php

namespace App\Interfaces;

use App\Models\Payment;
use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;


interface PaymentRepositoryInterface
{
    public function findById(string $id): ?Payment;
    public function findByGatewayIntentId(string $gatewayPaymentIntentId): ?Payment;
    public function create(array $data): Payment;
    public function update(Payment $payment, array $data): bool;
    public function updateStatus(Payment $payment, string $status): bool;
    
    // Payment Method methods
    public function findPaymentMethodById(string $id): ?PaymentMethod;
    public function findPaymentMethodByGatewayId(string $gatewayId): ?PaymentMethod;
    public function createPaymentMethod(array $data): PaymentMethod;
    public function updatePaymentMethod(PaymentMethod $paymentMethod, array $data): bool;
    public function getUserPaymentMethods(int $userId);
    public function setDefaultPaymentMethod(int $userId, string $paymentMethodId): bool;
}