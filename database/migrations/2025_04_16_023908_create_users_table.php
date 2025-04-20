<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('profile_picture')->nullable();
            $table->string('phone')->unique();
            $table->date('birthday')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->boolean('women_only_rides')->default(false);
            $table->enum('role', ['driver', 'passenger', 'admin']);
            $table->boolean('is_online')->default(false);
            $table->enum('account_status', ['deactivated', 'pending', 'activated', 'suspended', 'deleted'])->default('deactivated');
            $table->decimal('total_income', 10, 2)->default(0);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};