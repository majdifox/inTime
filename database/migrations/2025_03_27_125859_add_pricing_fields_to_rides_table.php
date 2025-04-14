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
        Schema::table('rides', function (Blueprint $table) {
            if (!Schema::hasColumn('rides', 'base_fare')) {
                $table->decimal('base_fare', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('rides', 'per_km_price')) {
                $table->decimal('per_km_price', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('rides', 'distance_in_km')) {
                $table->decimal('distance_in_km', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('rides', 'surge_multiplier')) {
                $table->decimal('surge_multiplier', 4, 2)->default(1.0);
            }
            if (!Schema::hasColumn('rides', 'wait_time_minutes')) {
                $table->integer('wait_time_minutes')->default(0);
            }
            if (!Schema::hasColumn('rides', 'vehicle_type')) {
                $table->string('vehicle_type')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rides', function (Blueprint $table) {
            //
        });
    }
};
