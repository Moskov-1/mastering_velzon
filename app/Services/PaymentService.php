<?php

namespace App\Services;

use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Services\PaymentGateway\PaymentGatewayInterface;
use App\DTO\PaymentIntentData;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Exception;

class PaymentService
{
    public function __construct(
        private PaymentRepositoryInterface $paymentRepository,
        private PaymentGatewayInterface $paymentGateway
    ) {}

    public function createPaymentIntent(array $data): array
    {
        return DB::transaction(function () use ($data) {
            // Create payment intent with gateway
            $paymentData = new PaymentIntentData(
                amount: $data['amount'],
                currency: $data['currency'] ?? 'usd',
                userId: $data['user_id'],
                description: $data['description'],
                metadata: $data['metadata'] ?? [],
                returnUrl: $data['return_url'] ?? null
            );

            $gatewayResponse = $this->paymentGateway->createPaymentIntent($paymentData);

            if (!$gatewayResponse['success']) {
                throw new Exception('Payment intent creation failed: ' . ($gatewayResponse['error'] ?? 'Unknown error'));
            }

            // Create payment record
            $payment = $this->paymentRepository->create([
                'user_id' => $data['user_id'],
                'payment_gateway' => 'stripe',
                'gateway_payment_id' => $gatewayResponse['payment_intent_id'],
                'amount' => $data['amount'],
                'currency' => $data['currency'] ?? 'usd',
                'status' => $gatewayResponse['status'],
                'description' => $data['description'],
                'metadata' => $data['metadata'] ?? [],
            ]);

            return [
                'payment' => $payment,
                'client_secret' => $gatewayResponse['client_secret'],
                'payment_intent_id' => $gatewayResponse['payment_intent_id'],
            ];
        });
    }

    public function confirmPayment(string $paymentIntentId): array
    {
        return DB::transaction(function () use ($paymentIntentId) {
            // Confirm with gateway
            $gatewayResponse = $this->paymentGateway->confirmPaymentIntent($paymentIntentId);

            if (!$gatewayResponse['success']) {
                throw new Exception('Payment confirmation failed: ' . ($gatewayResponse['error'] ?? 'Unknown error'));
            }

            // Update payment record
            $payment = $this->paymentRepository->findByGatewayId($paymentIntentId);
            
            if (!$payment) {
                throw new Exception('Payment not found');
            }

            $this->paymentRepository->update($payment, [
                'status' => $gatewayResponse['status'],
                'paid_at' => $gatewayResponse['status'] === 'succeeded' ? now() : null,
            ]);

            return [
                'payment' => $payment->fresh(),
                'status' => $gatewayResponse['status'],
            ];
        });
    }

    public function handleWebhook(array $payload, string $signature): array
    {
        $webhookResponse = $this->paymentGateway->handleWebhook($payload, $signature);

        if (!$webhookResponse['success']) {
            return $webhookResponse;
        }

        $event = $webhookResponse['event'];
        
        switch ($event->type) {
            case 'payment_intent.succeeded':
                return $this->handlePaymentSucceeded($event->data->object);
            case 'payment_intent.payment_failed':
                return $this->handlePaymentFailed($event->data->object);
            case 'payment_intent.canceled':
                return $this->handlePaymentCanceled($event->data->object);
        }

        return ['success' => true, 'handled' => false, 'type' => $event->type];
    }

    private function handlePaymentSucceeded($paymentIntent): array
    {
        $payment = $this->paymentRepository->findByGatewayId($paymentIntent->id);
        
        if ($payment) {
            $this->paymentRepository->update($payment, [
                'status' => 'succeeded',
                'paid_at' => now(),
                'metadata' => array_merge($payment->metadata ?? [], ['webhook_processed' => true]),
            ]);
        }

        return ['success' => true, 'event' => 'payment_intent.succeeded'];
    }

    private function handlePaymentFailed($paymentIntent): array
    {
        $payment = $this->paymentRepository->findByGatewayId($paymentIntent->id);
        
        if ($payment) {
            $this->paymentRepository->update($payment, [
                'status' => 'failed',
                'metadata' => array_merge($payment->metadata ?? [], [
                    'webhook_processed' => true,
                    'failure_message' => $paymentIntent->last_payment_error->message ?? null,
                ]),
            ]);
        }

        return ['success' => true, 'event' => 'payment_intent.payment_failed'];
    }

    private function handlePaymentCanceled($paymentIntent): array
    {
        $payment = $this->paymentRepository->findByGatewayId($paymentIntent->id);
        
        if ($payment) {
            $this->paymentRepository->update($payment, [
                'status' => 'canceled',
                'metadata' => array_merge($payment->metadata ?? [], ['webhook_processed' => true]),
            ]);
        }

        return ['success' => true, 'event' => 'payment_intent.canceled'];
    }

    public function getPaymentByGatewayId(string $gatewayId): ?Payment
    {
        return $this->paymentRepository->findByGatewayId($gatewayId);
    }

    public function getUserPayments(int $userId)
    {
        return $this->paymentRepository->getUserPayments($userId);
    }
}