<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fare_settings', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_type');
            $table->decimal('base_fare', 10, 2);
            $table->decimal('per_km_price', 10, 2);
            $table->decimal('per_minute_price', 10, 2)->default(0);
            $table->decimal('minimum_fare', 10, 2)->default(0);
            $table->timestamps();
            
            // Ensure unique vehicle types
            $table->unique('vehicle_type');
        });
        
        // Seed with initial fare settings
        DB::table('fare_settings')->insert([
            ['vehicle_type' => 'basic', 'base_fare' => 20.00, 'per_km_price' => 5.00, 'per_minute_price' => 0.00, 'minimum_fare' => 5.00, 'created_at' => now(), 'updated_at' => now()],
            ['vehicle_type' => 'comfort', 'base_fare' => 30.00, 'per_km_price' => 7.00, 'per_minute_price' => 0.00, 'minimum_fare' => 7.00, 'created_at' => now(), 'updated_at' => now()],
            ['vehicle_type' => 'wav', 'base_fare' => 50.00, 'per_km_price' => 10.00, 'per_minute_price' => 0.00, 'minimum_fare' => 10.00, 'created_at' => now(), 'updated_at' => now()],
            ['vehicle_type' => 'black', 'base_fare' => 70.00, 'per_km_price' => 12.00, 'per_minute_price' => 0.00, 'minimum_fare' => 12.00, 'created_at' => now(), 'updated_at' => now()]
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('fare_settings');
    }
};