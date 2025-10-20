<?php

namespace App\Http\Controllers\API\Payments;

use Illuminate\Http\Request;
use App\Interfaces\PaymentServiceInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\API\BaseController as BaseController;


class StripeWebhookController extends BaseController
{
    public function handleWebhook(Request $request, PaymentServiceInterface $paymentService)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sigHeader, config('services.stripe.webhook_secret')
            );

            $paymentService->handleWebhook($event->toArray());

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}