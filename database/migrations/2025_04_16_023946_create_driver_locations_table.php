<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
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
        
        // Add index for coordinates
        DB::statement("CREATE INDEX idx_driver_locations_coordinates ON driver_locations USING btree (latitude, longitude)");
    }

    public function down(): void
    {
        // Drop index first
        DB::statement("DROP INDEX IF EXISTS idx_driver_locations_coordinates");
        
        Schema::dropIfExists('driver_locations');
    }
};