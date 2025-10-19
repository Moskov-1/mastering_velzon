<?php

namespace App\Interfaces;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface PaymentRepositoryInterface
{
    public function findById(string $id): ?Payment;
    public function findByGatewayId(string $gatewayId): ?Payment;
    public function create(array $data): Payment;
    public function update(Payment $payment, array $data): bool;
    public function delete(Payment $payment): bool;
    public function getUserPayments(int $userId): Collection;
    public function getRecentPayments(int $limit = 10): Collection;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
}
