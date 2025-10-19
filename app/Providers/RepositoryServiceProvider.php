<?php

namespace App\Providers;

use App\Services\StripeService;
use Illuminate\Support\ServiceProvider;
use App\Http\Repository\PaymentRepository;
use App\Interfaces\PaymentGatewayInterface;
use App\Interfaces\PaymentRepositoryInterface;

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