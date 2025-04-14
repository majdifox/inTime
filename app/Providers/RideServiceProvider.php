<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\RideMatchingService;

class RideServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(RideMatchingService::class, function ($app) {
            return new RideMatchingService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}