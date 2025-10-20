<?php

namespace App\Http\Controllers\Web\Backend\Payments;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Interfaces\PaymentServiceInterface;

class PaymentController extends Controller
{
    public function __construct(private PaymentServiceInterface $paymentService)
    {
    }

    public function createPaymentIntent(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.50',
            'payment_method_id' => 'sometimes|string',
        ]);

        $paymentIntent = $this->paymentService->createPaymentIntent([
            'user_id' => auth('web')->user()->id,
            'amount' => $request->amount,
            'payment_method_id' => $request->payment_method_id,
            'description' => $request->description,
        ]);

        return response()->json($paymentIntent);
    }

    public function confirmPayment(Request $request)
    {
        $request->validate([
            'payment_intent_id' => 'required|string',
        ]);

        $payment = $this->paymentService->confirmPayment(
            $request->payment_intent_id,
            $request->payment_method_id
        );

        return response()->json($payment);
    }
}