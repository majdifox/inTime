<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_picture',
        'phone',
        'birthday',
        'gender',
        'role',
        'is_online',
        'account_status',
        'total_income',
        'women_only_rides',
        'ride_suspension_until',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'birthday' => 'date',
        'is_online' => 'boolean',
        'women_only_rides' => 'boolean',
        'ride_suspension_until' => 'datetime',
    ];

    /**
     * Get the driver record associated with the user.
     */
    public function driver()
    {
        return $this->hasOne(Driver::class);
    }

    /**
     * Get the passenger record associated with the user.
     */
    public function passenger()
    {
        return $this->hasOne(Passenger::class);
    }
    
    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }
    
    /**
     * Check if user is driver
     */
    public function isDriver()
    {
        return $this->role === 'driver';
    }
    
    /**
     * Check if user is passenger
     */
    public function isPassenger()
    {
        return $this->role === 'passenger';
    }
    
    /**
     * Check if user is a female user and can use women-only features
     */
    public function canUseWomenOnlyFeatures()
    {
        return $this->gender === 'female';
    }
    
    /**
     * Check if user has fully functional location sharing (for drivers)
     */
    public function hasActiveLocationSharing()
    {
        if (!$this->is_online || !$this->isDriver()) {
            return false;
        }
        
        return $this->driver && 
               $this->driver->driverLocation && 
               $this->driver->driverLocation->last_updated && 
               $this->driver->driverLocation->last_updated->gt(now()->subMinutes(5));
    }
    
    /**
     * Check if user is currently suspended from requesting rides
     */
    public function isRideSuspended()
    {
        return $this->ride_suspension_until && $this->ride_suspension_until->gt(now());
    }
    
    /**
     * Suspend the user from requesting rides for a specified number of hours
     */
    public function suspendRidesFor($hours)
    {
        $this->ride_suspension_until = now()->addHours($hours);
        $this->save();
    }
    
    /**
     * Get recent cancelled rides for this passenger
     */
    public function getRecentCancelledRidesCount($minutesWindow = 30)
    {
        if (!$this->passenger) {
            return 0;
        }
        
        return Ride::where('passenger_id', $this->passenger->id)
            ->where('reservation_status', 'cancelled')
            ->where('updated_at', '>=', now()->subMinutes($minutesWindow))
            ->count();
    }
    
    /**
     * Check if passenger should be suspended for excessive cancellations
     */
    public function shouldBeSuspendedForCancellations()
    {
        // Consider suspending if 3+ cancellations in 30 minutes
        return $this->getRecentCancelledRidesCount(30) >= 3;
    }
    
    /**
     * Check if this user can see or be matched with drivers in women-only mode
     */
    public function canAccessWomenOnlyDrivers()
    {
        return $this->gender === 'female';
    }
    
    /**
     * Check if this user driver can enable women-only mode
     */
    public function canEnableWomenOnlyMode()
    {
        return $this->gender === 'female' && $this->isDriver();
    }
}