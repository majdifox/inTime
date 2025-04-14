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
        // Drop the table if it exists
        Schema::dropIfExists('fare_settings');

        // Create the table from scratch with all required columns
        Schema::create('fare_settings', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_type');
            $table->decimal('base_fare', 10, 2);
            $table->decimal('per_km_price', 10, 2);
            $table->decimal('per_minute_price', 10, 2)->default(0);
            $table->decimal('minimum_fare', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fare_settings');
    }
};