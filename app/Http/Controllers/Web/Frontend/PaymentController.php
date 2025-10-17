<?php

namespace App\Http\Controllers\Web\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
     public function __construct(private PaymentService $paymentService) {}

    public function createIntent(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string|max:255',
            'currency' => 'sometimes|string|size:3',
            'return_url' => 'sometimes|url',
        ]);

        try {
            $result = $this->paymentService->createPaymentIntent([
                'user_id' => auth()->id(),
                'amount' => $request->amount,
                'currency' => $request->currency ?? 'usd',
                'description' => $request->description,
                'return_url' => $request->return_url,
                'metadata' => $request->metadata ?? [],
            ]);

            return response()->json([
                'success' => true,
                'client_secret' => $result['client_secret'],
                'payment_intent_id' => $result['payment_intent_id'],
                'payment' => $result['payment'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function confirm(Request $request): JsonResponse
    {
        $request->validate([
            'payment_intent_id' => 'required|string',
        ]);

        try {
            $result = $this->paymentService->confirmPayment($request->payment_intent_id);

            return response()->json([
                'success' => true,
                'payment' => $result['payment'],
                'status' => $result['status'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function webhook(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        $result = $this->paymentService->handleWebhook(
            json_decode($payload, true),
            $signature
        );

        if ($result['success']) {
            return response()->json(['success' => true]);
        }

        return response()->json([
            'success' => false,
            'error' => $result['error'],
        ], 400);
    }
}
