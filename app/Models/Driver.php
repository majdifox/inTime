<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'license_number',
        'license_expiry',
        'license_photo',
        'insurance_document',
        'good_conduct_certificate',
        'rating',
        'completed_rides',
        'balance',
        'is_verified',
        'driver_response_time',
        'women_only_driver',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'license_expiry' => 'date',
        'rating' => 'float',
        'completed_rides' => 'integer',
        'balance' => 'decimal:2',
        'is_verified' => 'boolean',
        'driver_response_time' => 'float',
        'women_only_driver' => 'boolean',
    ];

    /**
     * Get the user that owns the driver profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the vehicle associated with the driver.
     */
    public function vehicle()
    {
        return $this->hasOne(Vehicle::class);
    }
    
    /**
     * Get the rides for the driver.
     */
    public function rides()
    {
        return $this->hasMany(Ride::class);
    }
    
    /**
     * Get the driver's current location.
     */
    public function driverLocation()
    {
        return $this->hasOne(DriverLocation::class);
    }
    
    /**
     * Get ride requests for the driver.
     */
    public function rideRequests()
    {
        return $this->hasMany(RideRequest::class);
    }
    
    /**
     * Check if driver is eligible for women-only rides.
     * A driver is eligible for women-only rides if they are female and 
     * have opted in to women_only_driver mode.
     */
    public function isEligibleForWomenOnlyRides()
    {
        return $this->women_only_driver && $this->user->gender === 'female';
    }
    
    /**
     * Check if driver should be shown to a specific passenger based on gender preferences
     *
     * @param User $passenger The passenger user
     * @return bool Whether this driver should be visible to this passenger
     */
    public function isVisibleToPassenger(User $passenger)
    {
        // If driver has women_only_driver enabled and is female, they should only be shown to female passengers
        if ($this->women_only_driver && $this->user->gender === 'female') {
            return $passenger->gender === 'female';
        }
        
        // If passenger has women_only_rides enabled, they should only see female drivers with women_only_driver enabled
        if ($passenger->women_only_rides) {
            return $this->user->gender === 'female' && $this->women_only_driver;
        }
        
        // In all other cases, drivers are visible
        return true;
    }
    
    /**
     * Check if driver is fully available (logged in, online, location shared)
     */
    public function isFullyAvailable()
    {
        return $this->user->is_online && 
               $this->driverLocation && 
               $this->driverLocation->last_updated && 
               $this->driverLocation->last_updated->gt(now()->subMinutes(5)) &&
               $this->is_verified && 
               $this->user->account_status === 'activated' &&
               !$this->hasActiveRide();
    }
    
    /**
     * Check if driver documents are valid.
     */
    public function documentsValid()
    {
        $now = now();
        return $this->license_expiry->gt($now);
    }
    
    /**
     * Get driver's full name.
     */
    public function getFullNameAttribute()
    {
        return $this->user->name;
    }
    
    /**
     * Get driver's gender.
     */
    public function getGenderAttribute()
    {
        return $this->user->gender;
    }
    
    /**
     * Check if driver is currently available for ride requests.
     */
    public function isAvailable()
    {
        // Driver is available if online, verified, and not on an active ride
        return $this->user->is_online && 
               $this->is_verified && 
               $this->user->account_status === 'activated' &&
               !$this->hasActiveRide();
    }
    
    /**
     * Check if driver has an active ride.
     */
    public function hasActiveRide()
    {
        return $this->rides()
            ->where('ride_status', 'ongoing')
            ->whereNull('dropoff_time')
            ->exists();
    }
    
    /**
     * Get the current active ride for the driver.
     */
    public function getActiveRide()
    {
        return $this->rides()
            ->where('ride_status', 'ongoing')
            ->whereNull('dropoff_time')
            ->first();
    }
    
    /**
     * Scope query to only include available drivers.
     */
    public function scopeAvailable($query)
    {
        return $query->whereHas('user', function($q) {
            $q->where('is_online', true)
              ->where('account_status', 'activated');
        })
        ->where('is_verified', true)
        ->whereDoesntHave('rides', function($q) {
            $q->where('ride_status', 'ongoing')
              ->whereNull('dropoff_time');
        });
    }
    
    /**
     * Scope query to include only fully available drivers (online and location shared recently)
     */
    public function scopeFullyAvailable($query)
    {
        return $query->whereHas('user', function($q) {
            $q->where('is_online', true)
              ->where('account_status', 'activated');
        })
        ->where('is_verified', true)
        ->whereHas('driverLocation', function($q) {
            $q->where('last_updated', '>', now()->subMinutes(5));
        })
        ->whereDoesntHave('rides', function($q) {
            $q->where('ride_status', 'ongoing')
              ->whereNull('dropoff_time');
        });
    }
    
    /**
     * Scope query to only include women-only drivers.
     */
    public function scopeWomenOnly($query)
    {
        return $query->where('women_only_driver', true)
            ->whereHas('user', function($q) {
                $q->where('gender', 'female');
            });
    }
    
    /**
     * Get all earnings for the driver.
     */
    public function getEarnings()
    {
        return $this->rides()
            ->where('ride_status', 'completed')
            ->sum('price');
    }
    
    /**
     * Add a review for the driver.
     */
    public function addReview($rideId, $reviewerId, $rating, $comment = null)
    {
        $review = new Review();
        $review->ride_id = $rideId;
        $review->reviewer_id = $reviewerId;
        $review->reviewed_id = $this->user_id;
        $review->rating = $rating;
        $review->comment = $comment;
        $review->save();
        
        // Update the driver's rating (average of all ratings)
        $this->updateRating();
        
        return $review;
    }
    
    /**
     * Update the driver's rating based on all reviews.
     */
    public function updateRating()
    {
        $reviews = Review::whereHas('ride', function($q) {
            $q->where('driver_id', $this->id);
        })->get();
        
        if ($reviews->count() > 0) {
            $this->rating = $reviews->avg('rating');
            $this->save();
        }
        
        return $this->rating;
    }
    
    /**
     * Get a count of how many rides the driver has rejected in the last hour
     */
    public function getRejectedRidesLastHourCount()
    {
        return $this->rideRequests()
            ->where('status', 'rejected')
            ->where('responded_at', '>=', now()->subHour())
            ->count();
    }
    
    /**
     * Get a count of how many rides the driver has cancelled in the last two hours
     */
    public function getCancelledRidesLastTwoHoursCount()
    {
        return $this->rides()
            ->where('reservation_status', 'cancelled')
            ->where('updated_at', '>=', now()->subHours(2))
            ->count();
    }
    
    /**
     * Check if driver should be temporarily suspended due to too many rejections
     */
    public function shouldBeSuspendedForRejections()
    {
        return $this->getRejectedRidesLastHourCount() >= 5;
    }
    
    /**
     * Check if driver should be temporarily suspended due to too many cancellations
     */
    public function shouldBeSuspendedForCancellations()
    {
        return $this->getCancelledRidesLastTwoHoursCount() >= 3;
    }

    public function updateCompletedRidesCount()
{
    $completedRidesCount = $this->rides()
        ->where('ride_status', 'completed')
        ->count();
    
    if ($this->completed_rides !== $completedRidesCount) {
        $this->completed_rides = $completedRidesCount;
        $this->save();
    }
    
    return $this->completed_rides;
}
}