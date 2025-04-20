<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\RideMatchingService;

class Ride extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'passenger_id',
        'driver_id',
        'vehicle_id',
        'reservation_date',
        'reservation_status',
        'pickup_time',
        'pickup_location',
        'pickup_latitude',
        'pickup_longitude',
        'dropoff_time',
        'dropoff_location',
        'dropoff_latitude',
        'dropoff_longitude',
        'ride_status',
        'ride_cost',
        'price',
        'base_fare',
        'per_km_price',
        'distance_in_km',
        'surge_multiplier',
        'wait_time_minutes',
        'vehicle_type',
        'available_seats',
        'notes',
        'is_reviewed',
        'is_reviewed_by_driver',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
'reservation_date' => 'datetime',
    'pickup_time' => 'datetime',
    'dropoff_time' => 'datetime',
    'pickup_latitude' => 'decimal:7',
    'pickup_longitude' => 'decimal:7',
    'dropoff_latitude' => 'decimal:7',
    'dropoff_longitude' => 'decimal:7',
    'ride_cost' => 'decimal:2',
    'price' => 'decimal:2',
    'base_fare' => 'decimal:2',
    'per_km_price' => 'decimal:2',
    'distance_in_km' => 'decimal:2',
    'surge_multiplier' => 'decimal:2',
    'is_reviewed' => 'boolean',
    'is_reviewed_by_driver' => 'boolean',
    ];

    /**
     * Get the passenger that owns the ride.
     */
    public function passenger()
    {
        return $this->belongsTo(Passenger::class);
    }

    /**
     * Get the driver for the ride.
     */
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
    
    /**
     * Get the vehicle for the ride.
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
    
    /**
     * Get the ride requests associated with this ride.
     */
    public function rideRequests()
    {
        return $this->hasMany(RideRequest::class);
    }
    
    /**
     * Get the reviews for this ride.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    
    /**
     * Calculate the estimated time of arrival.
     */
    public function calculateETA()
    {
        if (!$this->driver || !$this->driver->driverLocation) {
            return null;
        }
        
        $rideMatchingService = app(RideMatchingService::class);
        
        return $rideMatchingService->calculateETA(
            $this->driver->driverLocation->latitude,
            $this->driver->driverLocation->longitude,
            $this->pickup_latitude,
            $this->pickup_longitude
        );
    }
    
    /**
     * Get the duration of the ride in minutes.
     */
    public function getDurationInMinutes()
    {
        if (!$this->pickup_time || !$this->dropoff_time) {
            return null;
        }
        
        return $this->pickup_time->diffInMinutes($this->dropoff_time);
    }
    
    /**
     * Calculate the total fare based on fare settings.
     * 
     * @param bool $recalculate Whether to recalculate the fare even if already set
     * @return float The calculated fare
     */
    public function calculateFare($recalculate = false)
    {
        // If fare is already calculated and we're not forcing recalculation, return it
        if ($this->price && !$recalculate) {
            return $this->price;
        }
        
        // Only calculate if we have necessary data
        if (!$this->distance_in_km || !$this->vehicle_type) {
            return null;
        }
        
        $rideMatchingService = app(RideMatchingService::class);
        
        // Determine duration (either actual or estimated)
        $durationInMinutes = $this->getDurationInMinutes();
        if (!$durationInMinutes) {
            // Estimate as 2 minutes per km if we don't have actual duration
            $durationInMinutes = ceil($this->distance_in_km * 2);
        }
        
        // Calculate surge multiplier if not already set
        $surgeMultiplier = $this->surge_multiplier ?? 1.0;
        
        // Calculate fare
        $fareInfo = $rideMatchingService->calculateFare(
            $this->vehicle_type,
            $this->distance_in_km,
            $durationInMinutes,
            $surgeMultiplier
        );
        
        return $fareInfo['final_fare'];
    }
    
    /**
     * Get fare breakdown.
     * 
     * @return array The fare breakdown details
     */
    public function getFareBreakdown()
    {
        // Get fare settings for this vehicle type
        $fareSetting = FareSetting::where('vehicle_type', $this->vehicle_type)->first();
        
        if (!$fareSetting) {
            return null;
        }
        
        $waitingFeePerMinute = 0.5; // 0.5 MAD per minute of waiting time
        
        $breakdown = [
            'base_fare' => $this->base_fare ?? $fareSetting->base_fare,
            'distance_fare' => $this->distance_in_km * ($this->per_km_price ?? $fareSetting->per_km_price),
            'time_based_fare' => 0, // Will be calculated if we have actual ride time
            'waiting_fee' => ($this->wait_time_minutes ?? 0) * $waitingFeePerMinute,
            'surge_multiplier' => $this->surge_multiplier ?? 1.0,
        ];
        
        // Calculate time-based fare if we have pickup and dropoff times
        if ($this->pickup_time && $this->dropoff_time) {
            $durationInMinutes = $this->getDurationInMinutes();
            $breakdown['time_based_fare'] = $durationInMinutes * $fareSetting->per_minute_price;
        }
        
        // Calculate subtotal (before surge)
        $breakdown['subtotal'] = $breakdown['base_fare'] + $breakdown['distance_fare'] + 
                                 $breakdown['time_based_fare'] + $breakdown['waiting_fee'];
        
        // Apply surge pricing
        $breakdown['surge_amount'] = $breakdown['surge_multiplier'] > 1.0 ? 
                                    ($breakdown['subtotal'] * ($breakdown['surge_multiplier'] - 1.0)) : 0;
        
        // Calculate total
        $breakdown['total'] = $breakdown['subtotal'] + $breakdown['surge_amount'];
        
        return $breakdown;
    }
    
    /**
     * Check if this is a women-only ride (based on driver settings, not vehicle type)
     * 
     * @return bool Whether this ride is restricted to women-only
     */
    public function isWomenOnlyRide()
    {
        // A ride is women-only if the driver has women_only_driver enabled
        return $this->driver && 
               $this->driver->women_only_driver && 
               $this->driver->user && 
               $this->driver->user->gender === 'female';
    }
    
    /**
     * Get the status of the ride in human readable format
     * 
     * @return string The status text
     */
    public function getStatusText()
    {
        if ($this->ride_status === 'completed') {
            return 'Completed';
        }
        
        if ($this->reservation_status === 'cancelled') {
            return 'Cancelled';
        }
        
        if ($this->reservation_status === 'matching') {
            return 'Finding Driver';
        }
        
        if ($this->reservation_status === 'pending') {
            return 'Pending';
        }
        
        if ($this->reservation_status === 'not_accepted') {
            return 'Not Accepted';
        }
        
        if ($this->reservation_status === 'accepted') {
            if ($this->pickup_time && !$this->dropoff_time) {
                return 'In Progress';
            }
            
            if (!$this->pickup_time) {
                return 'Driver En Route';
            }
        }
        
        return 'Unknown Status';
    }


}