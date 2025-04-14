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
        Schema::create('rides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('passenger_id')->constrained()->onDelete('cascade');
            $table->foreignId('driver_id')->constrained()->onDelete('cascade');
            $table->dateTime('reservation_date');
            $table->enum('reservation_status', ['pending','not_accepted', 'accepted', 'cancelled'])->default('pending');
            $table->dateTime('pickup_time')->nullable();
            $table->string('pickup_location');
            $table->dateTime('dropoff_time')->nullable();
            $table->string('dropoff_location');
            $table->enum('ride_status', ['ongoing','completed'])->default('ongoing');
            $table->decimal('ride_cost', 10, 2)->nullable();
            $table->boolean('is_reviewed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rides');
    }
};
