<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Driver;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Ride;
use App\Models\RideRequest;
use App\Models\DriverLocation;
use App\Models\Review;
use App\Models\FareSetting;
use App\Models\VehicleFeature;
use App\Services\RideMatchingService;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DriverController extends Controller
{
    protected $rideMatchingService;
    
    public function __construct(RideMatchingService $rideMatchingService)
    {
        $this->rideMatchingService = $rideMatchingService;
    }
    
    public function index(){
        return view('driver.awaitingRides');
    }

    public function test(){
        return view('driver.attentionNeeded');
    }

    public function driverRegistration(){
        // If user is already pending or activated, redirect to appropriate page
        if (Auth::user()->account_status === 'pending') {
            return redirect()->route('driver.under.review');
        }
        
        if (Auth::user()->account_status === 'activated') {
            return redirect()->route('driver.dashboard');
        }
        
        return view('driver.driverRegistration');
    }

    public function driverRegistrationStore(Request $request){
        // Prevent already pending or activated users from submitting again
        if (in_array(Auth::user()->account_status, ['pending', 'activated'])) {
            return redirect()->route(Auth::user()->account_status === 'pending' ? 'driver.under.review' : 'driver.dashboard');
        }
        
        // Validate request
        $validated = $request->validate([
            // Driver fields
            'user_id' => 'required|exists:users,id',
            'license_number' => 'required|string|max:255|unique:drivers,license_number',
            'license_expiry' => 'required|date|after:today',
            'license_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'women_only_driver' => 'nullable|boolean',
            
            // Vehicle fields
            'make' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer|min:2005|max:' . (date('Y') + 1),
            'color' => 'required|string|max:255',
            'plate_number' => 'required|string|max:255|unique:vehicles,plate_number',
            'type' => ['required', Rule::in(['basic', 'comfort', 'black', 'wav'])],
            'capacity' => 'required|integer|min:1|max:50',
            'vehicle_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'insurance_expiry' => 'required|date|after:today',
            'registration_expiry' => 'required|date|after:today',
            
            // Vehicle features
            'features' => 'nullable|array',
            'features.*' => Rule::in(['ac', 'wifi', 'child_seat', 'usb_charger', 'pet_friendly', 'luggage_carrier']),
        ]);

        // Handle file uploads
        if ($request->hasFile('license_photo')) {
            $validated['license_photo'] = $request->file('license_photo')->store('licenses', 'public');
        }

        if ($request->hasFile('vehicle_photo')) {
            $validated['vehicle_photo'] = $request->file('vehicle_photo')->store('vehicles', 'public');
        }

        // Validate women-only driver requirement - only female users can be women-only drivers
        $user = User::find($validated['user_id']);
        if (!empty($validated['women_only_driver']) && $user->gender !== 'female') {
            return back()->withErrors(['women_only_driver' => 'Only female drivers can register as women-only drivers.'])->withInput();
        }

        // Use a database transaction to ensure all records are created or none
        try {
            DB::beginTransaction();
            
            // Separate driver and vehicle data
            $driverData = [
                'user_id' => $validated['user_id'],
                'license_number' => $validated['license_number'],
                'license_expiry' => $validated['license_expiry'],
                'license_photo' => $validated['license_photo'] ?? null,
                'women_only_driver' => !empty($validated['women_only_driver']),
            ];
            
            // Create the driver
            $driver = Driver::create($driverData);
            
            // Create the vehicle with the new driver's ID
            $vehicleData = [
                'driver_id' => $driver->id,
                'make' => $validated['make'],
                'model' => $validated['model'],
                'year' => $validated['year'],
                'color' => $validated['color'],
                'plate_number' => $validated['plate_number'],
                'type' => $validated['type'],
                'capacity' => $validated['capacity'],
                'vehicle_photo' => $validated['vehicle_photo'] ?? null,
                'insurance_expiry' => $validated['insurance_expiry'],
                'registration_expiry' => $validated['registration_expiry'],
            ];
            
            // Enforce women vehicle type restriction - only female drivers with women_only_driver=true
            // if ($validated['type'] === 'women' && (!$user->gender === 'female' || !$validated['women_only_driver'])) {
            //     throw new \Exception('Only female drivers can register for the "Women" vehicle type.');
            // }
            
            $vehicle = Vehicle::create($vehicleData);
            
            // Add vehicle features if present
            if (!empty($validated['features'])) {
                foreach ($validated['features'] as $feature) {
                    $vehicle->features()->create(['feature' => $feature]);
                }
            }
            
            // Update user's account status to 'pending'
            $user->account_status = 'pending';
            $user->save();
            
            DB::commit();
            
            // Redirect to the under review page
            return redirect()->route('driver.under.review');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Clean up any uploaded files if transaction failed
            if (isset($validated['license_photo'])) {
                Storage::disk('public')->delete($validated['license_photo']);
            }
            if (isset($validated['vehicle_photo'])) {
                Storage::disk('public')->delete($validated['vehicle_photo']);
            }
            
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function underReview(){
        // Get current authenticated user's status
        $status = Auth::user()->account_status;
        
        // Show different messages based on account status
        $title = 'Your Account is Under Review';
        $message = 'Thank you for registering as a driver with inTime. Our team is currently reviewing your documents and vehicle information.';
        
        if ($status === 'deactivated') {
            // User with deactivated status should be redirected to registration
            return redirect()->route('driverRegistration.create');
        } else if ($status === 'activated') {
            // User with activated status should be redirected to dashboard
            return redirect()->route('driver.dashboard');
        } else if ($status === 'suspended' || $status === 'deleted') {
            return redirect()->route('home')->with('error', 'Your account has been ' . $status . '. Please contact support for assistance.');
        }
        
        return view('driver.underReview', compact('title', 'message', 'status'));
    }

    public function dashboard()
    {
        // Get the authenticated user
        $user = Auth::user();
        
        // Get driver details
        $driver = Driver::with('vehicle')->where('user_id', $user->id)->first();
        
        // Get driver statistics
        $stats = [
            'completed_rides' => $driver->completed_rides ?? 0,
            'total_income' => $user->total_income ?? 0,
            'rating' => $driver->rating ?? 0,
        ];
        
        // Get new ride requests - these need to be responded to quickly
        $rideRequests = RideRequest::with(['ride.passenger.user'])
            ->where('driver_id', $driver->id)
            ->where('status', 'pending')
            ->orderBy('requested_at', 'desc')
            ->get();
            
        // Get pending ride requests (old system compatibility)
        $pendingRides = Ride::with('passenger.user')
            ->where('driver_id', $driver->id)
            ->where('reservation_status', 'pending')
            ->orderBy('reservation_date', 'asc')
            ->get();
        
        // Get upcoming rides (accepted but not yet started)
        $upcomingRides = Ride::with('passenger.user')
            ->where('driver_id', $driver->id)
            ->where('reservation_status', 'accepted')
            ->where('ride_status', 'ongoing')
            ->where(function($query) {
                $query->whereNull('pickup_time')
                    ->orWhere('pickup_time', '>', now());
            })
            ->orderBy('reservation_date', 'asc')
            ->get();
        
        // Get active rides (in progress)
        $activeRides = Ride::with('passenger.user')
            ->where('driver_id', $driver->id)
            ->where('reservation_status', 'accepted')
            ->where('ride_status', 'ongoing')
            ->whereNotNull('pickup_time')
            ->whereNull('dropoff_time')
            ->get();
        
        return view('driver.dashboard', compact('user', 'driver', 'stats', 'rideRequests', 'pendingRides', 'upcomingRides', 'activeRides'));
    }
    
    /**
     * Show incoming ride requests
     */
    public function incomingRequests()
    {
        $driver = Driver::where('user_id', Auth::id())->first();
        
        $requests = RideRequest::with(['ride.passenger.user'])
            ->where('driver_id', $driver->id)
            ->where('status', 'pending')
            ->orderBy('requested_at', 'desc')
            ->get();
            
        return view('driver.incomingRequests', compact('requests'));
    }
    
    /**
     * Respond to a ride request (Accept/Reject within time window)
     */
    public function respondToRequest(Request $request, $requestId)
{
    $validated = $request->validate([
        'response' => ['required', Rule::in(['accept', 'reject'])],
    ]);
    
    $rideRequest = RideRequest::findOrFail($requestId);
    $driver = Driver::where('user_id', Auth::id())->first();
    
    // Security check
    if ($rideRequest->driver_id !== $driver->id) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }
    
    // MODIFICATION: Don't strictly check if pending, check if it's not already accepted or rejected
    // This way, even if client-side timing is off, we can still accept requests
    if (!in_array($rideRequest->status, ['pending', 'expired'])) {
        return response()->json([
            'error' => 'This request has already been processed',
            'current_status' => $rideRequest->status
        ], 400);
    }
    
    // If the request was marked as expired but we're still accepting it within a grace period (30 seconds)
    // allow the acceptance to go through
    if ($rideRequest->status === 'expired') {
        $gracePeriodSeconds = 30; // 30 second grace period
        $expiredRecently = $rideRequest->responded_at && 
            $rideRequest->responded_at->diffInSeconds(now()) <= $gracePeriodSeconds;
            
        if (!$expiredRecently) {
            return response()->json([
                'error' => 'This request has expired and is outside the grace period',
                'expired_at' => $rideRequest->responded_at
            ], 400);
        }
        
        // Log that we're accepting an expired request within grace period
        \Log::info("Driver #{$driver->id} accepting expired request #{$requestId} within grace period");
    }
    
    // Process the response without strict time limit
    $result = $this->rideMatchingService->handleDriverResponse(
        $rideRequest, 
        $validated['response'] === 'accept' ? 'accept' : 'reject'
    );
    
    if ($validated['response'] === 'accept') {
        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Ride request accepted successfully',
                'redirect' => route('driver.active.rides')
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to accept ride request. Please try again.'
            ]);
        }
    } else {
        return response()->json([
            'success' => true,
            'message' => 'Ride request rejected'
        ]);
    }
}
    
    /**
     * Update driver's online status (Available/Unavailable)
     */
    public function updateOnlineStatus(Request $request)
    {
        $validated = $request->validate([
            'is_online' => 'required|boolean',
        ]);
        
        $user = Auth::user();
        $user->is_online = $validated['is_online'];
        $user->save();
        
        // If going online, request location update
        $response = [
            'status' => 'success',
            'is_online' => $user->is_online,
        ];
        
        if ($validated['is_online']) {
            $response['request_location'] = true;
        } else {
            // If going offline, clear any active ride requests
            $driver = Driver::where('user_id', $user->id)->first();
            if ($driver) {
                RideRequest::where('driver_id', $driver->id)
                    ->where('status', 'pending')
                    ->update(['status' => 'expired']);
            }
        }
        
        return response()->json(['status' => 'success']);
    }
    
    /**
     * Update driver's location (real-time tracking)
     */
    public function updateLocation(Request $request)
{
    $validated = $request->validate([
        'latitude' => 'required|numeric|between:-90,90',
        'longitude' => 'required|numeric|between:-180,180',
    ]);
    
    $driver = Driver::where('user_id', Auth::id())->first();
    
    if (!$driver) {
        return response()->json([
            'status' => 'error',
            'message' => 'Driver record not found',
        ], 404);
    }
    
    // Make sure driver is online before updating location
    $user = Auth::user();
    
    // If driver is offline but sending location, automatically set them online
    if (!$user->is_online) {
        $user->is_online = true;
        $user->save();
        
        // Log this automatic status change
        \Log::info("Driver #{$driver->id} was automatically set to online when sending location");
    }
    
    // Update or create driver location with current timestamp
    try {
        $location = DriverLocation::updateOrCreate(
            ['driver_id' => $driver->id],
            [
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'last_updated' => now(),
            ]
        );
        
        // Confirm the update was successful
        if ($location->wasRecentlyCreated || $location->wasChanged()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Location updated successfully',
                'timestamp' => now()->toIso8601String(),
                'driver_status' => [
                    'is_online' => true,
                    'location_sharing' => true,
                    'account_status' => $user->account_status,
                    'vehicle_status' => $driver->vehicle ? ($driver->vehicle->is_active ? 'Active' : 'Inactive') : 'No vehicle',
                    'visibility_to_passengers' => ($user->is_online && 
                                                $user->account_status === 'activated' && 
                                                $driver->is_verified && 
                                                ($driver->vehicle && $driver->vehicle->is_active))
                ]
            ]);
        } else {
            return response()->json([
                'status' => 'warning',
                'message' => 'Location received but no changes were made',
            ]);
        }
    } catch (\Exception $e) {
        \Log::error("Location update failed for driver #{$driver->id}: " . $e->getMessage());
        
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to update location: ' . $e->getMessage(),
        ], 500);
    }
}
    
    /**
     * Show driver's active rides
     */
    public function activeRides()
    {
        $driver = Driver::where('user_id', Auth::id())->first();
        
        // Get rides where the driver is en route to the pickup
        $enRouteRides = Ride::with(['passenger.user'])
            ->where('driver_id', $driver->id)
            ->where('reservation_status', 'accepted')
            ->where('ride_status', 'ongoing')
            ->whereNull('pickup_time')
            ->orderBy('reservation_date', 'asc')
            ->get();
            
        // Get rides where the passenger has been picked up but not dropped off
        $inProgressRides = Ride::with(['passenger.user'])
            ->where('driver_id', $driver->id)
            ->where('reservation_status', 'accepted')
            ->where('ride_status', 'ongoing')
            ->whereNotNull('pickup_time')
            ->whereNull('dropoff_time')
            ->orderBy('pickup_time', 'asc')
            ->get();
            
        return view('driver.activeRides', compact('enRouteRides', 'inProgressRides'));
    }

   /**
    * Show list of awaiting/pending ride requests
    */
    public function awaitingRides()
    {
        $driver = Driver::where('user_id', Auth::id())->first();
        
        // Get all pending rides for this driver
        $pendingRides = Ride::with('passenger.user')
            ->where('driver_id', $driver->id)
            ->where('reservation_status', 'pending')
            ->orderBy('reservation_date', 'asc')
            ->get();
            
        return view('driver.awaitingRides', compact('pendingRides'));
    }
    
    /**
     * Start a ride (driver has arrived for pickup)
     */
    public function startRide(Request $request, $rideId)
    {
        $ride = Ride::findOrFail($rideId);
        $driver = Driver::where('user_id', Auth::id())->first();
        
        // Check if this ride belongs to this driver
        if ($ride->driver_id !== $driver->id) {
            return back()->with('error', 'You are not authorized to start this ride.');
        }
        
        // Check if the ride is in the correct state to be started
        if ($ride->reservation_status !== 'accepted') {
            return back()->with('error', 'This ride cannot be started because it has not been accepted.');
        }
        
        // Get current location for verification
        if ($request->has('latitude') && $request->has('longitude')) {
            $driverLat = $request->input('latitude');
            $driverLng = $request->input('longitude');
            
            // Calculate distance from pickup point
            $distance = $this->rideMatchingService->calculateDistance(
                $driverLat,
                $driverLng,
                $ride->pickup_latitude,
                $ride->pickup_longitude
            );
            
            // If driver is too far from pickup location (more than 300m), warn but allow start
            if ($distance > 0.3) {
                // Log the discrepancy but still allow the ride to start
                // This could be expanded to require confirmation or prevent starting
                \Log::warning("Driver started ride from {$distance}km away from pickup location. Ride ID: {$ride->id}");
            }
        }
        
        $ride->pickup_time = now();
        $ride->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Ride started successfully',
            'ride' => $ride
        ]);
    }
    
    /**
     * Complete a ride (passenger has been dropped off)
     */
    public function completeRide(Request $request, $rideId)
    {
        $ride = Ride::findOrFail($rideId);
        $driver = Driver::where('user_id', Auth::id())->first();
    
        // Check if this ride belongs to this driver
        if ($ride->driver_id !== $driver->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    
        // Check if the ride is in the correct state to be completed
        if ($ride->pickup_time === null) {
            return response()->json(['error' => 'This ride cannot be completed because it has not been started'], 400);
        }
    
        $ride->dropoff_time = now();
        $ride->ride_status = 'completed';
        
        // Get current location for verification
        if ($request->has('latitude') && $request->has('longitude')) {
            $driverLat = $request->input('latitude');
            $driverLng = $request->input('longitude');
            
            // Calculate distance from dropoff point
            $distance = $this->rideMatchingService->calculateDistance(
                $driverLat,
                $driverLng,
                $ride->dropoff_latitude,
                $ride->dropoff_longitude
            );
            
            // If driver is too far from dropoff location (more than 300m), log but allow completion
            if ($distance > 0.3) {
                \Log::warning("Driver completed ride from {$distance}km away from dropoff location. Ride ID: {$ride->id}");
            }
        }
        
        // Calculate actual distance traveled (if route tracking was implemented)
        // For now, we'll use the direct distance between pickup and dropoff
        $actualDistanceKm = $this->rideMatchingService->calculateDistance(
            $ride->pickup_latitude,
            $ride->pickup_longitude,
            $ride->dropoff_latitude,
            $ride->dropoff_longitude
        );
        
        // Update ride with actual distance
        $ride->distance_in_km = $actualDistanceKm;
        
        // Calculate waiting time (time between scheduled pickup and actual pickup)
        $waitTimeMinutes = 0;
        if ($ride->pickup_time && $ride->reservation_date) {
            // Calculate waiting time
            $scheduledPickup = $ride->reservation_date;
            $actualPickup = $ride->pickup_time;
            
            // If driver arrived late, don't charge for waiting time
            if ($actualPickup > $scheduledPickup) {
                $waitTimeMinutes = 0;
            } else {
                // Calculate time passenger kept the driver waiting
                $waitTimeMinutes = max(0, Carbon::parse($actualPickup)->diffInMinutes($scheduledPickup));
            }
        }
        
        // Add waiting time to the ride
        $ride->wait_time_minutes = $waitTimeMinutes;
        
        // Calculate ride duration for time-based fare component
        $rideDurationMinutes = Carbon::parse($ride->pickup_time)->diffInMinutes($ride->dropoff_time);
        
        // Get fare settings for the vehicle type
        $vehicleType = $ride->vehicle_type ?? $driver->vehicle->type;
        $fareSetting = FareSetting::where('vehicle_type', $vehicleType)->first();

        
        if (!$fareSetting) {
            // Fallback to default pricing if no fare settings found
            $base_fare = 50;
            $per_km_price = 15;
            $per_minute_price = 0;
        } else {
            $base_fare = $fareSetting->base_fare;
            $per_km_price = $fareSetting->per_km_price;
            $per_minute_price = $fareSetting->per_minute_price;
        }
        
        // Record the fare components
        $ride->base_fare = $base_fare;
        $ride->per_km_price = $per_km_price;
        
        // Calculate waiting fee
        $waitingFeePerMinute = 0.5; // 0.5 MAD per minute of waiting time
        $waitingFee = $waitTimeMinutes * $waitingFeePerMinute;
        
        // Calculate fare components
        $distanceFare = $actualDistanceKm * $per_km_price;
        $timeFare = $rideDurationMinutes * $per_minute_price;
        
        // Calculate basic fare
        $baseFare = $base_fare + $distanceFare + $timeFare;
        
        // Apply surge pricing if applicable
        $surgeMultiplier = $ride->surge_multiplier ?? 1.0;
        $finalFare = ($baseFare * $surgeMultiplier) + $waitingFee;
        
        // Ensure minimum fare
        if ($fareSetting && $finalFare < $fareSetting->minimum_fare) {
            $finalFare = $fareSetting->minimum_fare;
        }
        
        // Update ride with final price
        $ride->price = $finalFare;
        $ride->ride_cost = $finalFare; // For backward compatibility
        $ride->save();
    
        // Update driver stats
        $driver->completed_rides += 1;
        $driver->balance += $finalFare;
        $driver->save();
    
        // Update user total income
        $user = User::find(Auth::id());
        $user->total_income += $finalFare;
        $user->save();
    
        return response()->json([
            'success' => true,
            'message' => 'Ride completed successfully',
            'fare' => [
                'base_fare' => $base_fare,
                'distance_fare' => $distanceFare,
                'time_fare' => $timeFare,
                'waiting_fee' => $waitingFee,
                'surge_multiplier' => $surgeMultiplier,
                'final_fare' => $finalFare,
                'distance_km' => $actualDistanceKm,
                'duration_minutes' => $rideDurationMinutes
            ]
        ]);
    }
    
    /**
     * View ride history
     */
    public function rideHistory()
    {
        $driver = Driver::where('user_id', Auth::id())->first();
        
        $completedRides = Ride::with(['passenger.user'])
            ->where('driver_id', $driver->id)
            ->where('ride_status', 'completed')
            ->orderBy('dropoff_time', 'desc')
            ->paginate(10);
            
        return view('driver.rideHistory', compact('completedRides'));
    }
    
    /**
     * View driver earnings
     */
    public function earnings()
    {
        $driver = Driver::where('user_id', Auth::id())->first();
        
        // Get earnings grouped by day for the last 30 days
        $dailyEarnings = Ride::where('driver_id', $driver->id)
            ->where('ride_status', 'completed')
            ->where('dropoff_time', '>=', now()->subDays(30))
            ->select(
                DB::raw('DATE(dropoff_time) as date'),
                DB::raw('SUM(price) as total')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
            
        // Get total stats
        $stats = [
            'today' => $dailyEarnings->where('date', now()->format('Y-m-d'))->sum('total'),
            'week' => Ride::where('driver_id', $driver->id)
                ->where('ride_status', 'completed')
                ->where('dropoff_time', '>=', now()->startOfWeek())
                ->sum('price'),
            'month' => Ride::where('driver_id', $driver->id)
                ->where('ride_status', 'completed')
                ->where('dropoff_time', '>=', now()->startOfMonth())
                ->sum('price'),
            'total' => $driver->balance,
            'completed_rides' => $driver->completed_rides,
            'avg_rating' => $driver->rating
        ];
        
        // Get recent completed rides
        $recentRides = Ride::with(['passenger.user'])
            ->where('driver_id', $driver->id)
            ->where('ride_status', 'completed')
            ->orderBy('dropoff_time', 'desc')
            ->limit(5)
            ->get();
            
        return view('driver.earnings', compact('dailyEarnings', 'stats', 'recentRides'));
    }
    
    /**
     * Update driver profile information
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $driver = Driver::where('user_id', Auth::id())->first();
        
        $validated = $request->validate([
            'women_only_driver' => 'nullable|boolean',
        ]);
        
        // Validate gender requirement for women-only drivers
    if (!empty($validated['women_only_driver']) && $user->gender !== 'female') {
        return back()->withErrors(['women_only_driver' => 'Only female drivers can register as women-only drivers.'])->withInput();
    }
    
    $driver->women_only_driver = !empty($validated['women_only_driver']);
    $driver->save();
    
    return back()->with('success', 'Profile updated successfully.');
    }
    
    /**
     * Toggle women-only driver mode via AJAX
     */
    public function toggleWomenOnlyMode(Request $request)
{
    $user = Auth::user();
    $driver = Driver::where('user_id', Auth::id())->first();
    
    // Check if user is female (only females can toggle this)
    if ($user->gender !== 'female') {
        return response()->json([
            'success' => false,
            'message' => 'Only female drivers can use women-only driver mode'
        ], 403);
    }
    
    try {
        // Toggle women_only_driver
        $driver->women_only_driver = !$driver->women_only_driver;
        $driver->save();
        
        // Give warning if vehicle type doesn't match
        $warning = null;
        if ($driver->women_only_driver && $driver->vehicle && $driver->vehicle->type !== 'women') {
            $warning = 'For best matching results, consider updating your vehicle type to "Women" in vehicle settings.';
        }
        
        return response()->json([
            'success' => true,
            'women_only_driver' => (bool)$driver->women_only_driver, // Cast to boolean for clarity
            'warning' => $warning,
            'message' => $driver->women_only_driver ? 'Women-only driver mode enabled' : 'Women-only driver mode disabled'
        ]);
    } catch (\Exception $e) {
        \Log::error('Error toggling women-only mode: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while updating your preferences.'
        ], 500);
    }
}
    
    /**
     * Update vehicle information
     */
    public function updateVehicle(Request $request)
    {
        $user = Auth::user();
        $driver = Driver::where('user_id', Auth::id())->first();
        $vehicle = $driver->vehicle;
        
        $validated = $request->validate([
            'type' => ['required', Rule::in(['basic', 'comfort', 'black', 'wav'])],
            'make' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer|min:2005|max:' . (date('Y') + 1),
            'color' => 'required|string|max:255',
            'plate_number' => ['required', 'string', 'max:255', Rule::unique('vehicles')->ignore($vehicle->id)],
            'capacity' => 'required|integer|min:1|max:50',
            'vehicle_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'features' => 'nullable|array',
            'features.*' => Rule::in(['ac', 'wifi', 'child_seat', 'usb_charger', 'pet_friendly', 'luggage_carrier']),
        ]);
        
        // Handle file upload
        if ($request->hasFile('vehicle_photo')) {
            // Delete old image if exists
            if ($vehicle->vehicle_photo) {
                Storage::disk('public')->delete($vehicle->vehicle_photo);
            }
            
            $validated['vehicle_photo'] = $request->file('vehicle_photo')->store('vehicles', 'public');
        }
        
    // Update vehicle
    $vehicle->fill([
        'type' => $validated['type'],
        'make' => $validated['make'],
        'model' => $validated['model'],
        'year' => $validated['year'],
        'color' => $validated['color'],
        'plate_number' => $validated['plate_number'],
        'capacity' => $validated['capacity'],
    ]);
        
        if (isset($validated['vehicle_photo'])) {
            $vehicle->vehicle_photo = $validated['vehicle_photo'];
        }
        
        $vehicle->save();
        
        // Update vehicle features
        if (isset($validated['features'])) {
            // Remove existing features
            $vehicle->features()->delete();
            
            // Add new features
            foreach ($validated['features'] as $feature) {
                $vehicle->features()->create(['feature' => $feature]);
            }
        }
        
        return back()->with('success', 'Vehicle information updated successfully.');
    }
    
    /**
     * Cancel a ride (driver cancellation)
     */
    public function cancelRide(Request $request, $rideId)
    {
        $ride = Ride::findOrFail($rideId);
        $driver = Driver::where('user_id', Auth::id())->first();
        
        // Check if ride belongs to driver
        if ($ride->driver_id !== $driver->id) {
            return back()->with('error', 'You are not authorized to cancel this ride.');
        }
        
        // Check if ride can be cancelled (not started yet)
        if ($ride->pickup_time) {
            return back()->with('error', 'Cannot cancel a ride that has already started.');
        }
        
        // Cancel the ride
        $ride->reservation_status = 'cancelled';
        $ride->save();
        
        // Check if driver should be suspended for too many cancellations
        if ($driver->shouldBeSuspendedForCancellations()) {
            $user = $driver->user;
            $user->account_status = 'suspended';
            $user->save();
            
            return back()->with('warning', 'You have cancelled too many rides recently. Your account has been temporarily suspended for 2 hours.');
        }
        
        return back()->with('success', 'Ride cancelled successfully.');
    }

    public function debugDriverStatus()
{
    $driver = Driver::where('user_id', Auth::id())->first();
    
    if (!$driver) {
        return response()->json([
            'success' => false,
            'message' => 'Driver record not found'
        ], 404);
    }
    
    // Get location data
    $location = DriverLocation::where('driver_id', $driver->id)->first();
    
    // Get user data
    $user = Auth::user();
    
    // Collect debug information
    $debugInfo = [
        'driver' => [
            'id' => $driver->id,
            'is_verified' => $driver->is_verified,
            'women_only_driver' => $driver->women_only_driver
        ],
        'user' => [
            'id' => $user->id,
            'is_online' => $user->is_online,
            'account_status' => $user->account_status
        ],
        'vehicle' => null,
        'location' => null,
        'matching_issues' => []
    ];
    
    // Check vehicle
    if ($driver->vehicle) {
        $debugInfo['vehicle'] = [
            'id' => $driver->vehicle->id,
            'type' => $driver->vehicle->type,
            'is_active' => $driver->vehicle->is_active
        ];
    } else {
        $debugInfo['matching_issues'][] = 'No active vehicle found';
    }
    
    // Check location
    if ($location) {
        $debugInfo['location'] = [
            'latitude' => $location->latitude,
            'longitude' => $location->longitude,
            'last_updated' => $location->last_updated,
            'is_recent' => $location->last_updated > now()->subMinutes(5),
            'minutes_ago' => now()->diffInMinutes($location->last_updated)
        ];
        
        if ($location->last_updated <= now()->subMinutes(5)) {
            $debugInfo['matching_issues'][] = 'Location update is older than 5 minutes';
        }
    } else {
        $debugInfo['matching_issues'][] = 'No location data found';
    }
    
    // Check user online status
    if (!$user->is_online) {
        $debugInfo['matching_issues'][] = 'Driver is not online';
    }
    
    // Check account status
    if ($user->account_status !== 'activated') {
        $debugInfo['matching_issues'][] = 'Account is not activated';
    }
    
    // Check vehicle status
    if ($driver->vehicle && !$driver->vehicle->is_active) {
        $debugInfo['matching_issues'][] = 'Vehicle is not active';
    }
    
    // Check driver verification
    if (!$driver->is_verified) {
        $debugInfo['matching_issues'][] = 'Driver is not verified';
    }
    
    return response()->json([
        'success' => true,
        'debug_info' => $debugInfo,
        'eligibility_summary' => empty($debugInfo['matching_issues']) ? 
            'You are eligible to be matched with passengers' : 
            'You are NOT eligible to be matched with passengers due to the issues listed',
        'fix_instructions' => $this->generateFixInstructions($debugInfo['matching_issues'])
    ]);
}

/**
 * Generate instructions to fix issues
 */
private function generateFixInstructions(array $issues)
{
    $instructions = [];
    
    foreach ($issues as $issue) {
        switch ($issue) {
            case 'No location data found':
            case 'Location update is older than 5 minutes':
                $instructions[] = 'Enable location sharing and ensure your device permits background location';
                break;
            case 'Driver is not online':
                $instructions[] = 'Toggle "Go Online" in the app to start receiving ride requests';
                break;
            case 'Account is not activated':
                $instructions[] = 'Contact support as your account requires activation';
                break;
            case 'Vehicle is not active':
                $instructions[] = 'Update your vehicle status to active in vehicle settings';
                break;
            case 'Driver is not verified':
                $instructions[] = 'Complete the verification process or contact support';
                break;
            default:
                $instructions[] = 'Contact support for assistance with this issue';
        }
    }
    
    return array_unique($instructions);
}
public function checkOnlineStatus()
{
    $user = Auth::user();
    
    return response()->json([
        'is_online' => $user->is_online,
        'account_status' => $user->account_status
    ]);
}

public function checkRequestStatus($requestId)
{
    $rideRequest = RideRequest::findOrFail($requestId);
    $driver = Driver::where('user_id', Auth::id())->first();
    
    // Security check
    if ($rideRequest->driver_id !== $driver->id) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }
    
    return response()->json([
        'status' => $rideRequest->status
    ]);
}
public function heartbeat(Request $request)
{
    $user = Auth::user();
    // Update last_active timestamp
    $user->last_active = now();
    $user->save();
    
    return response()->json(['status' => 'success']);
}

