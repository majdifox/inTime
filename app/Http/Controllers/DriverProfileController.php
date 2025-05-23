<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Driver;
use App\Models\Review;
use App\Models\User;
use App\Models\Ride;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\RideRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DriverProfileController extends Controller
{
    /**
     * Display the driver's public profile
     *
     * @param int $id Driver ID
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        try {
            // Get the driver with related data
            $driver = Driver::with(['user', 'vehicle', 'vehicle.features'])
                ->where('id', $id)
                ->firstOrFail();

                $driver->updateCompletedRidesCount();

            // Use raw query to ensure we only get reviews with valid relationships
            $validReviewIds = DB::table('reviews')
                ->join('rides', 'reviews.ride_id', '=', 'rides.id')
                ->join('passengers', 'rides.passenger_id', '=', 'passengers.id')
                ->join('users', 'passengers.user_id', '=', 'users.id')
                ->where('rides.driver_id', $id)
                ->where('reviews.rating', '>', 0)
                ->pluck('reviews.id')
                ->toArray();
                
            // Now get the reviews using the validated IDs
            $reviews = Review::whereIn('id', $validReviewIds)
                ->with(['passenger.user'])
                ->orderBy('created_at', 'desc')
                ->paginate(5);
            
            // Calculate review summary
            $reviewSummary = [
                'total_count' => count($validReviewIds),
                'average_rating' => $driver->rating ?? 5.0,
                'ratings_breakdown' => [
                    5 => Review::whereIn('id', $validReviewIds)->where('rating', 5)->count(),
                    4 => Review::whereIn('id', $validReviewIds)->where('rating', 4)->count(),
                    3 => Review::whereIn('id', $validReviewIds)->where('rating', 3)->count(),
                    2 => Review::whereIn('id', $validReviewIds)->where('rating', 2)->count(),
                    1 => Review::whereIn('id', $validReviewIds)->where('rating', 1)->count(),
                ]
            ];
            
            // Get recent ride locations
            $recentLocations = Ride::where('driver_id', $id)
                ->where('ride_status', 'completed')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->select('pickup_location', 'dropoff_location')
                ->get()
                ->map(function ($ride) {
                    return [
                        'pickup' => $this->getNeighborhood($ride->pickup_location),
                        'dropoff' => $this->getNeighborhood($ride->dropoff_location)
                    ];
                })->toArray();
            
            // Check if the current user has ridden with this driver
            $hasRiddenWith = false;
            if (Auth::check() && Auth::user()->passenger) {
                $hasRiddenWith = Ride::where('driver_id', $id)
                    ->where('passenger_id', Auth::user()->passenger->id)
                    ->where('ride_status', 'completed')
                    ->exists();
            }
            
            return view('driver.profile', compact(
                'driver', 
                'reviews', 
                'reviewSummary', 
                'recentLocations',
                'hasRiddenWith'
            ));
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error displaying driver profile: ' . $e->getMessage());
            
            // Return a more user-friendly error page
            abort(404, 'Driver profile not found');
        }
    }
    
    /**
     * Extract just the neighborhood/city part from an address for privacy
     * 
     * @param string $fullAddress
     * @return string
     */
    private function getNeighborhood($fullAddress)
    {
        // Handle null addresses
        if (!$fullAddress) {
            return 'Unknown area';
        }
        
        // Simple implementation - extract just the last part of the address
        $parts = explode(',', $fullAddress);
        
        if (count($parts) > 1) {
            return trim($parts[count($parts) - 2]);
        }
        
        return 'Unknown area';
    }

    public function profileSettings()
{
    $user = Auth::user();
    $driver = Driver::with(['vehicle', 'vehicle.features'])->where('user_id', $user->id)->first();
    
    // Get all available vehicle features for checkboxes
    $availableFeatures = [
        'ac' => 'Air Conditioning',
        'wifi' => 'WiFi',
        'child_seat' => 'Child Seat',
        'usb_charger' => 'USB Charger',
        'pet_friendly' => 'Pet Friendly',
        'luggage_carrier' => 'Luggage Carrier'
    ];
    
    // Get existing feature values for the vehicle
    $existingFeatures = [];
    if ($driver->vehicle && $driver->vehicle->features) {
        foreach ($driver->vehicle->features as $feature) {
            $existingFeatures[] = $feature->feature;
        }
    }
    
    // Get available languages
    $availableLanguages = [
        'english' => 'English',
        'arabic' => 'Arabic',
        'french' => 'French',
        'spanish' => 'Spanish',
        'german' => 'German'
    ];
    
    return view('drivers.profile-settings', compact(
        'user',
        'driver',
        'availableFeatures',
        'existingFeatures',
        'availableLanguages'
    ));
}

/**
 * Update driver profile information
 */
public function updateProfile(Request $request)
{
    $user = Auth::user();
    $driver = Driver::where('user_id', $user->id)->first();
    
    $validated = $request->validate([
        'bio' => 'nullable|string|max:500',
        'languages' => 'nullable|array',
        'languages.*' => 'string|in:english,arabic,french,spanish,german',
        'women_only_driver' => 'nullable|boolean',
        
        // Vehicle info
        'make' => 'required|string|max:255',
        'model' => 'required|string|max:255',
        'year' => 'required|integer|min:2005|max:' . (date('Y') + 1),
        'color' => 'required|string|max:255',
        'plate_number' => ['required', 'string', 'max:255', Rule::unique('vehicles')->ignore($driver->vehicle->id)],
        'type' => ['required', Rule::in(['basic', 'comfort', 'black', 'wav'])],
        'capacity' => 'required|integer|min:1|max:50',
        'vehicle_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'features' => 'nullable|array',
        'features.*' => Rule::in(['ac', 'wifi', 'child_seat', 'usb_charger', 'pet_friendly', 'luggage_carrier']),
    ]);
    
    // Validate women-only driver requirement - only female users can be women-only drivers
    if (!empty($validated['women_only_driver']) && $user->gender !== 'female') {
        return back()->withErrors(['women_only_driver' => 'Only female drivers can register as women-only drivers.'])->withInput();
    }
    
    // Update driver profile
    $driver->bio = $validated['bio'] ?? null;
    $driver->languages = $validated['languages'] ?? null;
    $driver->women_only_driver = !empty($validated['women_only_driver']);
    $driver->save();
    
    // Update vehicle information
    $vehicle = $driver->vehicle;
    
    // Handle vehicle photo
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
    
    return back()->with('success', 'Profile and vehicle information updated successfully.');
}

/**
 * Update driver password
 */
public function updatePassword(Request $request)
{
    $validated = $request->validate([
        'current_password' => 'required|current_password',
        'password' => 'required|string|min:8|confirmed',
    ]);
    
    $user = Auth::user();
    $user->password = Hash::make($validated['password']);
    $user->save();
    
    return back()->with('success', 'Password updated successfully.');
}

public function privateProfile()
{
    $user = Auth::user();
    $driver = Driver::with(['vehicle', 'vehicle.features'])->where('user_id', $user->id)->firstOrFail();
    $driver->updateCompletedRidesCount();
    // Get recent earnings data for graph
    $recentEarnings = Ride::where('driver_id', $driver->id)
        ->where('ride_status', 'completed')
        ->where('dropoff_time', '>=', now()->subDays(30))
        ->select(
            DB::raw('DATE(dropoff_time) as date'),
            DB::raw('SUM(price) as total')
        )
        ->groupBy('date')
        ->orderBy('date', 'asc')
        ->get();
    
    // Calculate statistics from actual database data
    $completedRides = Ride::where('driver_id', $driver->id)
        ->where('ride_status', 'completed')
        ->count();
        
    $totalEarnings = Ride::where('driver_id', $driver->id)
        ->where('ride_status', 'completed')
        ->where('is_paid', true)
        ->sum('price');
    
    // Assign values to driver object
    $driver->completed_rides = $completedRides;
    
    // Get statistics
    $stats = [
        'today' => $recentEarnings->where('date', now()->format('Y-m-d'))->sum('total'),
        'week' => Ride::where('driver_id', $driver->id)
            ->where('ride_status', 'completed')
            ->where('dropoff_time', '>=', now()->startOfWeek())
            ->sum('price'),
        'month' => Ride::where('driver_id', $driver->id)
            ->where('ride_status', 'completed')
            ->where('dropoff_time', '>=', now()->startOfMonth())
            ->sum('price'),
        'total' => $totalEarnings,
        'completed_rides' => $completedRides,
        'avg_rating' => $driver->rating ?? 0
    ];
    
    // Calculate response rate
    $totalRequests = RideRequest::where('driver_id', $driver->id)->count();
    $respondedRequests = RideRequest::where('driver_id', $driver->id)
        ->whereNotNull('responded_at')
        ->count();
    
    $responseRate = $totalRequests > 0 ? $respondedRequests / $totalRequests : 1;
    $driver->response_rate = $responseRate;
    
    return view('driver.profile.private', compact('user', 'driver', 'stats'));
}
}