<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the type first if it exists
        DB::statement("DROP TYPE IF EXISTS vehicle_type_enum CASCADE");
        
        // Create the enum type
        DB::statement("CREATE TYPE vehicle_type_enum AS ENUM('basic', 'comfort', 'black', 'wav')");
        
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained()->onDelete('cascade');
            $table->string('make');
            $table->string('model');
            $table->integer('year');
            $table->string('color');
            $table->string('plate_number')->unique();
            // Create as string first
            $table->string('type');
            $table->integer('capacity');
            $table->string('vehicle_photo')->nullable();
            $table->date('insurance_expiry');
            $table->date('registration_expiry');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        
        // Alter the column to use the enum type
        DB::statement("ALTER TABLE vehicles ALTER COLUMN type TYPE vehicle_type_enum USING type::vehicle_type_enum");
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
        DB::statement("DROP TYPE IF EXISTS vehicle_type_enum CASCADE");
    }
};