public function setOffline(Request $request)
{
    $user = Auth::user();
    $user->is_online = false;
    $user->save();
    
    // Also clear any pending ride requests
    $driver = Driver::where('user_id', $user->id)->first();
    if ($driver) {
        RideRequest::where('driver_id', $driver->id)
            ->where('status', 'pending')
            ->update(['status' => 'expired']);
    }
    
    return response()->json(['status' => 'success']);
}

public function forceLocationRefresh(Request $request)
{
    $validated = $request->validate([
        'latitude' => 'required|numeric|between:-90,90',
        'longitude' => 'required|numeric|between:-180,180',
    ]);
    
    $driver = Driver::where('user_id', Auth::id())->first();
    
    if (!$driver) {
        return response()->json([
            'status' => 'error',
            'message' => 'Driver record not found',
        ], 404);
    }
    
    // Make sure driver is online
    $user = Auth::user();
    if (!$user->is_online) {
        $user->is_online = true;
        $user->save();
    }
    
    // Force update driver location with current timestamp
    try {
        $location = DriverLocation::updateOrCreate(
            ['driver_id' => $driver->id],
            [
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'last_updated' => now(),
            ]
        );
        
        return response()->json([
            'status' => 'success',
            'message' => 'Location forcefully updated',
            'location' => $location,
            'timestamp' => now()->toIso8601String()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}

}