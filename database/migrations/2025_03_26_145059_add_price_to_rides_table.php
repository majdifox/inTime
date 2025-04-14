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
            // Ensure the column doesn't already exist before adding
            if (!Schema::hasColumn('rides', 'price')) {
                $table->decimal('price', 10, 2)->nullable()->after('ride_cost');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rides', function (Blueprint $table) {
            if (Schema::hasColumn('rides', 'price')) {
                $table->dropColumn('price');
            }
        });
    }
};