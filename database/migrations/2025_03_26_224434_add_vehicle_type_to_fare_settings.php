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
        // Check if fare_settings table exists
        if (Schema::hasTable('fare_settings')) {
            // Check if vehicle_type column doesn't exist
            if (!Schema::hasColumn('fare_settings', 'vehicle_type')) {
                Schema::table('fare_settings', function (Blueprint $table) {
                    $table->string('vehicle_type')->after('id');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('fare_settings') && Schema::hasColumn('fare_settings', 'vehicle_type')) {
            Schema::table('fare_settings', function (Blueprint $table) {
                $table->dropColumn('vehicle_type');
            });
        }
    }
};