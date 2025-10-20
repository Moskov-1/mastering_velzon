<?php

namespace App\Providers;

use App\Services\StripeService;
use App\Services\StripePaymentService;
use Illuminate\Support\ServiceProvider;
use App\Http\Repository\PaymentRepository;
use App\Interfaces\PaymentGatewayInterface;
use App\Interfaces\PaymentServiceInterface;
use App\Interfaces\PaymentRepositoryInterface;

class PaymentServiceProvider  extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PaymentRepositoryInterface::class, PaymentRepository::class);
        $this->app->bind(PaymentServiceInterface::class, StripePaymentService::class);
    }


    public function boot(): void
    {
        //
    }
}