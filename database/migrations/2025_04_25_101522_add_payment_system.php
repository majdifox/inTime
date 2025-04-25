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
        // Add payment-related columns to the rides table
        Schema::table('rides', function (Blueprint $table) {
            $table->boolean('is_paid')->default(false);
            $table->string('payment_method')->nullable();
            $table->string('payment_status')->nullable();
            $table->timestamp('payment_confirmed_at')->nullable();
        });

        // Create payments table
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ride_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('payment_method');
            $table->string('status');
            $table->string('stripe_payment_id')->nullable();
            $table->json('payment_details')->nullable();
            $table->timestamps();
        });

        // Create payment_methods table for saved cards
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('stripe_payment_method_id');
            $table->string('brand');
            $table->string('last4');
            $table->integer('exp_month');
            $table->integer('exp_year');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // Add Stripe customer ID to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('stripe_customer_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rides', function (Blueprint $table) {
            $table->dropColumn('is_paid');
            $table->dropColumn('payment_method');
            $table->dropColumn('payment_status');
            $table->dropColumn('payment_confirmed_at');
        });

        Schema::dropIfExists('payments');
        Schema::dropIfExists('payment_methods');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('stripe_customer_id');
        });
    }
};