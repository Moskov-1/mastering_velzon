<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Repositories\Eloquent\PaymentRepository;
use App\Services\PaymentGateway\PaymentGatewayInterface;
use App\Services\PaymentGateway\StripeService;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Repositories
        $this->app->bind(PaymentRepositoryInterface::class, PaymentRepository::class);
        
        // Services
        $this->app->bind(PaymentGatewayInterface::class, StripeService::class);
    }

    public function boot(): void
    {
        //
    }
}