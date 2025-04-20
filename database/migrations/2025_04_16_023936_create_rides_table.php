<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the type first if it exists
        DB::statement("DROP TYPE IF EXISTS reservation_status_enum CASCADE");
        
        // Create the enum type
        DB::statement("CREATE TYPE reservation_status_enum AS ENUM('pending', 'matching', 'not_accepted', 'accepted', 'cancelled')");
        
        Schema::create('rides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('passenger_id')->constrained()->onDelete('cascade');
            $table->foreignId('driver_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('vehicle_id')->nullable()->constrained()->onDelete('set null');
            $table->dateTime('reservation_date');
            // Create without default first
            $table->string('reservation_status');
            $table->dateTime('pickup_time')->nullable();
            $table->string('pickup_location');
            $table->decimal('pickup_latitude', 10, 7)->nullable();
            $table->decimal('pickup_longitude', 10, 7)->nullable();
            $table->dateTime('dropoff_time')->nullable();
            $table->string('dropoff_location');
            $table->decimal('dropoff_latitude', 10, 7)->nullable();
            $table->decimal('dropoff_longitude', 10, 7)->nullable();
            $table->enum('ride_status', ['ongoing', 'completed'])->default('ongoing');
            $table->decimal('ride_cost', 10, 2)->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('base_fare', 10, 2)->nullable();
            $table->decimal('per_km_price', 10, 2)->nullable();
            $table->decimal('distance_in_km', 10, 2)->nullable();
            $table->decimal('surge_multiplier', 4, 2)->default(1.0);
            $table->integer('wait_time_minutes')->default(0);
            $table->string('vehicle_type')->nullable();
            $table->integer('available_seats')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_reviewed')->default(false);
            $table->timestamps();
        });
        
        // Fill all existing rows with 'pending' value
        DB::statement("UPDATE rides SET reservation_status = 'pending'");
        
        // Alter the column to use the enum type
        DB::statement("ALTER TABLE rides ALTER COLUMN reservation_status TYPE reservation_status_enum USING reservation_status::reservation_status_enum");
        
        // Now add default constraint
        DB::statement("ALTER TABLE rides ALTER COLUMN reservation_status SET DEFAULT 'pending'::reservation_status_enum");
        
        // Add indexes for coordinates
        DB::statement("CREATE INDEX idx_rides_pickup_coordinates ON rides USING btree (pickup_latitude, pickup_longitude)");
        DB::statement("CREATE INDEX idx_rides_dropoff_coordinates ON rides USING btree (dropoff_latitude, dropoff_longitude)");
    }

    public function down(): void
    {
        // Drop indexes first
        DB::statement("DROP INDEX IF EXISTS idx_rides_pickup_coordinates");
        DB::statement("DROP INDEX IF EXISTS idx_rides_dropoff_coordinates");
        
        Schema::dropIfExists('rides');
        DB::statement("DROP TYPE IF EXISTS reservation_status_enum CASCADE");
    }
};