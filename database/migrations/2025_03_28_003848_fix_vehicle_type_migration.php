<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixVehicleTypeMigration extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update passengers table to add ride_preferences
        Schema::table('passengers', function (Blueprint $table) {
            if (!Schema::hasColumn('passengers', 'ride_preferences')) {
                $table->json('ride_preferences')->nullable()->after('preferences');
            }
        });
        
        // Update users table to add women-only rides flag
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'women_only_rides')) {
                $table->boolean('women_only_rides')->default(false)->after('gender');
            }
        });
        
        // Create fare_settings table if it doesn't exist
        if (!Schema::hasTable('fare_settings')) {
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
                ['vehicle_type' => 'share', 'base_fare' => 50.00, 'per_km_price' => 15.00, 'per_minute_price' => 0.00, 'minimum_fare' => 50.00, 'created_at' => now(), 'updated_at' => now()],
                ['vehicle_type' => 'comfort', 'base_fare' => 80.00, 'per_km_price' => 20.00, 'per_minute_price' => 0.00, 'minimum_fare' => 80.00, 'created_at' => now(), 'updated_at' => now()],
                ['vehicle_type' => 'women', 'base_fare' => 100.00, 'per_km_price' => 25.00, 'per_minute_price' => 0.00, 'minimum_fare' => 100.00, 'created_at' => now(), 'updated_at' => now()],
                ['vehicle_type' => 'wav', 'base_fare' => 120.00, 'per_km_price' => 30.00, 'per_minute_price' => 0.00, 'minimum_fare' => 120.00, 'created_at' => now(), 'updated_at' => now()],
                ['vehicle_type' => 'black', 'base_fare' => 140.00, 'per_km_price' => 35.00, 'per_minute_price' => 0.00, 'minimum_fare' => 140.00, 'created_at' => now(), 'updated_at' => now()]
            ]);
        }
        
        // Update rides table to add required fields for fare calculation
        Schema::table('rides', function (Blueprint $table) {
            if (!Schema::hasColumn('rides', 'vehicle_type')) {
                $table->string('vehicle_type')->nullable()->after('ride_cost');
            }
            
            if (!Schema::hasColumn('rides', 'base_fare')) {
                $table->decimal('base_fare', 10, 2)->nullable()->after('ride_cost');
            }
            
            if (!Schema::hasColumn('rides', 'per_km_price')) {
                $table->decimal('per_km_price', 10, 2)->nullable()->after('base_fare');
            }
            
            if (!Schema::hasColumn('rides', 'distance_in_km')) {
                $table->decimal('distance_in_km', 10, 2)->nullable()->after('per_km_price');
            }
            
            if (!Schema::hasColumn('rides', 'surge_multiplier')) {
                $table->decimal('surge_multiplier', 4, 2)->default(1.0)->after('distance_in_km');
            }
            
            if (!Schema::hasColumn('rides', 'wait_time_minutes')) {
                $table->integer('wait_time_minutes')->default(0)->after('surge_multiplier');
            }
        });
        
        // For PostgreSQL, handle vehicle types as string instead of using enums
        try {
            // Add check constraint for valid vehicle types if it doesn't exist
            DB::statement("ALTER TABLE vehicles ADD CONSTRAINT IF NOT EXISTS check_vehicle_type CHECK (type IN ('share', 'comfort', 'women', 'wav', 'black'))");
        } catch (\Exception $e) {
            // Some database drivers might not support this syntax, so we'll catch and ignore
            \Log::info('Could not add check constraint: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('passengers', function (Blueprint $table) {
            if (Schema::hasColumn('passengers', 'ride_preferences')) {
                $table->dropColumn('ride_preferences');
            }
        });
        
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'women_only_rides')) {
                $table->dropColumn('women_only_rides');
            }
        });
        
        // Leave fare_settings table intact to prevent data loss
        
        Schema::table('rides', function (Blueprint $table) {
            $columns = [
                'vehicle_type', 'base_fare', 'per_km_price', 
                'distance_in_km', 'surge_multiplier', 'wait_time_minutes'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('rides', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
        
        // Try to remove the constraint
        try {
            DB::statement("ALTER TABLE vehicles DROP CONSTRAINT IF EXISTS check_vehicle_type");
        } catch (\Exception $e) {
            // Ignore errors
            \Log::info('Could not drop check constraint: ' . $e->getMessage());
        }
    }
}