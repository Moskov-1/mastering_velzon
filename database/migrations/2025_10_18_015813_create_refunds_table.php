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
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Refund details
            $table->string('gateway_refund_id')->nullable(); // Stripe Refund ID
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('usd');
            $table->decimal('gateway_fee_refunded', 10, 2)->default(0.00);
            
            // Refund status
            $table->enum('status', [
                'pending',
                'succeeded',
                'failed',
                'canceled'
            ])->default('pending');
            
            // Refund reason
            $table->enum('reason', [
                'duplicate',
                'fraudulent',
                'requested_by_customer',
                'expired_uncaptured_charge'
            ])->nullable();
            
            $table->text('failure_reason')->nullable();
            $table->json('metadata')->nullable();
            $table->json('gateway_response')->nullable();
            
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            
            $table->index(['payment_id']);
            $table->index(['gateway_refund_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};
