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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'women_only_rides')) {
                $table->boolean('women_only_rides')->default(false);
            }
        });
        
        Schema::table('drivers', function (Blueprint $table) {
            if (!Schema::hasColumn('drivers', 'women_only_driver')) {
                $table->boolean('women_only_driver')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users_and_drivers', function (Blueprint $table) {
            //
        });
    }
};
