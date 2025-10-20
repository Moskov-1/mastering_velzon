<?php

namespace App\Http\Repository;

use App\Models\Payment;
use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Collection;
use App\Interfaces\PaymentRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class PaymentRepository implements PaymentRepositoryInterface
{
    public function findById(string $id): ?Payment
    {
        return Payment::find($id);
    }

    public function findByGatewayIntentId(string $gatewayPaymentIntentId): ?Payment
    {
        return Payment::where('gateway_payment_intent_id', $gatewayPaymentIntentId)->first();
    }

    public function create(array $data): Payment
    {
        return Payment::create($data);
    }

    public function update(Payment $payment, array $data): bool
    {
        return $payment->update($data);
    }

    public function updateStatus(Payment $payment, string $status): bool
    {
        $updateData = ['status' => $status];
        
        // Set timestamp based on status
        switch ($status) {
            case 'succeeded':
                $updateData['confirmed_at'] = now();
                break;
            case 'canceled':
                $updateData['canceled_at'] = now();
                break;
            case 'failed':
                $updateData['failed_at'] = now();
                break;
            case 'requires_capture':
                $updateData['captured_at'] = now();
                break;
        }

        return $payment->update($updateData);
    }

    public function findPaymentMethodById(string $id): ?PaymentMethod
    {
        return PaymentMethod::find($id);
    }

    public function findPaymentMethodByGatewayId(string $gatewayId): ?PaymentMethod
    {
        return PaymentMethod::where('payment_method_id', $gatewayId)->first();
    }

    public function createPaymentMethod(array $data): PaymentMethod
    {
        return PaymentMethod::create($data);
    }

    public function updatePaymentMethod(PaymentMethod $paymentMethod, array $data): bool
    {
        return $paymentMethod->update($data);
    }

    public function getUserPaymentMethods(int $userId)
    {
        return PaymentMethod::where('user_id', $userId)
            ->where('is_active', true)
            ->orderBy('is_default', 'desc')
            ->get();
    }

    public function setDefaultPaymentMethod(int $userId, string $paymentMethodId): bool
    {
        // Remove default from all user's payment methods
        PaymentMethod::where('user_id', $userId)
            ->where('is_default', true)
            ->update(['is_default' => false]);

        // Set new default
        return PaymentMethod::where('id', $paymentMethodId)
            ->where('user_id', $userId)
            ->update(['is_default' => true]);
    }
}