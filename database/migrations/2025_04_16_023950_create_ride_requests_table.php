<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
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

    public function down(): void
    {
        Schema::dropIfExists('ride_requests');
    }
};