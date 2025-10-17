<?php

namespace App\Services\PaymentGateway;

use App\DTO\PaymentIntentData;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;
use Illuminate\Support\Facades\Log;

class StripeService implements PaymentGatewayInterface
{
    public function __construct()
    {
        Stripe::setApiKey(config('stripe.secret'));
        Stripe::setApiVersion(config('stripe.version', '2023-10-16'));
    }

    public function createPaymentIntent(PaymentIntentData $paymentData): array
    {
        try {
            $intent = PaymentIntent::create($paymentData->toArray());
            
            return [
                'success' => true,
                'payment_intent_id' => $intent->id,
                'client_secret' => $intent->client_secret,
                'status' => $intent->status,
                'amount' => $intent->amount / 100, // Convert back to dollars
                'currency' => $intent->currency,
            ];
        } catch (ApiErrorException $e) {
            Log::error('Stripe Payment Intent Creation Failed: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ];
        }
    }

    public function retrievePaymentIntent(string $paymentIntentId): array
    {
        try {
            $intent = PaymentIntent::retrieve($paymentIntentId);
            
            return [
                'success' => true,
                'payment_intent' => $intent->toArray(),
                'status' => $intent->status,
                'amount' => $intent->amount / 100,
            ];
        } catch (ApiErrorException $e) {
            Log::error('Stripe Payment Intent Retrieval Failed: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function confirmPaymentIntent(string $paymentIntentId): array
    {
        try {
            $intent = PaymentIntent::retrieve($paymentIntentId);
            $intent->confirm();
            
            return [
                'success' => true,
                'status' => $intent->status,
                'payment_intent' => $intent->toArray(),
            ];
        } catch (ApiErrorException $e) {
            Log::error('Stripe Payment Intent Confirmation Failed: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function cancelPaymentIntent(string $paymentIntentId): array
    {
        try {
            $intent = PaymentIntent::retrieve($paymentIntentId);
            $cancelled = $intent->cancel();
            
            return [
                'success' => true,
                'status' => $cancelled->status,
                'payment_intent' => $cancelled->toArray(),
            ];
        } catch (ApiErrorException $e) {
            Log::error('Stripe Payment Intent Cancellation Failed: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function refundPayment(string $paymentIntentId, ?float $amount = null): array
    {
        try {
            $params = ['payment_intent' => $paymentIntentId];
            
            if ($amount) {
                $params['amount'] = $amount * 100; // Convert to cents
            }
            
            $refund = \Stripe\Refund::create($params);
            
            return [
                'success' => true,
                'refund_id' => $refund->id,
                'status' => $refund->status,
                'amount' => $refund->amount / 100,
            ];
        } catch (ApiErrorException $e) {
            Log::error('Stripe Refund Failed: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function handleWebhook(array $payload, string $signature): array
    {
        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $signature,
                config('stripe.webhook_secret')
            );

            return [
                'success' => true,
                'event' => $event,
                'type' => $event->type,
            ];
        } catch (\Exception $e) {
            Log::error('Stripe Webhook Handling Failed: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}