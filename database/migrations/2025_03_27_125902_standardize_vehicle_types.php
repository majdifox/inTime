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

        // If the vehicles table has a 'type' column
        if (Schema::hasColumn('vehicles', 'type')) {
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
        }

        // Add a check constraint for valid vehicle types
        try {
            DB::statement("ALTER TABLE vehicles ADD CONSTRAINT check_vehicle_type CHECK (type IN ('share', 'comfort', 'women', 'wav', 'black'))");
        } catch (\Exception $e) {
            // Constraint might already exist
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the column to text type
        if (Schema::hasColumn('vehicles', 'type')) {
            DB::statement("ALTER TABLE vehicles ALTER COLUMN type TYPE text");
        }

        // Drop the check constraint
        try {
            DB::statement("ALTER TABLE vehicles DROP CONSTRAINT check_vehicle_type");
        } catch (\Exception $e) {
            // Constraint might not exist
        }

        // Drop the enum type
        try {
            DB::statement("DROP TYPE vehicle_type_enum CASCADE");
        } catch (\Exception $e) {
            // Type might not exist
        }
    }
};