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
            if (!Schema::hasColumn('rides', 'available_seats')) {
                $table->integer('available_seats')->nullable();
            }
            
            if (!Schema::hasColumn('rides', 'price')) {
                $table->decimal('price', 10, 2)->nullable();
            }
            
            if (!Schema::hasColumn('rides', 'notes')) {
                $table->text('notes')->nullable();
            }
            
            if (!Schema::hasColumn('rides', 'vehicle_id')) {
                $table->foreignId('vehicle_id')->nullable()->constrained()->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rides', function (Blueprint $table) {
            // Only drop columns that exist
            if (Schema::hasColumn('rides', 'available_seats')) {
                $table->dropColumn('available_seats');
            }
            
            if (Schema::hasColumn('rides', 'price')) {
                $table->dropColumn('price');
            }
            
            if (Schema::hasColumn('rides', 'notes')) {
                $table->dropColumn('notes');
            }
            
            if (Schema::hasColumn('rides', 'vehicle_id')) {
                $table->dropColumn('vehicle_id');
            }
        });
    }
};