<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Payment method type
            $table->enum('type', [
                'card', 
                'digital_wallet', 
                'bank_redirect', 
                'upi',
                'bank_transfer'
            ])->default('card');
            
            // Provider information
            $table->string('provider'); // 'stripe', 'paypal', 'razorpay', etc.
            $table->string('provider_customer_id')->nullable(); // Stripe Customer ID
            $table->string('payment_method_id'); // Stripe PaymentMethod ID
            
            // Card details (for card payments)
            $table->string('card_brand')->nullable(); // visa, mastercard, amex
            $table->string('last_four', 4)->nullable();
            $table->unsignedTinyInteger('expiry_month')->nullable();
            $table->unsignedSmallInteger('expiry_year')->nullable();
            $table->string('fingerprint')->nullable(); // Card fingerprint for duplicate detection
            
            // Digital wallet details
            $table->string('wallet_type')->nullable(); // google_pay, apple_pay, etc.
            $table->json('wallet_details')->nullable(); // Wallet-specific data
            
            // Bank details (for bank transfers/redirects)
            $table->string('bank_name')->nullable();
            $table->string('bank_account_last_four')->nullable();
            
            // Status and preferences
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'is_default']);
            $table->index(['user_id', 'is_active']);
            $table->unique(['user_id', 'payment_method_id', 'provider']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
