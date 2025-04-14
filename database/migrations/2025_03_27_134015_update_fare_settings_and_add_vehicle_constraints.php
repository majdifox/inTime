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
        // Ensure the enum type exists
        DB::statement("DO $$
        BEGIN
            IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'vehicle_type_enum') THEN
                CREATE TYPE vehicle_type_enum AS ENUM('share', 'comfort', 'women', 'wav', 'black');
            END IF;
        END $$;");

        // Instead of creating fare_settings table, just truncate and insert new values
        if (Schema::hasTable('fare_settings')) {
            // Clear existing fare settings
            DB::table('fare_settings')->truncate();
            
            // Insert updated fare settings
            DB::table('fare_settings')->insert([
                ['vehicle_type' => 'share', 'base_fare' => 50.00, 'per_km_price' => 15.00, 'per_minute_price' => 0.00, 'minimum_fare' => 50.00, 'created_at' => now(), 'updated_at' => now()],
                ['vehicle_type' => 'comfort', 'base_fare' => 80.00, 'per_km_price' => 20.00, 'per_minute_price' => 0.00, 'minimum_fare' => 80.00, 'created_at' => now(), 'updated_at' => now()],
                ['vehicle_type' => 'women', 'base_fare' => 100.00, 'per_km_price' => 25.00, 'per_minute_price' => 0.00, 'minimum_fare' => 100.00, 'created_at' => now(), 'updated_at' => now()],
                ['vehicle_type' => 'wav', 'base_fare' => 120.00, 'per_km_price' => 30.00, 'per_minute_price' => 0.00, 'minimum_fare' => 120.00, 'created_at' => now(), 'updated_at' => now()],
                ['vehicle_type' => 'black', 'base_fare' => 140.00, 'per_km_price' => 35.00, 'per_minute_price' => 0.00, 'minimum_fare' => 140.00, 'created_at' => now(), 'updated_at' => now()]
            ]);
        }
        
        // For vehicles table, add constraint if not exists
        if (Schema::hasTable('vehicles')) {
            // Add a new temporary column with the enum type
            DB::statement("ALTER TABLE vehicles ADD COLUMN type_new vehicle_type_enum");

            // Update the new column with converted values
            DB::statement("UPDATE vehicles 
                SET type_new = 
                    CASE 
                        WHEN type::text = 'share' THEN 'share'::vehicle_type_enum
                        WHEN type::text = 'comfort' THEN 'comfort'::vehicle_type_enum
                        WHEN type::text = 'women' THEN 'women'::vehicle_type_enum
                        WHEN type::text = 'wav' THEN 'wav'::vehicle_type_enum
                        WHEN type::text = 'black' THEN 'black'::vehicle_type_enum
                        ELSE 'share'::vehicle_type_enum
                    END");

            // Drop the old column
            DB::statement("ALTER TABLE vehicles DROP COLUMN type");

            // Rename the new column
            DB::statement("ALTER TABLE vehicles RENAME COLUMN type_new TO type");
            
            // Try to add constraint, but it might already exist
            try {
                DB::statement("ALTER TABLE vehicles ADD CONSTRAINT check_vehicle_type CHECK (type IN ('share', 'comfort', 'women', 'wav', 'black'))");
            } catch (\Exception $e) {
                // Constraint might already exist, which is fine
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the constraint
        try {
            DB::statement("ALTER TABLE vehicles DROP CONSTRAINT check_vehicle_type");
        } catch (\Exception $e) {
            // Constraint might not exist
        }

        // Revert the column type to text
        if (Schema::hasTable('vehicles')) {
            DB::statement("ALTER TABLE vehicles ALTER COLUMN type TYPE text");
        }

        // Drop the enum type
        try {
            DB::statement("DROP TYPE vehicle_type_enum CASCADE");
        } catch (\Exception $e) {
            // Type might not exist
        }
    }
};