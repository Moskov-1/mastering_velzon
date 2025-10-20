<?php

namespace App\Services;

use Exception;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\DTO\PaymentIntentData;
use Illuminate\Support\Facades\DB;
use App\Interfaces\PaymentGatewayInterface;
use App\Interfaces\PaymentRepositoryInterface;

class PaymentService
{
    public function __construct(
        private PaymentRepositoryInterface $paymentRepository,
        private PaymentGatewayInterface $paymentGateway
    ) {}

    public function createPayment(array $data): Payment
    {
        return DB::transaction(function () use ($data) {
            $payment = Payment::create([
                'user_id' => $data['user_id'],
                'payment_method_id' => $data['payment_method_id'] ?? null,
                'payment_gateway' => $data['payment_gateway'] ?? 'stripe',
                'amount' => $data['amount'],
                'currency' => $data['currency'] ?? 'usd',
                'description' => $data['description'] ?? null,
                'metadata' => $data['metadata'] ?? [],
                'status' => Payment::STATUS_REQUIRES_PAYMENT_METHOD,
            ]);

            // Create payment intent with gateway
            $gatewayResponse = $this->gateway->createPaymentIntent([
                'amount' => $data['amount'],
                'currency' => $data['currency'] ?? 'usd',
                'payment_method' => $data['payment_method_id'] ? $this->getGatewayPaymentMethodId($data['payment_method_id']) : null,
                'customer' => $data['customer_id'] ?? null,
                'metadata' => array_merge($data['metadata'] ?? [], ['payment_id' => $payment->id]),
            ]);

            $payment->update([
                'gateway_payment_intent_id' => $gatewayResponse['id'],
                'client_secret' => $gatewayResponse['client_secret'] ?? null,
                'gateway_response' => $gatewayResponse,
            ]);

            return $payment->fresh();
        });
    }

    public function confirmPayment(Payment $payment, array $data): Payment
    {
        return DB::transaction(function () use ($payment, $data) {
            $gatewayResponse = $this->gateway->confirmPaymentIntent(
                $payment->gateway_payment_intent_id,
                $data
            );

            $this->updatePaymentFromGatewayResponse($payment, $gatewayResponse);

            if ($payment->status === Payment::STATUS_SUCCEEDED) {
                $this->handleSuccessfulPayment($payment);
            }

            return $payment->fresh();
        });
    }

    public function handleWebhook(array $payload): void
    {
        $event = $this->gateway->parseWebhook($payload);

        switch ($event['type']) {
            case 'payment_intent.succeeded':
                $this->handlePaymentSucceeded($event);
                break;
            case 'payment_intent.payment_failed':
                $this->handlePaymentFailed($event);
                break;
            case 'payment_intent.canceled':
                $this->handlePaymentCanceled($event);
                break;
        }
    }

    public function cancelPayment(Payment $payment, string $reason = null): Payment
    {
        return DB::transaction(function () use ($payment, $reason) {
            if ($payment->gateway_payment_intent_id) {
                $gatewayResponse = $this->gateway->cancelPaymentIntent($payment->gateway_payment_intent_id);
                $this->updatePaymentFromGatewayResponse($payment, $gatewayResponse);
            }

            $payment->update([
                'status' => Payment::STATUS_CANCELED,
                'cancellation_reason' => $reason,
                'canceled_at' => now(),
            ]);

            return $payment->fresh();
        });
    }

    public function refundPayment(Payment $payment, array $data = []): Payment
    {
        return DB::transaction(function () use ($payment, $data) {
            $gatewayResponse = $this->gateway->createRefund(
                $payment->gateway_charge_id ?? $payment->gateway_payment_intent_id,
                $data
            );

            $payment->update([
                'refunded_at' => now(),
                'metadata' => array_merge($payment->metadata ?? [], ['refund' => $gatewayResponse]),
            ]);

            return $payment->fresh();
        });
    }

    protected function updatePaymentFromGatewayResponse(Payment $payment, array $response): void
    {
        $updateData = [
            'status' => $this->mapGatewayStatus($response['status']),
            'gateway_charge_id' => $response['latest_charge'] ?? null,
            'amount_received' => $response['amount_received'] ?? null,
            'gateway_fee' => $response['application_fee_amount'] ?? 0,
            'net_amount' => $this->calculateNetAmount($response),
            'gateway_response' => array_merge($payment->gateway_response ?? [], $response),
        ];

        // Update timestamps based on status
        if ($response['status'] === 'succeeded' && !$payment->confirmed_at) {
            $updateData['confirmed_at'] = now();
        }

        if ($response['status'] === 'canceled' && !$payment->canceled_at) {
            $updateData['canceled_at'] = now();
        }

        $payment->update($updateData);
    }

    protected function handleSuccessfulPayment(Payment $payment): void
    {
        // Update order status, send notifications, etc.
        event(new \App\Events\PaymentSucceeded($payment));
    }

    protected function handlePaymentSucceeded(array $event): void
    {
        $payment = Payment::where('gateway_payment_intent_id', $event['data']['object']['id'])->first();
        
        if ($payment) {
            $this->updatePaymentFromGatewayResponse($payment, $event['data']['object']);
            $this->handleSuccessfulPayment($payment);
        }
    }

    protected function handlePaymentFailed(array $event): void
    {
        $payment = Payment::where('gateway_payment_intent_id', $event['data']['object']['id'])->first();
        
        if ($payment) {
            $payment->update([
                'status' => Payment::STATUS_FAILED,
                'failure_message' => $event['data']['object']['last_payment_error']['message'] ?? null,
                'failure_code' => $event['data']['object']['last_payment_error']['code'] ?? null,
                'failed_at' => now(),
            ]);
        }
    }

    protected function handlePaymentCanceled(array $event): void
    {
        $payment = Payment::where('gateway_payment_intent_id', $event['data']['object']['id'])->first();
        
        if ($payment) {
            $payment->update([
                'status' => Payment::STATUS_CANCELED,
                'cancellation_reason' => $event['data']['object']['cancellation_reason'] ?? null,
                'canceled_at' => now(),
            ]);
        }
    }

    protected function mapGatewayStatus(string $gatewayStatus): string
    {
        return match($gatewayStatus) {
            'requires_payment_method' => Payment::STATUS_REQUIRES_PAYMENT_METHOD,
            'requires_confirmation' => Payment::STATUS_REQUIRES_CONFIRMATION,
            'requires_action' => Payment::STATUS_REQUIRES_ACTION,
            'processing' => Payment::STATUS_PROCESSING,
            'requires_capture' => Payment::STATUS_REQUIRES_CAPTURE,
            'canceled' => Payment::STATUS_CANCELED,
            'succeeded' => Payment::STATUS_SUCCEEDED,
            default => Payment::STATUS_FAILED,
        };
    }

    protected function calculateNetAmount(array $response): ?float
    {
        if (!isset($response['amount_received'])) {
            return null;
        }

        $fee = $response['application_fee_amount'] ?? 0;
        return $response['amount_received'] - $fee;
    }

    protected function getGatewayPaymentMethodId(?int $paymentMethodId): ?string
    {
        if (!$paymentMethodId) {
            return null;
        }

        $paymentMethod = PaymentMethod::find($paymentMethodId);
        return $paymentMethod?->payment_method_id;
    }
}