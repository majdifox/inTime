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
        // First, ensure the enum type exists
        DB::statement("DO $$
        BEGIN
            IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'reservation_status_enum') THEN
                CREATE TYPE reservation_status_enum AS ENUM('pending', 'matching', 'not_accepted', 'accepted', 'cancelled');
            END IF;
        END $$;");

        // If reservation_status column exists, handle its conversion carefully
        if (Schema::hasColumn('rides', 'reservation_status')) {
            // Add a new temporary column with the enum type
            DB::statement("ALTER TABLE rides ADD COLUMN reservation_status_new reservation_status_enum");

            // Update the new column with converted values
            DB::statement("UPDATE rides 
                SET reservation_status_new = 
                    CASE 
                        WHEN reservation_status = 'pending' THEN 'pending'::reservation_status_enum
                        WHEN reservation_status = 'matching' THEN 'matching'::reservation_status_enum
                        WHEN reservation_status = 'not_accepted' THEN 'not_accepted'::reservation_status_enum
                        WHEN reservation_status = 'accepted' THEN 'accepted'::reservation_status_enum
                        WHEN reservation_status = 'cancelled' THEN 'cancelled'::reservation_status_enum
                        ELSE 'pending'::reservation_status_enum
                    END");

            // Drop the old column
            DB::statement("ALTER TABLE rides DROP COLUMN reservation_status");

            // Rename the new column
            DB::statement("ALTER TABLE rides RENAME COLUMN reservation_status_new TO reservation_status");
        }

        // Add ride_preferences field to passengers table
        Schema::table('passengers', function (Blueprint $table) {
            if (!Schema::hasColumn('passengers', 'ride_preferences')) {
                $table->json('ride_preferences')->nullable()->after('preferences');
            }
        });

        // Update the vehicles table with new vehicle types
        if (Schema::hasColumn('vehicles', 'type')) {
            // Ensure vehicle_type_enum exists
            DB::statement("DO $$
            BEGIN
                IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'vehicle_type_enum') THEN
                    CREATE TYPE vehicle_type_enum AS ENUM('share', 'comfort', 'women', 'wav', 'black');
                END IF;
            END $$;");
            
            // Add a new temporary column with the enum type
            DB::statement("ALTER TABLE vehicles ADD COLUMN type_new vehicle_type_enum");

            // Update the new column with converted values
            DB::statement("UPDATE vehicles 
                SET type_new = 
                    CASE 
                        WHEN lower(type) = 'share' THEN 'share'::vehicle_type_enum
                        WHEN lower(type) = 'comfort' THEN 'comfort'::vehicle_type_enum
                        WHEN lower(type) = 'women' THEN 'women'::vehicle_type_enum
                        WHEN lower(type) = 'wav' THEN 'wav'::vehicle_type_enum
                        WHEN lower(type) = 'black' THEN 'black'::vehicle_type_enum
                        ELSE 'share'::vehicle_type_enum
                    END");

            // Drop the old column
            DB::statement("ALTER TABLE vehicles DROP COLUMN type");

            // Rename the new column
            DB::statement("ALTER TABLE vehicles RENAME COLUMN type_new TO type");
        }

        // Add base_fare and per_km_price to rides table
        Schema::table('rides', function (Blueprint $table) {
            if (!Schema::hasColumn('rides', 'base_fare')) {
                $table->decimal('base_fare', 10, 2)->nullable()->after('price');
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
            if (!Schema::hasColumn('rides', 'vehicle_type')) {
                $table->string('vehicle_type')->nullable()->after('wait_time_minutes');
            }
        });

        // Add driver_response_time to store how quickly drivers respond
        Schema::table('drivers', function (Blueprint $table) {
            if (!Schema::hasColumn('drivers', 'driver_response_time')) {
                $table->decimal('driver_response_time', 5, 2)->nullable()->after('is_verified');
            }
            if (!Schema::hasColumn('drivers', 'women_only_driver')) {
                $table->boolean('women_only_driver')->default(false)->after('driver_response_time');
            }
        });

        // Add gender restrictions to user table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'women_only_rides')) {
                $table->boolean('women_only_rides')->default(false)->after('gender');
            }
        });

        // Create a ride_requests table to track driver matching attempts
        if (!Schema::hasTable('ride_requests')) {
            Schema::create('ride_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('ride_id')->constrained()->onDelete('cascade');
                $table->foreignId('driver_id')->constrained()->onDelete('cascade');
                $table->string('status')->default('pending');
                $table->timestamp('requested_at')->nullable();
                $table->timestamp('responded_at')->nullable();
                $table->timestamps();
            });
            
            // Add check constraint for status
            DB::statement("ALTER TABLE ride_requests ADD CONSTRAINT check_status_validity CHECK (status IN ('pending', 'accepted', 'rejected', 'expired'))");
        }

        // Create fare_settings table for configurable pricing
        if (!Schema::hasTable('fare_settings')) {
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop additional columns from passengers table
        Schema::table('passengers', function (Blueprint $table) {
            if (Schema::hasColumn('passengers', 'ride_preferences')) {
                $table->dropColumn('ride_preferences');
            }
        });

        // Remove added columns from rides table
        Schema::table('rides', function (Blueprint $table) {
            if (Schema::hasColumn('rides', 'base_fare')) {
                $table->dropColumn('base_fare');
            }
            if (Schema::hasColumn('rides', 'per_km_price')) {
                $table->dropColumn('per_km_price');
            }
            if (Schema::hasColumn('rides', 'distance_in_km')) {
                $table->dropColumn('distance_in_km');
            }
            if (Schema::hasColumn('rides', 'surge_multiplier')) {
                $table->dropColumn('surge_multiplier');
            }
            if (Schema::hasColumn('rides', 'wait_time_minutes')) {
                $table->dropColumn('wait_time_minutes');
            }
            if (Schema::hasColumn('rides', 'vehicle_type')) {
                $table->dropColumn('vehicle_type');
            }
            
            // Revert reservation_status to string
            if (Schema::hasColumn('rides', 'reservation_status')) {
                DB::statement("ALTER TABLE rides ALTER COLUMN reservation_status TYPE varchar(255)");
            }
        });

        // Remove added columns from drivers table
        Schema::table('drivers', function (Blueprint $table) {
            if (Schema::hasColumn('drivers', 'driver_response_time')) {
                $table->dropColumn('driver_response_time');
            }
            if (Schema::hasColumn('drivers', 'women_only_driver')) {
                $table->dropColumn('women_only_driver');
            }
        });

        // Remove added columns from users table
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'women_only_rides')) {
                $table->dropColumn('women_only_rides');
            }
        });

        // Drop additional tables
        Schema::dropIfExists('ride_requests');
        Schema::dropIfExists('fare_settings');

        // Drop custom enum types
        try {
            DB::statement("DROP TYPE IF EXISTS vehicle_type_enum CASCADE");
        } catch (\Exception $e) {
            // Ignore errors
        }

        try {
            DB::statement("DROP TYPE IF EXISTS reservation_status_enum CASCADE");
        } catch (\Exception $e) {
            // Ignore errors
        }
    }
};