<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('license_number')->unique();
            $table->date('license_expiry');
            $table->string('license_photo')->nullable();
            $table->string('insurance_document')->nullable();
            $table->string('good_conduct_certificate')->nullable();
            $table->decimal('rating', 3, 2)->nullable();
            $table->integer('completed_rides')->default(0);
            $table->decimal('balance', 10, 2)->default(0);
            $table->boolean('is_verified')->default(false);
            $table->decimal('driver_response_time', 5, 2)->nullable();
            $table->boolean('women_only_driver')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};