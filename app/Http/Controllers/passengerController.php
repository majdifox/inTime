<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\Ride;
use App\Models\DriverLocation;
use App\Models\User;
use App\Models\Passenger;
use App\Models\FareSetting;
use App\Models\RideRequest;
use App\Services\RideMatchingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class PassengerController extends Controller
{
    protected $rideMatchingService;
    
    public function __construct(RideMatchingService $rideMatchingService)
    {
        $this->rideMatchingService = $rideMatchingService;
    }

    /**
     * Display passenger dashboard with upcoming rides
     */
    public function index()
    {
        // Find or create passenger record
        $passenger = Passenger::firstOrCreate(
            ['user_id' => Auth::id()],
            [
                'rating' => 5.0,
                'total_rides' => 0,
                'preferences' => null,
                'ride_preferences' => null
            ]
        );

        // Get upcoming rides
        $upcomingRides = Ride::with(['driver.user', 'driver.vehicle'])
            ->where('passenger_id', $passenger->id)
            ->where(function($query) {
                $query->where('reservation_status', 'accepted')
                    ->orWhere('reservation_status', 'pending')
                    ->orWhere('reservation_status', 'matching');
            })
            ->where(function($query) {
                $query->whereNull('dropoff_time')
                    ->orWhere('ride_status', '!=', 'completed');
            })
            ->orderBy('reservation_date', 'asc')
            ->get();
            
        // Get active ride
        $activeRide = Ride::with(['driver.user', 'driver.vehicle', 'driver.driverLocation'])
            ->where('passenger_id', $passenger->id)
            ->where('reservation_status', 'accepted')
            ->where('ride_status', 'ongoing')
            ->whereNull('dropoff_time')
            ->first();
            
        // Get ride history
        $rideHistory = Ride::with(['driver.user', 'driver.vehicle'])
            ->where('passenger_id', $passenger->id)
            ->where('ride_status', 'completed')
            ->orderBy('dropoff_time', 'desc')
            ->limit(5)
            ->get();
            
        return view('passenger.dashboard', compact('upcomingRides', 'rideHistory', 'passenger', 'activeRide'));
    }

    /**
     * Show the active ride page
     */
    public function activeRide()
    {
        $passenger = Passenger::where('user_id', Auth::id())->first();
        
        // Get active ride
        $activeRide = Ride::with(['driver.user', 'driver.vehicle', 'driver.driverLocation'])
            ->where('passenger_id', $passenger->id)
            ->where('reservation_status', 'accepted')
            ->where('ride_status', 'ongoing')
            ->whereNull('dropoff_time')
            ->first();
            
        if (!$activeRide) {
            return redirect()->route('passenger.dashboard')->with('error', 'No active ride found.');
        }
        
        return view('passenger.activeRide', compact('activeRide', 'passenger'));
    }
    
    /**
     * Show the book ride page
     */
    public function bookRide()
    {
        // Check if user is suspended from requesting rides
        if (Auth::user()->isRideSuspended()) {
            $suspensionEnd = Auth::user()->ride_suspension_until->diffForHumans();
            return redirect()->route('passenger.dashboard')->with('error', "You are temporarily suspended from requesting rides. Suspension ends $suspensionEnd");
        }
        
        // Get vehicle types for selection
        $vehicleTypes = Vehicle::select('type')->distinct()->get()->pluck('type');
        
        // Get all fare options
        $fareOptions = FareSetting::all();
        
        // Get or create passenger record
        $passenger = Passenger::firstOrCreate(
            ['user_id' => Auth::id()],
            [
                'rating' => 5.0,
                'total_rides' => 0,
                'preferences' => null,
                'ride_preferences' => null
            ]
        );
        
        // Get saved locations from passenger preferences
        $savedLocations = [];
        if ($passenger->preferences && isset($passenger->preferences['favorite_locations'])) {
            $savedLocations = $passenger->preferences['favorite_locations'];
        }
        
        return view('passenger.bookRide', compact('vehicleTypes', 'fareOptions', 'savedLocations'));
    }
    
    /**
     * Calculate ride options based on location
     */
    public function calculateRideOptions(Request $request)
    {
        $validated = $request->validate([
            'pickup_location' => 'required|string|max:255',
            'pickup_latitude' => 'required|numeric|between:-90,90',
            'pickup_longitude' => 'required|numeric|between:-180,180',
            'dropoff_location' => 'required|string|max:255',
            'dropoff_latitude' => 'required|numeric|between:-90,90',
            'dropoff_longitude' => 'required|numeric|between:-180,180',
        ]);
        
        // Calculate distance in kilometers
        $distanceInKm = $this->calculateDistance(
            $validated['pickup_latitude'], 
            $validated['pickup_longitude'], 
            $validated['dropoff_latitude'], 
            $validated['dropoff_longitude']
        );
        
        // Estimate duration in minutes (using simple calculation, ~2 min per km)
        $durationInMinutes = ceil($distanceInKm * 2);
        
        // Calculate potential surge pricing
        $surgeMultiplier = $this->calculateSurgePricing(
            $validated['pickup_latitude'],
            $validated['pickup_longitude']
        );
        
        // Get user for gender check
        $user = Auth::user();
        
        // Calculate fares for each vehicle type
        $vehicleTypes = ['basic', 'comfort', 'black', 'wav'];

        $rideOptions = [];
        
        foreach ($vehicleTypes as $vehicleType) {
            // Skip women-only rides for male passengers
            if ($vehicleType === 'women' && $user->gender !== 'female') {
                continue;
            }
            
            // Calculate fare for this vehicle type
            $fare = $this->calculateFare($vehicleType, $distanceInKm, $surgeMultiplier);
            
            // Calculate estimated wait time based on available drivers
            $waitTime = $this->estimateWaitTime(
                $validated['pickup_latitude'],
                $validated['pickup_longitude'],
                $vehicleType
            );
            
            $rideOptions[$vehicleType] = [
                'vehicle_type' => $vehicleType,
                'display_name' => ucfirst($vehicleType),
                'base_fare' => $fare['base_fare'],
                'distance_fare' => $fare['distance_fare'],
                'total_fare' => $fare['total_fare'],
                'distance_km' => round($distanceInKm, 2),
                'duration_minutes' => $durationInMinutes,
                'wait_time_minutes' => $waitTime,
                'surge_multiplier' => $surgeMultiplier,
                'is_surge_active' => $surgeMultiplier > 1,
            ];
        }
        
        // Store search params in session for booking
        session([
            'ride_search' => [
                'pickup_location' => $validated['pickup_location'],
                'pickup_latitude' => $validated['pickup_latitude'],
                'pickup_longitude' => $validated['pickup_longitude'],
                'dropoff_location' => $validated['dropoff_location'],
                'dropoff_latitude' => $validated['dropoff_latitude'],
                'dropoff_longitude' => $validated['dropoff_longitude'],
                'distance_km' => $distanceInKm,
                'duration_minutes' => $durationInMinutes,
                'surge_multiplier' => $surgeMultiplier,
                'reservation_date' => now()
            ]
        ]);
        
        return response()->json([
            'ride_options' => $rideOptions,
            'distance' => [
                'km' => round($distanceInKm, 2),
                'miles' => round($distanceInKm / 1.60934, 2)
            ],
            'duration' => [
                'minutes' => $durationInMinutes,
                'text' => $this->formatDuration($durationInMinutes)
            ]
        ]);
    }
    
    /**
     * Get available drivers for a ride
     */
    public function getAvailableDrivers(Request $request)
    {
        $request->validate([
            'vehicle_type' => 'required|string|in:basic,comfort,black,wav',
            'pickup_latitude' => 'required|numeric',
            'pickup_longitude' => 'required|numeric',
        ]);
        
        $vehicleType = $request->input('vehicle_type');
        $pickupLat = $request->input('pickup_latitude');
        $pickupLng = $request->input('pickup_longitude');
        
        // Ensure ride search is stored in session
        $rideSearch = session('ride_search');
        if (!$rideSearch) {
            return response()->json([
                'success' => false,
                'message' => 'Ride search information missing. Please try again.'
            ], 400);
        }
        
        // Get available drivers for this location and vehicle type
        $availableDrivers = $this->rideMatchingService->getAvailableDriversForSelection(
            $pickupLat,
            $pickupLng,
            $vehicleType,
            Auth::user()
        );
        
        return response()->json([
            'success' => true,
            'drivers' => $availableDrivers
        ]);
    }

    public function selectDriver(Request $request)
    {
        // Get vehicle type and coordinates from query parameters with a default value
        $vehicleType = $request->query('vehicle_type', 'basic');
        $pickupLat = $request->query('pickup_latitude');
        $pickupLng = $request->query('pickup_longitude');
        
        // If no pickup coordinates are provided, get from session
        if (!$pickupLat || !$pickupLng) {
            $rideSearch = session('ride_search');
            if ($rideSearch) {
                $pickupLat = $rideSearch['pickup_latitude'] ?? null;
                $pickupLng = $rideSearch['pickup_longitude'] ?? null;
            }
        }
        
        // Validate coordinates exist
        if (!$pickupLat || !$pickupLng) {
            return redirect()->route('passenger.book')->with('error', 'Pickup location information is missing. Please try again.');
        }
        
        // Get ride info from session
        $rideInfo = session('ride_search');
        if (!$rideInfo) {
            return redirect()->route('passenger.book')->with('error', 'Ride information is missing. Please try again.');
        }
        
        // IMPORTANT FIX: Ensure required keys exist in $rideInfo
        if (!isset($rideInfo['vehicle_type'])) {
            $rideInfo['vehicle_type'] = $vehicleType;
        }
        
        // Calculate price if it doesn't exist
        if (!isset($rideInfo['price'])) {
            // Calculate using the same method as in calculateRideOptions
            $fare = $this->calculateFare(
                $rideInfo['vehicle_type'] ?? $vehicleType,
                $rideInfo['distance_km'] ?? 0,
                $rideInfo['surge_multiplier'] ?? 1.0
            );
            
            $rideInfo['price'] = $fare['total_fare'];
        }
        
        // Get available drivers for the selected vehicle type
        $drivers = $this->rideMatchingService->getAvailableDriversForSelection(
            $pickupLat,
            $pickupLng,
            $vehicleType,
            Auth::user()
        );
        
        // Sort drivers by distance, then by rating (highest first), then by completed rides (highest first)
        usort($drivers, function($a, $b) {
            // First sort by distance (closest first)
            if ($a['distance_km'] != $b['distance_km']) {
                return $a['distance_km'] <=> $b['distance_km'];
            }
            
            // Then by rating (highest first)
            if ($a['rating'] != $b['rating']) {
                return $b['rating'] <=> $a['rating'];
            }
            
            // Finally by completed rides (highest first)
            return $b['completed_rides'] <=> $a['completed_rides'];
        });
        
        return view('passenger.selectDriver', [
            'drivers' => $drivers,
            'rideInfo' => $rideInfo,
            'vehicle_type' => $vehicleType  // Make sure to pass this variable to the view
        ]);
    }
    
    /**
     * Request a ride with selected vehicle type and driver
     */
    public function requestRide(Request $request)
{
    $validated = $request->validate([
        'vehicle_type' => 'required|string|in:basic,comfort,black,wav',
        'driver_id' => 'nullable|integer|exists:drivers,id',
        'ride_preferences' => 'nullable|array'
    ]);
    
    // Check if user is suspended from requesting rides
    if (Auth::user()->isRideSuspended()) {
        return redirect()->route('passenger.dashboard')->with('error', 'You are temporarily suspended from requesting rides.');
    }
    
    // Get ride search params from session
    $rideSearch = session('ride_search');
    if (!$rideSearch) {
        return redirect()->route('passenger.book')->with('error', 'Ride search information missing. Please try again.');
    }
    
    $passenger = Passenger::where('user_id', Auth::id())->first();
    $user = Auth::user();
    
    
    // Calculate fare
    $fare = $this->calculateFare(
        $validated['vehicle_type'],
        $rideSearch['distance_km'],
        $rideSearch['surge_multiplier']
    );
    
    // Create new ride
    $ride = new Ride();
    $ride->passenger_id = $passenger->id;
    $ride->reservation_date = Carbon::parse($rideSearch['reservation_date']);
    $ride->reservation_status = 'matching'; // Start matching immediately
    $ride->pickup_location = $rideSearch['pickup_location'];
    $ride->pickup_latitude = $rideSearch['pickup_latitude'];
    $ride->pickup_longitude = $rideSearch['pickup_longitude'];
    $ride->dropoff_location = $rideSearch['dropoff_location'];
    $ride->dropoff_latitude = $rideSearch['dropoff_latitude'];
    $ride->dropoff_longitude = $rideSearch['dropoff_longitude'];
    $ride->ride_status = 'ongoing';
    $ride->vehicle_type = $validated['vehicle_type'];
    $ride->distance_in_km = $rideSearch['distance_km'];
    $ride->base_fare = $fare['base_fare'];
    $ride->per_km_price = $fare['rate_per_km'];
    $ride->price = $fare['total_fare'];
    $ride->ride_cost = $fare['total_fare']; // For backward compatibility
    $ride->surge_multiplier = $rideSearch['surge_multiplier'];
    $ride->wait_time_minutes = 0;
    $ride->is_reviewed = false;
    $ride->save();
    
    // Save passenger preferences if provided
    if (!empty($validated['ride_preferences'])) {
        $preferences = $passenger->ride_preferences ?: [];
        $preferences = array_merge($preferences, $validated['ride_preferences']);
        $passenger->ride_preferences = $preferences;
        $passenger->save();
        
        // Also update user women_only_rides preference if present
        if (isset($validated['ride_preferences']['women_only_rides'])) {
            $user->women_only_rides = $validated['ride_preferences']['women_only_rides'];
            $user->save();
        }
    }
    
    // Start the matching process
    $matchingInitiated = false;
    if (isset($validated['driver_id'])) {
        // Manual driver selection - send request only to the selected driver
        $matchingInitiated = $this->rideMatchingService->initiateMatching($ride, $validated['driver_id']);
    } else {
        // Automatic driver matching
        $matchingInitiated = $this->rideMatchingService->initiateMatching($ride);
    }
    
    // Clear search session
    session()->forget('ride_search');
    
    if ($matchingInitiated) {
        return redirect()->route('passenger.ride.matching', ['ride' => $ride->id]);
    } else {
        return redirect()->route('passenger.dashboard')->with('error', 'No drivers available at the moment. Please try again later.');
    }
}
    
    /**
     * Show ride matching status
     */
    public function rideMatching(Ride $ride)
    {
        // Security check
        $passenger = Passenger::where('user_id', Auth::id())->first();
        if ($ride->passenger_id !== $passenger->id) {
            return redirect()->route('passenger.dashboard')->with('error', 'Unauthorized access.');
        }
        
        // Check if ride is still in matching state
        if ($ride->reservation_status === 'accepted') {
            return redirect()->route('passenger.active.ride');
        } else if ($ride->reservation_status !== 'matching') {
            return redirect()->route('passenger.dashboard')->with('error', 'This ride is no longer matching.');
        }
        
        // Load ride with relationship data
        $ride->load(['passenger.user']);
        
        return view('passenger.rideMatching', compact('ride'));
    }
    
    /**
     * Check ride matching status via AJAX
     */
    public function checkMatchingStatus(Ride $ride)
    {
        // Security check
        $passenger = Passenger::where('user_id', Auth::id())->first();
        if ($ride->passenger_id !== $passenger->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Refresh ride data
        $ride->refresh();
        
        if ($ride->reservation_status === 'accepted') {
            // Ride was accepted, return driver info
            $ride->load(['driver.user', 'driver.vehicle']);
            
            // Calculate ETA
            $eta = null;
            if ($ride->driver->driverLocation) {
                $eta = $this->calculateETA(
                    $ride->driver->driverLocation->latitude,
                    $ride->driver->driverLocation->longitude,
                    $ride->pickup_latitude,
                    $ride->pickup_longitude
                );
            }
            
            return response()->json([
                'status' => 'matched',
                'driver' => [
                    'name' => $ride->driver->user->name,
                    'profile_picture' => $ride->driver->user->profile_picture,
                    'gender' => $ride->driver->user->gender,
                    'rating' => $ride->driver->rating,
                    'completed_rides' => $ride->driver->completed_rides,
                    'women_only_driver' => $ride->driver->women_only_driver,
                    'vehicle' => [
                        'make' => $ride->driver->vehicle->make,
                        'model' => $ride->driver->vehicle->model,
                        'color' => $ride->driver->vehicle->color,
                        'plate_number' => $ride->driver->vehicle->plate_number,
                        'type' => $ride->driver->vehicle->type
                    ],
                    'eta_minutes' => $eta,
                    'eta_text' => $eta ? $this->formatDuration($eta) : 'Unknown'
                ],
                'redirect' => route('passenger.active.ride')
            ]);
        } else if ($ride->reservation_status === 'matching') {
            // Still matching, provide status update
            $rideRequests = $ride->rideRequests()->orderBy('created_at', 'desc')->get();
            $pendingCount = $rideRequests->where('status', 'pending')->count();
            $rejectedCount = $rideRequests->where('status', 'rejected')->count();
            
            return response()->json([
                'status' => 'matching',
                'matching_time' => $ride->updated_at->diffForHumans(),
                'requests_sent' => $rideRequests->count(),
                'pending_requests' => $pendingCount,
                'rejected_requests' => $rejectedCount,
                'message' => 'Looking for a driver near you...'
            ]);
        } else {
            // Matching failed or was cancelled
            return response()->json([
                'status' => 'failed',
                'reservation_status' => $ride->reservation_status,
                'message' => 'Unable to find a driver at this time.',
                'redirect' => route('passenger.dashboard')
            ]);
        }
    }
    
    /**
     * Cancel a ride
     */
    public function cancelRide(Request $request, $rideId)
    {
        $ride = Ride::findOrFail($rideId);
        $passenger = Passenger::where('user_id', Auth::id())->first();
        
        // Check if ride belongs to passenger
        if ($ride->passenger_id !== $passenger->id) {
            return back()->with('error', 'You are not authorized to cancel this ride.');
        }
        
        // Check if ride can be cancelled (not started yet)
        if ($ride->pickup_time) {
            return back()->with('error', 'Cannot cancel a ride that has already started.');
        }
        
        // Get the timestamp before cancellation for comparison
        $wasDriverAssigned = $ride->driver_id !== null;
        $rideCreatedAt = $ride->created_at;
        
        // Cancel the ride
        $ride->reservation_status = 'cancelled';
        $ride->save();
        
        // Check if passenger should face penalties
        if ($wasDriverAssigned && Carbon::now()->diffInMinutes($rideCreatedAt) > 2) {
            // If driver was already assigned and it's not within seconds of the request
            // We may want to add a cancellation fee here
        }
        
        // Check if passenger has multiple recent cancellations
        if (Auth::user()->shouldBeSuspendedForCancellations()) {
            // Suspend for 6 hours
            Auth::user()->suspendRidesFor(6);
            
            return back()->with('warning', 'You have cancelled too many rides recently. Your ability to request rides has been temporarily suspended for 6 hours.');
        }
        
        return back()->with('success', 'Ride cancelled successfully.');
    }
    
    /**
     * Show ride history
     */
    public function rideHistory()
    {
        $passenger = Passenger::where('user_id', Auth::id())->first();
        
        $rides = Ride::with(['driver.user', 'driver.vehicle'])
            ->where('passenger_id', $passenger->id)
            ->where('ride_status', 'completed')
            ->orderBy('dropoff_time', 'desc')
            ->paginate(10);
            
        return view('passenger.rideHistory', compact('rides'));
    }

    /**
 * Rate a completed ride
 * 
 * @param Request $request The request data
 * @param Ride $ride The ride to rate
 * @return \Illuminate\Http\RedirectResponse
 */
public function rateRide(Request $request, Ride $ride)
{
    // Validate the incoming request
    $validated = $request->validate([
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'nullable|string|max:500',
    ]);
    
    // Security check - ensure the ride belongs to this passenger
    $passenger = Passenger::where('user_id', Auth::id())->first();
    if ($ride->passenger_id !== $passenger->id) {
        return back()->with('error', 'You are not authorized to rate this ride.');
    }
    
    // Check if ride is completed
    if ($ride->ride_status !== 'completed') {
        return back()->with('error', 'Only completed rides can be rated.');
    }
    
    // Check if ride is already reviewed
    if ($ride->is_reviewed) {
        return back()->with('error', 'This ride has already been reviewed.');
    }
    
    // Create the review
    $review = new Review();
    $review->ride_id = $ride->id;
    $review->reviewer_id = $passenger->user_id;
    $review->reviewed_id = $ride->driver->user_id;
    $review->rating = $validated['rating'];
    $review->comment = $validated['comment'] ?? null;
    $review->save();
    
    // Update the driver's rating
    $driver = $ride->driver;
    $allRatings = Review::where('reviewed_id', $driver->user_id)->pluck('rating');
    $newRating = $allRatings->avg();
    $driver->rating = $newRating;
    $driver->save();
    
    // Mark the ride as reviewed
    $ride->is_reviewed = true;
    $ride->save();
    
    return back()->with('success', 'Thank you for your rating and feedback!');
}
    
    /**
     * Save favorite locations
     */
    public function saveFavoriteLocation(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'address' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'type' => 'required|in:home,work,other'
        ]);
        
        $passenger = Passenger::where('user_id', Auth::id())->first();
        
        // Save location using model method
        $passenger->saveFavoriteLocation($validated);
        
        return back()->with('success', 'Location saved successfully.');
    }
    
    /**
     * Save ride preferences
     */
    public function saveRidePreferences(Request $request)
{
    $validated = $request->validate([
        'preferences' => 'required|array'
    ]);
    
    $passenger = Passenger::where('user_id', Auth::id())->first();
    $user = Auth::user();
    
    // Validate women_only_rides preference - only female passengers can set this
    if (isset($validated['preferences']['women_only_rides']) && 
        $validated['preferences']['women_only_rides'] && 
        $user->gender !== 'female') {
        
        return response()->json([
            'success' => false,
            'message' => 'Women-only rides are only available for female passengers'
        ], 400);
    }
    
    // Handle vehicle_features array
    if (isset($validated['preferences']['vehicle_features']) && is_array($validated['preferences']['vehicle_features'])) {
        // Ensure only valid features are included
        $validFeatures = ['ac', 'wifi', 'child_seat', 'usb_charger', 'pet_friendly', 'luggage_carrier'];
        $validated['preferences']['vehicle_features'] = array_intersect(
            $validated['preferences']['vehicle_features'], 
            $validFeatures
        );
    }
    
    // Initialize ride_preferences if it doesn't exist
    if (!$passenger->ride_preferences) {
        $passenger->ride_preferences = [];
    }
    
    // Merge new preferences with existing ones
    $passenger->ride_preferences = array_merge($passenger->ride_preferences ?? [], $validated['preferences']);
    $passenger->save();
    
    // Also update the user's women_only_rides preference if present
    if (isset($validated['preferences']['women_only_rides'])) {
        $user->women_only_rides = $validated['preferences']['women_only_rides'];
        $user->save();
    }
    
    return response()->json(['success' => true]);
}
    
    /**
     * Calculate fare based on vehicle type and distance
     * 
     * @param string $vehicleType The type of vehicle
     * @param float $distanceInKm The distance in kilometers
     * @param float $surgeMultiplier The surge pricing multiplier
     * @return array The fare details
     */
    private function calculateFare($vehicleType, $distanceInKm, $surgeMultiplier = 1.0)
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
            'total_fare' => round($totalFare, 2)
        ];
    }
    
    /**
     * Calculate distance between two coordinates using Haversine formula
     * 
     * @param float $lat1 First latitude
     * @param float $lon1 First longitude
     * @param float $lat2 Second latitude
     * @param float $lon2 Second longitude
     * @return float Distance in kilometers
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Radius of the earth in km
        
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        
        $a = sin($dLat/2) * sin($dLat/2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        
        return $earthRadius * $c;
    }
    
    /**
     * Calculate surge pricing multiplier based on demand
     * 
     * @param float $latitude Pickup latitude
     * @param float $longitude Pickup longitude
     * @return float Surge multiplier (1.0-2.0)
     */
    private function calculateSurgePricing($latitude, $longitude)
    {
        // Count available drivers in the area
        $availableDrivers = DriverLocation::join('drivers', 'driver_locations.driver_id', '=', 'drivers.id')
            ->join('users', 'drivers.user_id', '=', 'users.id')
            ->whereRaw("
                (6371 * acos(
                    cos(radians(?)) * 
                    cos(radians(driver_locations.latitude)) * 
                    cos(radians(driver_locations.longitude) - radians(?)) + 
                    sin(radians(?)) * 
                    sin(radians(driver_locations.latitude))
                )) <= ?", [$latitude, $longitude, $latitude, 5])
            ->where('users.is_online', true)
            ->count();
            
        // Count active ride requests in the area
        $activeRequests = Ride::whereRaw("
                (6371 * acos(
                    cos(radians(?)) * 
                    cos(radians(pickup_latitude)) * 
                    cos(radians(pickup_longitude) - radians(?)) + 
                    sin(radians(?)) * 
                    sin(radians(pickup_latitude))
                )) <= ?", [$latitude, $longitude, $latitude, 5])
            ->where('reservation_status', 'matching')
            ->orWhere(function($query) {
                $query->where('reservation_status', 'accepted')
                    ->where('ride_status', 'ongoing')
                    ->whereNull('dropoff_time');
            })
            ->count();
            
        // Calculate demand/supply ratio
        if ($availableDrivers == 0) {
            return 1.5; // Default surge if no drivers
        }
        
        $ratio = $activeRequests / $availableDrivers;
        
        // Apply surge pricing based on ratio
        if ($ratio <= 0.5) {
            return 1.0; // Normal pricing
        } elseif ($ratio <= 0.8) {
            return 1.2; // Slight surge
        } elseif ($ratio <= 1.2) {
            return 1.5; // Moderate surge
        } else {
            return 2.0; // High surge
        }
    }
    
    /**
     * Estimate wait time for a ride based on available drivers
     * 
     * @param float $latitude Pickup latitude
     * @param float $longitude Pickup longitude
     * @param string $vehicleType Vehicle type
     * @return int Estimated wait time in minutes
     */
    private function estimateWaitTime($latitude, $longitude, $vehicleType)
    {
        // Find nearest driver of the specified type
        $nearestDriver = DriverLocation::join('drivers', 'driver_locations.driver_id', '=', 'drivers.id')
            ->join('users', 'drivers.user_id', '=', 'users.id')
            ->join('vehicles', 'drivers.id', '=', 'vehicles.driver_id')
            ->selectRaw("
                driver_locations.latitude,
                driver_locations.longitude,
                (6371 * acos(
                    cos(radians(?)) * 
                    cos(radians(driver_locations.latitude)) * 
                    cos(radians(driver_locations.longitude) - radians(?)) + 
                    sin(radians(?)) * 
                    sin(radians(driver_locations.latitude))
                )) AS distance_km", [$latitude, $longitude, $latitude])
            ->where('users.is_online', true)
            ->where('vehicles.type', $vehicleType)
            ->whereRaw("
                (6371 * acos(
                    cos(radians(?)) * 
                    cos(radians(driver_locations.latitude)) * 
                    cos(radians(driver_locations.longitude) - radians(?)) + 
                    sin(radians(?)) * 
                    sin(radians(driver_locations.latitude))
                )) <= ?", [$latitude, $longitude, $latitude, 10])
            ->orderByRaw('distance_km ASC')
            ->first();
            
        if (!$nearestDriver) {
            // No nearby drivers, estimate higher wait time
            return 15; // Default 15 minutes
        }
        
        // Calculate ETA based on distance
        return $this->calculateETA(
            $nearestDriver->latitude,
            $nearestDriver->longitude,
            $latitude,
            $longitude
        );
    }
    
   /**
     * Calculate ETA between two points
     * 
     * @param float $driverLat Driver latitude
     * @param float $driverLng Driver longitude
     * @param float $pickupLat Pickup latitude
     * @param float $pickupLng Pickup longitude
     * @return int Estimated time in minutes
     */
    private function calculateETA($driverLat, $driverLng, $pickupLat, $pickupLng)
    {
        // Calculate distance in km
        $distance = $this->calculateDistance($driverLat, $driverLng, $pickupLat, $pickupLng);
        
        // Assuming average speed of 30 km/h
        $timeInHours = $distance / 30;
        $timeInMinutes = ceil($timeInHours * 60);
        
        // Add a buffer for traffic and other delays
        $estimatedTime = $timeInMinutes + 2;
        
        return max(1, $estimatedTime); // Minimum 1 minute
    }
    
    /**
     * Format duration in minutes to readable text
     *
     * @param int $minutes Duration in minutes
     * @return string Formatted duration text
     */
    private function formatDuration($minutes)
    {
        if ($minutes < 1) {
            return 'Less than a minute';
        } elseif ($minutes < 60) {
            return $minutes . ' ' . ($minutes === 1 ? 'minute' : 'minutes');
        } else {
            $hours = floor($minutes / 60);
            $mins = $minutes % 60;
            $hourText = $hours . ' ' . ($hours === 1 ? 'hour' : 'hours');
            $minText = $mins > 0 ? ' ' . $mins . ' ' . ($mins === 1 ? 'minute' : 'minutes') : '';
            return $hourText . $minText;
        }
    }
    
    /**
     * Mark a passenger as no-show after driver has waited
     */
    public function markNoShow(Request $request, $rideId)
    {
        $ride = Ride::findOrFail($rideId);
        $driver = Driver::where('user_id', Auth::id())->first();
        
        // Security check
        if ($ride->driver_id !== $driver->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Check if the ride is in the correct state (driver arrived but passenger not picked up)
        if ($ride->reservation_status !== 'accepted' || $ride->pickup_time) {
            return response()->json(['error' => 'Invalid ride state for no-show'], 400);
        }
        
        // Check if waiting time is at least 5 minutes
        $waitingTime = now()->diffInMinutes($ride->reservation_date);
        if ($waitingTime < 5) {
            return response()->json([
                'error' => 'Please wait at least 5 minutes before marking as no-show',
                'waiting_time' => $waitingTime,
                'remaining_time' => 5 - $waitingTime
            ], 400);
        }
        
        // Mark the ride as cancelled with no-show reason
        $ride->reservation_status = 'cancelled';
        $ride->ride_status = 'cancelled';
        $ride->wait_time_minutes = $waitingTime;
        $ride->save();
        
        // Add a cancellation fee to the driver's balance
        $cancellationFee = 30; // Fixed fee of 30 MAD for no-show
        $driver->balance += $cancellationFee;
        $driver->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Passenger marked as no-show. You will receive a cancellation fee.',
            'fee' => $cancellationFee
        ]);
    }
    
    /**
     * Handle passenger's destination change mid-ride
     */
    public function changeDestination(Request $request, $rideId)
    {
        $validated = $request->validate([
            'new_dropoff_location' => 'required|string|max:255',
            'new_dropoff_latitude' => 'required|numeric|between:-90,90',
            'new_dropoff_longitude' => 'required|numeric|between:-180,180',
        ]);
        
        $ride = Ride::findOrFail($rideId);
        $passenger = Passenger::where('user_id', Auth::id())->first();
        
        // Check if ride belongs to passenger
        if ($ride->passenger_id !== $passenger->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Check if ride is in progress (pickup time exists)
        if (!$ride->pickup_time || $ride->dropoff_time) {
            return response()->json(['error' => 'Cannot change destination for this ride'], 400);
        }
        
        // Get original and new coordinates
        $originalDropoffLat = $ride->dropoff_latitude;
        $originalDropoffLng = $ride->dropoff_longitude;
        $newDropoffLat = $validated['new_dropoff_latitude'];
        $newDropoffLng = $validated['new_dropoff_longitude'];
        
        // Calculate new distance
        $pickupToOriginalDropoff = $this->calculateDistance(
            $ride->pickup_latitude,
            $ride->pickup_longitude,
            $originalDropoffLat,
            $originalDropoffLng
        );
        
        $pickupToNewDropoff = $this->calculateDistance(
            $ride->pickup_latitude,
            $ride->pickup_longitude,
            $newDropoffLat,
            $newDropoffLng
        );
        
        // Calculate fare difference
        $originalFare = $ride->price;
        $farePerKm = $ride->per_km_price;
        $distanceDifference = $pickupToNewDropoff - $pickupToOriginalDropoff;
        $fareDifference = $distanceDifference * $farePerKm * ($ride->surge_multiplier ?? 1.0);
        $newFare = $originalFare + $fareDifference;
        
        // Update ride information
        $ride->dropoff_location = $validated['new_dropoff_location'];
        $ride->dropoff_latitude = $newDropoffLat;
        $ride->dropoff_longitude = $newDropoffLng;
        $ride->distance_in_km = $pickupToNewDropoff;
        $ride->price = max(0, $newFare); // Ensure fare doesn't go negative
        $ride->ride_cost = max(0, $newFare); // For backward compatibility
        $ride->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Destination updated successfully',
            'new_fare' => round($newFare, 2),
            'fare_difference' => round($fareDifference, 2),
            'original_fare' => $originalFare,
            'new_distance_km' => round($pickupToNewDropoff, 2)
        ]);
    }


    /**
 * Debug available drivers (admin or development only)
 */
public function debugAvailableDrivers(Request $request)
{
    $results = [];
    
    // ADDED: Clear pickup coordinates from session to ensure we don't use stale data
    if (session()->has('ride_search')) {
        $rideSearch = session('ride_search');
        \Log::info("Current ride_search in session: " . json_encode($rideSearch));
    }
    
    // Get all drivers
    $allDrivers = Driver::with(['user', 'vehicle', 'driverLocation'])->get();
    
    foreach ($allDrivers as $driver) {
        $status = 'unavailable';
        $reasons = [];
        
        // Check if driver is online
        if (!$driver->user || !$driver->user->is_online) {
            $reasons[] = 'Driver is offline';
        }
        
        // Check if driver's account is activated
        if ($driver->user && $driver->user->account_status !== 'activated') {
            $reasons[] = 'Account status: ' . $driver->user->account_status;
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
        if (!$driver->driverLocation) {
            $reasons[] = 'No location data available';
        }
        elseif ($driver->driverLocation->last_updated->lt(now()->subMinutes(10))) {
            $reasons[] = 'Location not updated in last 10 minutes';
            // ADDED: Show actual time
            $minutes = now()->diffInMinutes($driver->driverLocation->last_updated);
            $reasons[count($reasons)-1] .= " (last update: {$minutes} minutes ago)";
        } else {
            // If location is recent, calculate distance
            $pickup_lat = $request->input('lat', 0);
            $pickup_lng = $request->input('lng', 0);
            
            if ($pickup_lat && $pickup_lng) {
                $distance = $this->rideMatchingService->calculateDistance(
                    $pickup_lat,
                    $pickup_lng,
                    $driver->driverLocation->latitude,
                    $driver->driverLocation->longitude
                );
                
                if ($distance > 30) {
                    $reasons[] = 'Too far: ' . round($distance, 2) . 'km away';
                }
            }
        }
        
        // Check if driver is on an active ride
        $activeRide = Ride::where('driver_id', $driver->id)
            ->where('ride_status', 'ongoing')
            ->whereNull('dropoff_time')
            ->exists();
            
        if ($activeRide) {
            $reasons[] = 'Currently on an active ride';
        }
        
        // Determine overall status
        if (empty($reasons)) {
            $status = 'available';
        }
        
        $results[] = [
            'id' => $driver->id,
            'name' => $driver->user ? $driver->user->name : 'Unknown',
            'status' => $status,
            'reasons' => $reasons,
            'location_updated' => $driver->driverLocation ? $driver->driverLocation->last_updated->diffForHumans() : 'Never',
            'vehicle_type' => $driver->vehicle ? $driver->vehicle->type : 'None'
        ];
    }
    
    // ADDED: Add debug information for the current user
    $currentUser = Auth::user();
    $currentPassenger = Passenger::where('user_id', $currentUser->id)->first();
    
    $debugInfo = [
        'user_id' => $currentUser->id,
        'gender' => $currentUser->gender,
        'women_only_rides' => $currentUser->women_only_rides ? 'Yes' : 'No',
        'passenger_id' => $currentPassenger ? $currentPassenger->id : 'None',
    ];
    
    return view('passenger.debugDrivers', compact('results', 'debugInfo'));
}

public function nearbyDrivers(Request $request)
{
    $latitude = $request->input('latitude');
    $longitude = $request->input('longitude');
    
    // If no coordinates provided, try to get from session
    if (!$latitude || !$longitude) {
        $rideSearch = session('ride_search');
        if ($rideSearch) {
            $latitude = $rideSearch['pickup_latitude'] ?? null;
            $longitude = $rideSearch['pickup_longitude'] ?? null;
        }
    }
    
    // Validate coordinates exist
    if (!$latitude || !$longitude) {
        return view('passenger.nearbyDrivers', [
            'drivers' => [],
            'has_coordinates' => false,
            'vehicleTypes' => ['basic', 'comfort', 'black', 'wav']
        ]);
    }
    
    $vehicleType = $request->input('vehicle_type', 'basic');

    // Get nearby drivers
    $nearbyDrivers = $this->rideMatchingService->getAvailableDriversForSelection(
        $latitude,
        $longitude,
        $vehicleType,
        Auth::user()
    );
    
    return view('passenger.nearbyDrivers', [
        'drivers' => $nearbyDrivers,
        'has_coordinates' => true,
        'latitude' => $latitude,
        'longitude' => $longitude,
        'vehicle_type' => $vehicleType,
        'vehicleTypes' => ['basic', 'comfort', 'black', 'wav']
    ]);
}
public function clearSessionData()
{
    // Clear ride search data from session
    session()->forget('ride_search');
    
    return response()->json([
        'status' => 'success',
        'message' => 'Session data cleared'
    ]);
}

}