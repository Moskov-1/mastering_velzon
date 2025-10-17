<?php

namespace App\Repositories\Eloquent;

use App\Models\Payment;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class PaymentRepository implements PaymentRepositoryInterface
{
    public function findById(string $id): ?Payment
    {
        return Payment::find($id);
    }

    public function findByGatewayId(string $gatewayId): ?Payment
    {
        return Payment::where('gateway_payment_id', $gatewayId)->first();
    }

    public function create(array $data): Payment
    {
        return Payment::create($data);
    }

    public function update(Payment $payment, array $data): bool
    {
        return $payment->update($data);
    }

    public function delete(Payment $payment): bool
    {
        return $payment->delete();
    }

    public function getUserPayments(int $userId): Collection
    {
        return Payment::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getRecentPayments(int $limit = 10): Collection
    {
        return Payment::with('user')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Payment::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}