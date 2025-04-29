<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User; // Add this line
use App\Observers\UserObserver; // Add this line

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register the observer to change default account status based on role
        User::observe(UserObserver::class);
    }
}