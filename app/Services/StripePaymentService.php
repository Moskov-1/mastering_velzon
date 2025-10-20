<?php

namespace App\Services;

use Exception;
use Stripe\Refund;
use Stripe\Stripe;
use Stripe\Webhook;
use App\Models\User;
use App\Models\Payment;
use Stripe\StripeClient;
use Stripe\PaymentIntent;
use App\Models\PaymentMethod;
use App\DTO\PaymentIntentData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use App\Interfaces\PaymentGatewayInterface;
use App\Interfaces\PaymentServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use App\Interfaces\PaymentRepositoryInterface;


class StripePaymentService implements PaymentServiceInterface
{
    private StripeClient $stripe;
    private PaymentRepositoryInterface $paymentRepository;

    public function __construct(PaymentRepositoryInterface $paymentRepository)
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
        $this->paymentRepository = $paymentRepository;
    }

    public function createPaymentIntent(array $data): array
    {
        try {
            DB::beginTransaction();

            // Create payment record first
            $payment = $this->paymentRepository->create([
                'user_id' => $data['user_id'],
                'payment_method_id' => $data['payment_method_id'] ?? null,
                'amount' => $data['amount'],
                'currency' => $data['currency'] ?? 'usd',
                'description' => $data['description'] ?? null,
                'metadata' => $data['metadata'] ?? null,
                'status' => 'requires_payment_method',
            ]);

            // Prepare Stripe parameters
            $stripeParams = [
                'amount' => (int) ($data['amount'] * 100), // Convert to cents
                'currency' => $data['currency'] ?? 'usd',
                'metadata' => [
                    'payment_id' => $payment->id,
                    'user_id' => $data['user_id'],
                ],
            ];

            // Add customer if exists
            if (!empty($data['customer_id'])) {
                $stripeParams['customer'] = $data['customer_id'];
            } elseif (!empty($data['user_id'])) {
                $user = User::find($data['user_id']);
                if ($user && $user->gateway_customer_id) {
                    $stripeParams['customer'] = $user->gateway_customer_id;
                }
            }

            // Add payment method if provided
            if (!empty($data['payment_method_id'])) {
                $paymentMethod = $this->paymentRepository->findPaymentMethodById($data['payment_method_id']);
                if ($paymentMethod) {
                    $stripeParams['payment_method'] = $paymentMethod->payment_method_id;
                    $stripeParams['confirm'] = true;
                    $stripeParams['off_session'] = $data['off_session'] ?? false;
                }
            }

            // Create Stripe PaymentIntent
            $paymentIntent = $this->stripe->paymentIntents->create($stripeParams);

            // Update payment with Stripe data
            $this->paymentRepository->update($payment, [
                'gateway_payment_intent_id' => $paymentIntent->id,
                'gateway_customer_id' => $paymentIntent->customer ?? null,
                'client_secret' => $paymentIntent->client_secret,
                'status' => $paymentIntent->status,
                'gateway_response' => $paymentIntent->toArray(),
            ]);

            DB::commit();

            return [
                'payment_intent_id' => $paymentIntent->id,
                'client_secret' => $paymentIntent->client_secret,
                'status' => $paymentIntent->status,
                'payment_id' => $payment->id,
                'requires_action' => $paymentIntent->status === 'requires_action',
            ];

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function confirmPayment(string $paymentIntentId, string $paymentMethodId = null): Payment
    {
        try {
            $payment = $this->paymentRepository->findByGatewayIntentId($paymentIntentId);
            if (!$payment) {
                throw new Exception('Payment not found');
            }

            $params = [];
            if ($paymentMethodId) {
                $params['payment_method'] = $paymentMethodId;
            }

            $paymentIntent = $this->stripe->paymentIntents->confirm($paymentIntentId, $params);

            $this->paymentRepository->update($payment, [
                'status' => $paymentIntent->status,
                'gateway_response' => $paymentIntent->toArray(),
            ]);

            if ($paymentIntent->status === 'succeeded') {
                $this->paymentRepository->updateStatus($payment, 'succeeded');
                
                // Update amount received and fees
                if ($paymentIntent->charges->data[0] ?? null) {
                    $charge = $paymentIntent->charges->data[0];
                    $this->paymentRepository->update($payment, [
                        'amount_received' => $charge->amount / 100,
                        'gateway_fee' => $charge->balance_transaction->fee / 100 ?? 0,
                        'net_amount' => ($charge->amount - ($charge->balance_transaction->fee ?? 0)) / 100,
                        'gateway_charge_id' => $charge->id,
                    ]);
                }
            }

            return $payment->fresh();

        } catch (ApiErrorException $e) {
            $this->handleStripeError($payment, $e);
            throw $e;
        }
    }

    public function capturePayment(string $paymentId): Payment
    {
        $payment = $this->paymentRepository->findById($paymentId);
        if (!$payment) {
            throw new Exception('Payment not found');
        }

        $paymentIntent = $this->stripe->paymentIntents->capture($payment->gateway_payment_intent_id);

        $this->paymentRepository->update($payment, [
            'status' => $paymentIntent->status,
            'captured_at' => now(),
            'gateway_response' => $paymentIntent->toArray(),
        ]);

        return $payment->fresh();
    }

    public function cancelPayment(string $paymentId): Payment
    {
        $payment = $this->paymentRepository->findById($paymentId);
        if (!$payment) {
            throw new Exception('Payment not found');
        }

        $paymentIntent = $this->stripe->paymentIntents->cancel($payment->gateway_payment_intent_id);

        $this->paymentRepository->update($payment, [
            'status' => $paymentIntent->status,
            'cancellation_reason' => $paymentIntent->cancellation_reason,
            'canceled_at' => now(),
            'gateway_response' => $paymentIntent->toArray(),
        ]);

        return $payment->fresh();
    }

    public function refundPayment(string $paymentId, ?float $amount = null): Payment
    {
        $payment = $this->paymentRepository->findById($paymentId);
        if (!$payment || !$payment->gateway_charge_id) {
            throw new Exception('Payment or charge not found');
        }

        $refundParams = ['charge' => $payment->gateway_charge_id];
        if ($amount) {
            $refundParams['amount'] = (int) ($amount * 100);
        }

        $refund = $this->stripe->refunds->create($refundParams);

        $this->paymentRepository->update($payment, [
            'refunded_at' => now(),
            'metadata' => array_merge($payment->metadata ?? [], [
                'refund_id' => $refund->id,
                'refund_amount' => $amount ?? $payment->amount_received,
            ]),
        ]);

        return $payment->fresh();
    }

    public function createPaymentMethod(array $data): PaymentMethod
    {
        $paymentMethod = $this->stripe->paymentMethods->create([
            'type' => $data['type'],
            $data['type'] => $data['details'],
        ]);

        return $this->paymentRepository->createPaymentMethod([
            'user_id' => $data['user_id'],
            'type' => $data['type'],
            'provider' => 'stripe',
            'payment_method_id' => $paymentMethod->id,
            'card_brand' => $paymentMethod->card->brand ?? null,
            'last_four' => $paymentMethod->card->last4 ?? null,
            'expiry_month' => $paymentMethod->card->exp_month ?? null,
            'expiry_year' => $paymentMethod->card->exp_year ?? null,
            'fingerprint' => $paymentMethod->card->fingerprint ?? null,
            'metadata' => $paymentMethod->toArray(),
        ]);
    }

    public function attachPaymentMethodToCustomer(string $paymentMethodId, int $userId): PaymentMethod
    {
        $paymentMethod = $this->paymentRepository->findPaymentMethodByGatewayId($paymentMethodId);
        if (!$paymentMethod) {
            throw new Exception('Payment method not found');
        }

        $user = User::find($userId);
        if (!$user->gateway_customer_id) {
            // Create Stripe customer
            $customer = $this->stripe->customers->create([
                'email' => $user->email,
                'name' => $user->name,
                'metadata' => ['user_id' => $user->id],
            ]);
            $user->update(['gateway_customer_id' => $customer->id]);
        }

        // Attach payment method to customer
        $this->stripe->paymentMethods->attach(
            $paymentMethodId,
            ['customer' => $user->gateway_customer_id]
        );

        return $paymentMethod;
    }

    public function detachPaymentMethod(string $paymentMethodId): bool
    {
        $paymentMethod = $this->paymentRepository->findPaymentMethodByGatewayId($paymentMethodId);
        if (!$paymentMethod) {
            throw new Exception('Payment method not found');
        }

        $this->stripe->paymentMethods->detach($paymentMethodId);
        return $this->paymentRepository->updatePaymentMethod($paymentMethod, ['is_active' => false]);
    }

    public function getCustomerPaymentMethods(int $userId): Collection
    {
        return $this->paymentRepository->getUserPaymentMethods($userId);
    }

    public function setDefaultPaymentMethod(int $userId, string $paymentMethodId): bool
    {
        $paymentMethod = $this->paymentRepository->findPaymentMethodById($paymentMethodId);
        if (!$paymentMethod) {
            throw new Exception('Payment method not found');
        }

        $user = User::find($userId);
        if ($user->gateway_customer_id) {
            $this->stripe->customers->update($user->gateway_customer_id, [
                'invoice_settings' => [
                    'default_payment_method' => $paymentMethod->payment_method_id,
                ],
            ]);
        }

        return $this->paymentRepository->setDefaultPaymentMethod($userId, $paymentMethodId);
    }

    public function handleWebhook(array $payload): void
    {
        $event = $payload['type'];
        $data = $payload['data']['object'];

        switch ($event) {
            case 'payment_intent.succeeded':
                $this->handlePaymentSucceeded($data);
                break;
            case 'payment_intent.payment_failed':
                $this->handlePaymentFailed($data);
                break;
            case 'payment_intent.canceled':
                $this->handlePaymentCanceled($data);
                break;
        }
    }

    private function handlePaymentSucceeded(array $paymentIntent): void
    {
        $payment = $this->paymentRepository->findByGatewayIntentId($paymentIntent['id']);
        if ($payment) {
            $this->paymentRepository->updateStatus($payment, 'succeeded');
        }
    }

    private function handlePaymentFailed(array $paymentIntent): void
    {
        $payment = $this->paymentRepository->findByGatewayIntentId($paymentIntent['id']);
        if ($payment) {
            $this->paymentRepository->update($payment, [
                'status' => 'failed',
                'failure_message' => $paymentIntent['last_payment_error']['message'] ?? null,
                'failure_code' => $paymentIntent['last_payment_error']['code'] ?? null,
                'failed_at' => now(),
            ]);
        }
    }

    private function handlePaymentCanceled(array $paymentIntent): void
    {
        $payment = $this->paymentRepository->findByGatewayIntentId($paymentIntent['id']);
        if ($payment) {
            $this->paymentRepository->updateStatus($payment, 'canceled');
        }
    }

    private function handleStripeError(?Payment $payment, ApiErrorException $e): void
    {
        if ($payment) {
            $this->paymentRepository->update($payment, [
                'status' => 'failed',
                'failure_message' => $e->getMessage(),
                'failure_code' => $e->getStripeCode(),
                'failed_at' => now(),
            ]);
        }
    }
}