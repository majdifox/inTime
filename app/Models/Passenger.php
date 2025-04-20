<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Passenger extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'rating',
        'total_rides',
        'preferences',
        'ride_preferences',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rating' => 'decimal:2',
        'preferences' => 'array',
        'ride_preferences' => 'array',
    ];

    /**
     * Get the user that owns the passenger profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the rides associated with the passenger.
     */
    public function rides()
    {
        return $this->hasMany(Ride::class);
    }
    
    /**
     * Check if passenger is eligible for women-only rides
     * (can see and be matched with women-only drivers)
     * 
     * @return bool Whether passenger can request women-only rides
     */
    public function canAccessWomenOnlyDrivers()
    {
        return $this->user->gender === 'female';
    }
    
    /**
     * Get passenger's active ride if any
     * 
     * @return Ride|null The active ride or null if none
     */
    public function getActiveRide()
    {
        return $this->rides()
            ->where(function($query) {
                $query->where('reservation_status', 'accepted')
                    ->orWhere('reservation_status', 'matching');
            })
            ->where(function($query) {
                $query->whereNull('dropoff_time')
                    ->orWhere('ride_status', '!=', 'completed');
            })
            ->orderBy('reservation_date', 'desc')
            ->first();
    }
    
    /**
     * Get passenger's favorite locations from preferences
     * 
     * @return array Array of saved locations
     */
    public function getFavoriteLocations()
    {
        return isset($this->preferences['favorite_locations']) 
            ? $this->preferences['favorite_locations'] 
            : [];
    }
    
    /**
     * Add or update a favorite location
     * 
     * @param array $locationData The location data to save
     * @return bool Success status
     */
    // public function saveFavoriteLocation(array $locationData)
    // {
    //     $preferences = $this->preferences ?: [];
        
    //     if (!isset($preferences['favorite_locations'])) {
    //         $preferences['favorite_locations'] = [];
    //     }
        
    //     // Check if location with same type already exists (except 'other')
    //     $locationExists = false;
    //     foreach ($preferences['favorite_locations'] as $key => $location) {
    //         if ($location['type'] === $locationData['type'] && $locationData['type'] !== 'other') {
    //             $preferences['favorite_locations'][$key] = $locationData;
    //             $locationExists = true;
    //             break;
    //         }
    //     }
        
    //     if (!$locationExists) {
    //         $preferences['favorite_locations'][] = $locationData;
    //     }
        
    //     $this->preferences = $preferences;
    //     return $this->save();
    // }
    
    /**
     * Calculate the appropriate fare for a ride based on distance and vehicle type
     * 
     * @param string $vehicleType The type of vehicle
     * @param float $distanceInKm The distance in kilometers
     * @param float $surgeMultiplier Optional surge pricing multiplier
     * @return array The fare details
     */
    public function calculateFare($vehicleType, $distanceInKm, $surgeMultiplier = 1.0)
    {
        // Get fare settings based on vehicle type
        $fareSettings = [
            'basic' => ['base_fare' => 50, 'rate_per_km' => 15],
            'comfort' => ['base_fare' => 80, 'rate_per_km' => 20],
            'wav' => ['base_fare' => 120, 'rate_per_km' => 30],
            'black' => ['base_fare' => 140, 'rate_per_km' => 35],
        ];
        
        // Use default if vehicle type not found
        if (!isset($fareSettings[$vehicleType])) {
            $vehicleType = 'basic';
        }
        
        $baseFare = $fareSettings[$vehicleType]['base_fare'];
        $ratePerKm = $fareSettings[$vehicleType]['rate_per_km'];
        
        // Calculate fare
        $distanceFare = $ratePerKm * $distanceInKm;
        $subtotal = $baseFare + $distanceFare;
        
        // Apply surge pricing
        $totalFare = $subtotal * $surgeMultiplier;
        
        return [
            'vehicle_type' => $vehicleType,
            'base_fare' => $baseFare,
            'rate_per_km' => $ratePerKm,
            'distance_km' => $distanceInKm,
            'distance_fare' => $distanceFare,
            'subtotal' => $subtotal,
            'surge_multiplier' => $surgeMultiplier,
            'total_fare' => $totalFare
        ];
    }
    
    /**
     * Check if this passenger prefers women-only drivers
     * 
     * @return bool Whether passenger prefers women-only drivers
     */
    public function prefersWomenOnlyDrivers()
    {
        return $this->user && $this->user->women_only_rides;
    }

    /**
 * Save a favorite location to passenger preferences
 * 
 * @param array $locationData The location data
 * @return bool Success status
 */
public function saveFavoriteLocation(array $locationData): bool
{
    $preferences = $this->preferences ?? [];
    
    // Initialize favorite_locations array if it doesn't exist
    if (!isset($preferences['favorite_locations'])) {
        $preferences['favorite_locations'] = [];
    }
    
    // Add a unique ID to the location
    $locationData['id'] = uniqid('loc_');
    
    // Add location to favorites
    $preferences['favorite_locations'][] = $locationData;
    
    // Save preferences
    $this->preferences = $preferences;
    
    return $this->save();
}

/**
 * Get a specific saved location by ID
 * 
 * @param string $locationId The location ID
 * @return array|null The location data or null if not found
 */
public function getFavoriteLocation(string $locationId): ?array
{
    if (!$this->preferences || !isset($this->preferences['favorite_locations'])) {
        return null;
    }
    
    foreach ($this->preferences['favorite_locations'] as $location) {
        if (isset($location['id']) && $location['id'] === $locationId) {
            return $location;
        }
    }
    
    return null;
}

/**
 * Get the latest ride for this passenger
 * 
 * @return \App\Models\Ride|null The latest ride or null
 */
public function getLatestRide()
{
    return $this->hasMany(Ride::class)->latest()->first();
}

/**
 * Calculate the average rating for this passenger
 * 
 * @return float The average rating
 */
public function getAverageRating(): float
{
    // Get all reviews for this passenger
    return Review::where('reviewed_id', $this->user_id)->avg('rating') ?? 0;
}

/**
 * Get reviews for this passenger
 * 
 * @param int $limit Maximum number of reviews to return
 * @return \Illuminate\Database\Eloquent\Collection Reviews
 */
public function getReviews(int $limit = 5)
{
    return Review::where('reviewed_id', $this->user_id)
        ->with('reviewer.passenger', 'reviewer.driver', 'ride')
        ->latest()
        ->limit($limit)
        ->get();
}
}

