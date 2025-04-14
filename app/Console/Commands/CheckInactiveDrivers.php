<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckInactiveDrivers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-inactive-drivers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
{
    $inactiveTimeout = now()->subMinutes(5);
    
    $users = User::where('role', 'driver')
        ->where('is_online', true)
        ->where(function($query) use ($inactiveTimeout) {
            $query->where('last_active', '<', $inactiveTimeout)
                  ->orWhereNull('last_active');
        })
        ->get();
        
    foreach($users as $user) {
        // Set user offline
        $user->is_online = false;
        $user->save();
        
        Log::info("Driver #{$user->id} automatically set offline due to inactivity");
        
        // Expire any pending requests
        $driver = Driver::where('user_id', $user->id)->first();
        if ($driver) {
            RideRequest::where('driver_id', $driver->id)
                ->where('status', 'pending')
                ->update(['status' => 'expired']);
        }
    }
    
    $this->info("Set " . count($users) . " inactive drivers to offline status");
}
}
