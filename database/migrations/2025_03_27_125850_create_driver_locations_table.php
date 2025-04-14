<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the table if it exists to ensure a clean slate
        Schema::dropIfExists('driver_locations');
        
        // Create the table with all required columns
        Schema::create('driver_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained()->onDelete('cascade');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->timestamp('last_updated')->useCurrent();
            $table->timestamps();
            
            // Each driver should have only one location
            $table->unique('driver_id');
        });
        
        // Add indexes for faster location queries
        DB::statement("CREATE INDEX idx_driver_locations_coordinates ON driver_locations USING btree (latitude, longitude)");
        
        // Add indexes to rides table for coordinates
        DB::statement("CREATE INDEX idx_rides_pickup_coordinates ON rides USING btree (pickup_latitude, pickup_longitude)");
        DB::statement("CREATE INDEX idx_rides_dropoff_coordinates ON rides USING btree (dropoff_latitude, dropoff_longitude)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes first
        DB::statement("DROP INDEX IF EXISTS idx_rides_pickup_coordinates");
        DB::statement("DROP INDEX IF EXISTS idx_rides_dropoff_coordinates");
        DB::statement("DROP INDEX IF EXISTS idx_driver_locations_coordinates");
        
        // Then drop the table
        Schema::dropIfExists('driver_locations');
    }
};