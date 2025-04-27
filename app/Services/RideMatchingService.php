<?php

namespace App\Services;

use App\Models\Ride;
use App\Models\Driver;
use App\Models\RideRequest;
use App\Models\FareSetting;
use App\Models\DriverLocation;
use App\Models\Vehicle;
use App\Models\User;
use App\Models\Passenger;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RideMatchingService
{
    /**
     * Find eligible and available drivers for a ride
     *
     * @param Ride $ride The ride to match
     * @param int $maxDrivers Maximum number of drivers to find (default 5)
     * @return array Array of potential drivers
     */
    public function findDriversForRide(Ride $ride, int $maxDrivers = 5): array
{
    // Get vehicle type from the ride
    $vehicleType = $ride->vehicle_type ?? 'basic';
    
    // Get passenger details
    $passenger = $ride->passenger;
    $passengerUser = $passenger->user;
    
    // Calculate the distance formula as a string for reuse
    $distanceFormula = "
        (6371 * acos(
            cos(radians({$ride->pickup_latitude})) * 
            cos(radians(driver_locations.latitude)) * 
            cos(radians(driver_locations.longitude) - radians({$ride->pickup_longitude})) + 
            sin(radians({$ride->pickup_latitude})) * 
            sin(radians(driver_locations.latitude))
        ))";
    
    // Start building the query for available drivers
    $query = DB::table('drivers')
        ->join('users', 'drivers.user_id', '=', 'users.id')
        ->join('vehicles', 'drivers.id', '=', 'vehicles.driver_id')
        ->join('driver_locations', 'drivers.id', '=', 'driver_locations.driver_id')
        ->select(
            'drivers.id', 
            'users.id as user_id',
            'users.name', 
            'users.profile_picture',
            'users.gender',
            'drivers.rating',
            'drivers.completed_rides',
            'drivers.women_only_driver',
            'vehicles.type as vehicle_type',
            'vehicles.make',
            'vehicles.model',
            'vehicles.color',
            'vehicles.plate_number',
            'vehicles.id as vehicle_id',
            'driver_locations.latitude',
            'driver_locations.longitude',
            'driver_locations.last_updated',
            DB::raw("$distanceFormula AS distance_km")
        )
        ->where('users.is_online', true)
        ->where('users.account_status', 'activated')
        ->where('drivers.is_verified', true)
        ->where('vehicles.is_active', true)
        // Only select drivers who have updated their location in the last 5 minutes
        ->where('driver_locations.last_updated', '>', now()->subMinutes(5));
        
    // Filter by vehicle type
    $query->where('vehicles.type', $vehicleType);
    
    // Gender filtering for women-only service
    if ($passengerUser->gender === 'female' && $passengerUser->women_only_rides) {
        // Only female drivers with women_only_driver flag can serve women-only passengers
        $query->where('drivers.women_only_driver', true)
              ->where('users.gender', 'female');
    }
    
    // Filter out women-only drivers if they only want female passengers
    // and passenger is not female
    if ($passengerUser->gender !== 'female') {
        $query->where(function($q) {
            $q->where('drivers.women_only_driver', false)
              ->orWhere('users.gender', '!=', 'female');
        });
    }
    
    // ADDED: Filter by vehicle features if passenger has preferences
    if (!empty($passenger->ride_preferences['vehicle_features'])) {
        // For each required feature, join with vehicle_features table and add a where condition
        foreach ($passenger->ride_preferences['vehicle_features'] as $index => $feature) {
            $tableAlias = "vf{$index}";
            
            $query->join("vehicle_features as {$tableAlias}", function($join) use ($tableAlias, $feature) {
                $join->on('vehicles.id', '=', "{$tableAlias}.vehicle_id")
                     ->where("{$tableAlias}.feature", '=', $feature);
            });
        }
    }
    
    // Get drivers within reasonable distance (10km), ordered by proximity
    $query->whereRaw("$distanceFormula < ?", [10])
          ->orderBy('distance_km')
          ->limit($maxDrivers);
    
    $drivers = $query->get();
        
    return $drivers->toArray();
}
    
    /**
     * Calculate the fare for a ride
     * 
     * @param string $vehicleType The type of vehicle
     * @param float $distanceInKm The distance in kilometers
     * @param int $durationInMinutes The estimated duration in minutes
     * @param float $surgeMultiplier Surge pricing multiplier (default 1.0)
     * @return array The calculated fare information
     */
    public function calculateFare(string $vehicleType, float $distanceInKm, int $durationInMinutes, float $surgeMultiplier = 1.0): array
    {
        // Get fare settings from database if available
        $fareSetting = FareSetting::where('vehicle_type', $vehicleType)->first();
        
        if ($fareSetting) {
            $baseFare = $fareSetting->base_fare;
            $perKmPrice = $fareSetting->per_km_price;
            $perMinutePrice = $fareSetting->per_minute_price ?? 0;
            $minimumFare = $fareSetting->minimum_fare ?? 0;
        } else {
            // Define fare settings for each vehicle type as fallback
            $fareSettings = [
                'basic' => ['base_fare' => 50, 'per_km_price' => 15, 'per_minute_price' => 0],
                'comfort' => ['base_fare' => 80, 'per_km_price' => 20, 'per_minute_price' => 0],
                'wav' => ['base_fare' => 120, 'per_km_price' => 30, 'per_minute_price' => 0],
                'black' => ['base_fare' => 140, 'per_km_price' => 35, 'per_minute_price' => 0],
            ];
            
            // Use default if vehicle type not found
            if (!isset($fareSettings[$vehicleType])) {
                $vehicleType = 'basic';
            }
            
            $baseFare = $fareSettings[$vehicleType]['base_fare'];
            $perKmPrice = $fareSettings[$vehicleType]['per_km_price'];
            $perMinutePrice = $fareSettings[$vehicleType]['per_minute_price'];
            $minimumFare = $baseFare; // Use base fare as minimum by default
        }
        
        // Calculate fare components
        $distanceFare = $perKmPrice * $distanceInKm;
        $timeFare = $perMinutePrice * $durationInMinutes;
        $subtotal = $baseFare + $distanceFare + $timeFare;
        
        // Apply surge pricing
        $totalWithSurge = $subtotal * $surgeMultiplier;
        
        // Apply minimum fare if needed
        $finalFare = max($totalWithSurge, $minimumFare);
        
        return [
            'base_fare' => $baseFare,
            'per_km_price' => $perKmPrice,
            'distance_fare' => $distanceFare,
            'time_fare' => $timeFare,
            'subtotal' => $subtotal,
            'surge_multiplier' => $surgeMultiplier,
            'final_fare' => round($finalFare, 2),
            'distance_in_km' => $distanceInKm,
            'duration_in_minutes' => $durationInMinutes,
            'vehicle_type' => $vehicleType,
        ];
    }
    
    /**
     * Initiate the matching process for a ride with a specific driver
     * 
     * @param Ride $ride The ride to match
     * @param int|null $driverId Specific driver ID to match with, or null to find best match
     * @return bool True if the matching process was initiated
     */
    public function initiateMatching(Ride $ride, ?int $driverId = null): bool
{
    // Update ride status to matching
    $ride->reservation_status = 'matching';
    $ride->save();
    
    \Log::info("Initiating matching for ride #{$ride->id} with " . ($driverId ? "specified driver #{$driverId}" : "automatic driver selection"));
    
    if ($driverId) {
        // Check if the specified driver is available and eligible
        $driver = Driver::with(['user', 'vehicle', 'driverLocation'])->find($driverId);
        
        if (!$driver) {
            \Log::warning("Driver #{$driverId} not found when initiating ride #{$ride->id}");
            $ride->reservation_status = 'pending';
            $ride->save();
            return false;
        }
        
        if (!$this->isDriverAvailable($driver)) {
            \Log::warning("Driver #{$driverId} is not available for ride #{$ride->id}");
            $ride->reservation_status = 'pending';
            $ride->save();
            return false;
        }
        
        if (!$this->isDriverVisibleToPassenger($driver, $ride->passenger->user)) {
            \Log::warning("Driver #{$driverId} is not visible to passenger for ride #{$ride->id}");
            $ride->reservation_status = 'pending';
            $ride->save();
            return false;
        }
        
        // Create ride request for the specified driver
        $this->sendRequestToDriver($ride, $driverId);
        \Log::info("Ride request sent to specified driver #{$driverId} for ride #{$ride->id}");
        return true;
    } else {
        // Find potential drivers
        $potentialDrivers = $this->findDriversForRide($ride);
        
        if (empty($potentialDrivers)) {
            \Log::warning("No drivers found for ride #{$ride->id}. Checking individual requirements...");
            
            // Debug why no drivers were found by checking each criterion separately
            $onlineDrivers = Driver::whereHas('user', function($q) {
                $q->where('is_online', true);
            })->count();
            
            $verifiedDrivers = Driver::where('is_verified', true)->count();
            
            $recentLocationDrivers = Driver::whereHas('driverLocation', function($q) {
                $q->where('last_updated', '>', now()->subMinutes(5));
            })->count();
            
            $matchingVehicleDrivers = Driver::whereHas('vehicle', function($q) use ($ride) {
                $q->where('type', $ride->vehicle_type ?? 'share')
                  ->where('is_active', true);
            })->count();
            
            $availableDrivers = Driver::whereDoesntHave('rides', function($q) {
                $q->where('ride_status', 'ongoing')
                  ->whereNull('dropoff_time');
            })->count();
            
            \Log::info("Driver availability breakdown for ride #{$ride->id}: " . 
                       "Online: {$onlineDrivers}, " . 
                       "Verified: {$verifiedDrivers}, " . 
                       "Recent location: {$recentLocationDrivers}, " . 
                       "Matching vehicle: {$matchingVehicleDrivers}, " . 
                       "Not on active ride: {$availableDrivers}");
            
            // No drivers available, set ride status back to pending
            $ride->reservation_status = 'pending';
            $ride->save();
            return false;
        }
        
        // Log the number of potential drivers found
        \Log::info("Found " . count($potentialDrivers) . " potential drivers for ride #{$ride->id}");
        
        // Create ride request for the first driver (best match)
        $this->sendRequestToDriver($ride, $potentialDrivers[0]->id);
        \Log::info("Ride request sent to driver #{$potentialDrivers[0]->id} for ride #{$ride->id}");
        return true;
    }
}
    
    /**
     * Check if driver is available
     *
     * @param Driver $driver The driver to check
     * @return bool True if driver is available
     */
    private function isDriverAvailable(Driver $driver): bool
{
    $reasons = [];
    
    // Load relationships if not already loaded
    if (!$driver->relationLoaded('user')) {
        $driver->load('user');
    }
    
    if (!$driver->relationLoaded('vehicle')) {
        $driver->load('vehicle');
    }
    
    if (!$driver->relationLoaded('driverLocation')) {
        $driver->load('driverLocation');
    }
    
    // Check if driver is online and account is activated
    if (!$driver->user || !$driver->user->is_online) {
        $reasons[] = 'Driver is offline';
    }
    
    if ($driver->user && $driver->user->account_status !== 'activated') {
        $reasons[] = "Account status: {$driver->user->account_status}";
    }
    
    // Check if driver is verified
    if (!$driver->is_verified) {
        $reasons[] = 'Driver is not verified';
    }
    
    // Check if driver has an active vehicle
    if (!$driver->vehicle || !$driver->vehicle->is_active) {
        $reasons[] = 'No active vehicle';
    }
    
    // Check if driver has updated their location recently
    if (!$driver->driverLocation || $driver->driverLocation->last_updated->lt(now()->subMinutes(5))) {
        $reasons[] = 'Location not updated in last 5 minutes';
    }
    
    // Check if driver is currently on an active ride
    $activeRide = Ride::where('driver_id', $driver->id)
        ->where('ride_status', 'ongoing')
        ->whereNull('dropoff_time')
        ->exists();
        
    if ($activeRide) {
        $reasons[] = 'Currently on an active ride';
    }
    
    // If any reasons were found, log them and return false
    if (!empty($reasons)) {
        \Log::info("Driver #{$driver->id} is unavailable: " . implode(', ', $reasons));
        return false;
    }
    
    return true;
}
    
    /**
     * Check if driver is visible to passenger based on gender preferences
     *
     * @param Driver $driver The driver to check
     * @param User $passengerUser The passenger user
     * @return bool True if driver is visible to passenger
     */
    private function isDriverVisibleToPassenger(Driver $driver, User $passengerUser): bool
    {
        // Load relationships if not already loaded
        if (!$driver->relationLoaded('user')) {
            $driver->load('user');
        }
        
        // Check women-only driver restrictions
        if ($driver->women_only_driver && $driver->user->gender === 'female' && $passengerUser->gender !== 'female') {
            return false;
        }
        
        // Check passenger women-only preference
        if ($passengerUser->women_only_rides && ($driver->user->gender !== 'female' || !$driver->women_only_driver)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Send a ride request to a specific driver
     * 
     * @param Ride $ride The ride to be requested
     * @param int $driverId The ID of the driver to send the request to
     * @return RideRequest The created ride request
     */
    public function sendRequestToDriver(Ride $ride, int $driverId): RideRequest
    {
        // Create a new ride request
        $rideRequest = new RideRequest();
        $rideRequest->ride_id = $ride->id;
        $rideRequest->driver_id = $driverId;
        $rideRequest->status = 'pending';
        $rideRequest->requested_at = now();
        $rideRequest->save();
        
        // Here you would typically send a notification to the driver
        // For example, using a notification system or push notifications
        
        return $rideRequest;
    }
    
    /**
     * Handle a driver's response to a ride request (accept/reject)
     * 
     * @param RideRequest $rideRequest The ride request being responded to
     * @param string $response 'accept' or 'reject'
     * @return bool True if the ride was successfully matched
     */
    public function handleDriverResponse(RideRequest $rideRequest, string $response): bool
    {
        $rideRequest->responded_at = now();
        
        // Allow acceptance of expired requests within a grace period
        if ($rideRequest->status === 'expired') {
            $gracePeriodSeconds = 30; // 30 second grace period
            $expiredRecently = $rideRequest->responded_at && 
                $rideRequest->responded_at->diffInSeconds(now()) <= $gracePeriodSeconds;
                
            if ($expiredRecently) {
                // Reset status to pending temporarily so we can accept it
                $rideRequest->status = 'pending';
                \Log::info("Resetting expired request #{$rideRequest->id} to pending within grace period");
            }
        }
        
        if ($response === 'accept') {
            $rideRequest->status = 'accepted';
            $rideRequest->save();
            
            // Update the ride with the accepted driver
            $ride = $rideRequest->ride;
            $ride->driver_id = $rideRequest->driver_id;
            $ride->reservation_status = 'accepted';
            $ride->save();
            
            // Calculate distance and update the ride record
            $driver = Driver::with('driverLocation')->find($rideRequest->driver_id);
            if ($driver && $driver->driverLocation) {
                $eta = $this->calculateETA(
                    $driver->driverLocation->latitude,
                    $driver->driverLocation->longitude,
                    $ride->pickup_latitude,
                    $ride->pickup_longitude
                );
                
                // Update ride with ETA information
                $ride->wait_time_minutes = $eta;
                $ride->save();
            }
            
            return true;
        } else {
            // Driver rejected the request
            $rideRequest->status = 'rejected';
            $rideRequest->save();
            
            // Check if driver should be temporarily suspended due to too many rejections
            $driver = Driver::find($rideRequest->driver_id);
            if ($driver) {
                $recentRejections = RideRequest::where('driver_id', $driver->id)
                    ->where('status', 'rejected')
                    ->where('responded_at', '>', now()->subHours(1))
                    ->count();
                    
                if ($recentRejections >= 5) { // More than 5 rejections in the last hour
                    $driver->user->account_status = 'suspended';
                    $driver->user->save();
                    
                    // You would typically notify the driver here
                }
            }
            
            // Find the next available driver
            $ride = $rideRequest->ride;
            $potentialDrivers = $this->findDriversForRide($ride);
            
            // Filter out the drivers who have already rejected this ride
            $rejectedDriverIds = RideRequest::where('ride_id', $ride->id)
                ->where('status', 'rejected')
                ->pluck('driver_id')
                ->toArray();
                
            $availableDrivers = array_filter($potentialDrivers, function($driver) use ($rejectedDriverIds) {
                return !in_array($driver->id, $rejectedDriverIds);
            });
            
            if (!empty($availableDrivers)) {
                // Send request to the next driver
                $nextDriver = reset($availableDrivers);
                $this->sendRequestToDriver($ride, $nextDriver->id);
                return false; // Not yet matched, but processing continues
            } else {
                // No more available drivers, set ride status to not accepted
                $ride->reservation_status = 'not_accepted';
                $ride->save();
                return false;
            }
        }
    }
    
    /**
     * Calculate the estimated time of arrival 
     * 
     * @param float $driverLat Driver's latitude
     * @param float $driverLon Driver's longitude
     * @param float $pickupLat Pickup latitude
     * @param float $pickupLon Pickup longitude
     * @param float $averageSpeed Average driving speed in km/h (default 30)
     * @return int Estimated time in minutes
     */
    public function calculateETA(float $driverLat, float $driverLon, float $pickupLat, float $pickupLon, float $averageSpeed = 30): int
    {
        // Calculate distance in kilometers
        $distance = $this->calculateDistance($driverLat, $driverLon, $pickupLat, $pickupLon);
        
        // Calculate time in minutes based on average speed
        $timeInMinutes = ($distance / $averageSpeed) * 60;
        
        // Add a buffer for traffic and pickup time (20%)
        $timeWithBuffer = $timeInMinutes * 1.2;
        
        return max(1, (int) ceil($timeWithBuffer));
    }
    
    /**
     * Calculate surge multiplier for a location
     * 
     * @param float $latitude Pickup latitude
     * @param float $longitude Pickup longitude
     * @param float $radius Radius to check in kilometers
     * @return float Surge multiplier
     */
    public function calculateSurgeMultiplier(float $latitude, float $longitude, float $radius = 5.0): float
    {
        // Calculate distance formula as a string for reuse
        $distanceFormula = "(6371 * acos(
            cos(radians($latitude)) * 
            cos(radians(pickup_latitude)) * 
            cos(radians(pickup_longitude) - radians($longitude)) + 
            sin(radians($latitude)) * 
            sin(radians(pickup_latitude))
        ))";
        
        // Count active rides in the area using whereRaw instead of havingRaw
        $activeRidesCount = Ride::whereRaw("$distanceFormula <= ?", [$radius])
            ->where(function($query) {
                $query->where('reservation_status', 'matching')
                    ->orWhere(function($q) {
                        $q->where('reservation_status', 'accepted')
                          ->where('ride_status', 'ongoing')
                          ->whereNull('dropoff_time');
                    });
            })
            ->count();
        
        // Calculate distance formula for driver locations
        $driverDistanceFormula = "(6371 * acos(
            cos(radians($latitude)) * 
            cos(radians(driver_locations.latitude)) * 
            cos(radians(driver_locations.longitude) - radians($longitude)) + 
            sin(radians($latitude)) * 
            sin(radians(driver_locations.latitude))
        ))";
        
        // Count available drivers in the area
        $availableDriversCount = DriverLocation::join('drivers', 'driver_locations.driver_id', '=', 'drivers.id')
            ->join('users', 'drivers.user_id', '=', 'users.id')
            ->whereRaw("$driverDistanceFormula <= ?", [$radius])
            ->where('users.is_online', true)
            ->where('users.account_status', 'activated')
            ->where('driver_locations.last_updated', '>', now()->subMinutes(5))
            ->count();
        
        // Calculate demand ratio (with protection against division by zero)
        $demandRatio = $availableDriversCount > 0 ? $activeRidesCount / $availableDriversCount : 1;
        
        // Calculate surge multiplier based on demand ratio
        if ($demandRatio <= 0.5) {
            return 1.0; // Low demand, no surge
        } elseif ($demandRatio <= 0.8) {
            return 1.2; // Moderate demand
        } elseif ($demandRatio <= 1.2) {
            return 1.5; // High demand
        } elseif ($demandRatio <= 1.8) {
            return 1.8; // Very high demand
        } else {
            return 2.0; // Extreme demand
        }
    }

    /**
     * Calculate the distance between two points using the Haversine formula
     * 
     * @param float $lat1 First latitude
     * @param float $lon1 First longitude
     * @param float $lat2 Second latitude
     * @param float $lon2 Second longitude
     * @return float Distance in kilometers
     */
    public function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        // Earth's radius in kilometers
        $earthRadius = 6371;
        
        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);
        
        $a = sin($latDelta/2) * sin($latDelta/2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta/2) * sin($lonDelta/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        
        return $earthRadius * $c;
    }
    
    /**
     * Get available drivers for passenger selection
     * 
     * @param float $pickupLat Pickup latitude
     * @param float $pickupLng Pickup longitude
     * @param string $vehicleType Requested vehicle type
     * @param User $passenger The passenger user
     * @return array List of available drivers
     */
    public function getAvailableDriversForSelection(float $pickupLat, float $pickupLng, string $vehicleType, User $passenger): array
    {
        // Check for fully available drivers (online, location shared, not on a ride)
        $query = Driver::with(['user', 'vehicle', 'driverLocation'])
            ->whereHas('user', function($q) {
                $q->where('is_online', true)
                  ->where('account_status', 'activated');
            })
            ->where('is_verified', true)
            ->whereHas('driverLocation', function($q) {
                $q->where('last_updated', '>', now()->subMinutes(10));
            })
            ->whereHas('vehicle', function($q) use ($vehicleType) {
                $q->where('type', $vehicleType)
                  ->where('is_active', true);
            });
        
        // UPDATED LOGIC:
        if ($passenger->gender === 'female') {
            if ($passenger->women_only_rides) {
                // Female passenger with women_only_rides wants only female drivers with women_only_driver
                $query->whereHas('user', function($q) {
                    $q->where('gender', 'female');
                });
            } else {
                // Female passenger without women_only_rides can see all drivers PLUS female drivers that have women_only_driver
                // This means no filtering needed for female passengers without the preference
            }
        } else {
            // Non-female passengers can't see female drivers with women_only_driver enabled
            $query->where(function($q) {
                $q->where('women_only_driver', false)
                  ->orWhereHas('user', function($sq) {
                      $sq->where('gender', '!=', 'female');
                  });
            });
        }
        
        // Get drivers
        $drivers = $query->get();
        
        // Debug logging
        \Log::info("Found " . $drivers->count() . " drivers for passenger {$passenger->id} with vehicle type {$vehicleType}, gender={$passenger->gender}, women_only_rides=".($passenger->women_only_rides?'true':'false'));
        
        // Calculate distance and ETA for each driver
        $result = [];
        foreach ($drivers as $driver) {
            if (!$driver->driverLocation) {
                \Log::warning("Driver #{$driver->id} has no location data");
                continue;
            }
            
            $distance = $this->calculateDistance(
                $pickupLat, 
                $pickupLng, 
                $driver->driverLocation->latitude, 
                $driver->driverLocation->longitude
            );
            
            $eta = $this->calculateETA(
                $driver->driverLocation->latitude, 
                $driver->driverLocation->longitude,
                $pickupLat,
                $pickupLng
            );
            
            if ($distance <= 30) {
                $result[] = [
                    'id' => $driver->id,
                    'name' => $driver->user->name,
                    'gender' => $driver->user->gender,
                    'profile_picture' => $driver->user->profile_picture,
                    'women_only_driver' => $driver->women_only_driver,
                    'rating' => $driver->rating ?? 5.0,
                    'completed_rides' => $driver->completed_rides ?? 0,
                    'vehicle' => [
                        'make' => $driver->vehicle->make,
                        'model' => $driver->vehicle->model,
                        'color' => $driver->vehicle->color,
                        'plate_number' => $driver->vehicle->plate_number,
                        'type' => $driver->vehicle->type
                    ],
                    'location' => [
                        'latitude' => $driver->driverLocation->latitude,
                        'longitude' => $driver->driverLocation->longitude
                    ],
                    'distance_km' => round($distance, 2),
                    'eta_minutes' => $eta
                ];
            }
        }
        
        // Sort by distance (closest first)
        usort($result, function($a, $b) {
            return $a['distance_km'] <=> $b['distance_km'];
        });
        
        return $result;
    }
}