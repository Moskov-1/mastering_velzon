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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            
            // Relationships
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_method_id')->nullable()->constrained('user_payment_methods')->onDelete('set null');
            
            // Gateway information
            $table->string('payment_gateway')->default('stripe');
            $table->string('gateway_payment_intent_id')->nullable(); // Stripe PaymentIntent ID
            $table->string('gateway_setup_intent_id')->nullable(); // Stripe SetupIntent ID
            $table->string('gateway_charge_id')->nullable(); // Stripe Charge ID
            $table->string('gateway_customer_id')->nullable(); // Stripe Customer ID
            
            // Amount details
            $table->decimal('amount', 10, 2); // In smallest currency unit (cents)
            $table->decimal('amount_received', 10, 2)->nullable(); // Actual amount received
            $table->string('currency', 3)->default('usd');
            $table->decimal('application_fee_amount', 10, 2)->nullable(); // For connected accounts
            $table->decimal('gateway_fee', 10, 2)->default(0.00);
            $table->decimal('net_amount', 10, 2)->nullable(); // Amount after fees
            
            // Payment status (aligned with Stripe)
            $table->enum('status', [
                'requires_payment_method',
                'requires_confirmation',
                'requires_action',
                'processing',
                'requires_capture',
                'canceled',
                'succeeded',
                'failed'
            ])->default('requires_payment_method');
            
            // Payment flow tracking
            $table->string('cancellation_reason')->nullable(); // duplicate, fraudulent, abandoned, etc.
            $table->text('failure_message')->nullable();
            $table->string('failure_code')->nullable();
            
            // 3D Secure and authentication
            $table->json('next_action')->nullable(); // For 3DS authentication
            $table->string('client_secret')->nullable(); // For client-side confirmation
            
            // Digital wallet specific
            $table->json('wallet_details')->nullable(); // Google Pay/Apple Pay data
            
            // Timestamps for payment lifecycle
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('captured_at')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            
            // Metadata and descriptions
            $table->json('metadata')->nullable();
            $table->text('description')->nullable();
            $table->json('gateway_response')->nullable(); // Full gateway response
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['order_id']);
            $table->index(['gateway_payment_intent_id']);
            $table->index(['gateway_charge_id']);
            $table->index(['created_at', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
