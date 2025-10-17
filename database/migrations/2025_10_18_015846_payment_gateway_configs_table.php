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
        Schema::create('payment_gateway_configs', function (Blueprint $table) {
            $table->id();
            $table->string('gateway_name'); // stripe, paypal, etc.
            $table->string('environment'); // sandbox, production
            $table->boolean('is_active')->default(false);
            $table->boolean('is_default')->default(false);
            
            // Configuration (encrypted)
            $table->text('public_key');
            $table->text('secret_key');
            $table->text('webhook_secret')->nullable();
            
            // Supported features
            $table->json('supported_currencies')->nullable();
            $table->json('supported_payment_methods')->nullable(); // card, google_pay, etc.
            $table->json('supported_countries')->nullable();
            
            // Gateway-specific settings
            $table->json('gateway_config')->nullable();
            
            $table->timestamps();
            
            $table->unique(['gateway_name', 'environment']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_gateway_configs');
    }
};
