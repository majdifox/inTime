<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Passenger;
use App\Models\Ride;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PassengerProfileController extends Controller
{
   /**
    * Display the passenger's public profile
    * 
    * @param int $id Passenger ID
    * @return \Illuminate\View\View
    */
   public function show($id)
   {
       $passenger = Passenger::with('user')->findOrFail($id);
       
       // Get completed rides
       $completedRides = Ride::where('passenger_id', $passenger->id)
           ->where('ride_status', 'completed')
           ->count();
           
       // Get reviews left by drivers for the passenger
       $reviews = Review::whereHas('ride', function($query) use ($passenger) {
               $query->where('passenger_id', $passenger->id);
           })
           ->where('reviewed_id', $passenger->user_id)
           ->with(['reviewer', 'ride'])
           ->orderBy('created_at', 'desc')
           ->paginate(5);
           
       // Get ratings statistics
       $averageRating = Review::where('reviewed_id', $passenger->user_id)->avg('rating') ?? 0;
       $ratingsCount = Review::where('reviewed_id', $passenger->user_id)->count();
       
       // Get breakdown of ratings (how many 5 stars, 4 stars, etc.)
       $ratingsBreakdown = [
           5 => Review::where('reviewed_id', $passenger->user_id)->where('rating', 5)->count(),
           4 => Review::where('reviewed_id', $passenger->user_id)->where('rating', 4)->count(),
           3 => Review::where('reviewed_id', $passenger->user_id)->where('rating', 3)->count(),
           2 => Review::where('reviewed_id', $passenger->user_id)->where('rating', 2)->count(),
           1 => Review::where('reviewed_id', $passenger->user_id)->where('rating', 1)->count(),
       ];
       
       // Get passenger's favorite ride types if available
       $favoriteVehicleTypes = [];
       $vehicleTypeCounts = Ride::where('passenger_id', $passenger->id)
           ->where('ride_status', 'completed')
           ->select('vehicle_type', DB::raw('count(*) as count'))
           ->groupBy('vehicle_type')
           ->orderBy('count', 'desc')
           ->take(3) // Limit to top 3 most used vehicle types
           ->get();
           
       if ($vehicleTypeCounts->count() > 0) {
           $favoriteVehicleTypes = $vehicleTypeCounts->toArray();
       }
       
       // Check if the current user can see private information
       $canViewPrivate = Auth::check() && (
           Auth::id() === $passenger->user_id || 
           Auth::user()->role === 'admin'
       );
       
       return view('passenger.profile.public', compact(
           'passenger', 
           'completedRides', 
           'reviews', 
           'averageRating', 
           'ratingsCount',
           'ratingsBreakdown',
           'favoriteVehicleTypes',
           'canViewPrivate'
       ));
   }
    
    /**
     * Display the passenger's private profile (only for the passenger themselves)
     * 
     * @return \Illuminate\View\View
     */
    public function privateProfile()
    {
        $passenger = Passenger::where('user_id', Auth::id())->firstOrFail();
        
        // Get ride preferences
        $ridePreferences = $passenger->ride_preferences ?? [];
        
        // Get saved locations
        $savedLocations = [];
        if ($passenger->preferences && isset($passenger->preferences['favorite_locations'])) {
            $savedLocations = $passenger->preferences['favorite_locations'];
        }
        
        // Get ride statistics
        $rideStats = [
            'total' => Ride::where('passenger_id', $passenger->id)->count(),
            'completed' => Ride::where('passenger_id', $passenger->id)
                ->where('ride_status', 'completed')
                ->count(),
            'cancelled' => Ride::where('passenger_id', $passenger->id)
                ->where('reservation_status', 'cancelled')
                ->count(),
        ];
        
        // Get recent rides
        $recentRides = Ride::with(['driver.user', 'driver.vehicle'])
            ->where('passenger_id', $passenger->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        return view('passenger.profile.private', compact(
            'passenger',
            'ridePreferences',
            'savedLocations',
            'rideStats',
            'recentRides'
        ));
    }
    
    /**
     * Update passenger preferences
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePreferences(Request $request)
    {
        $validated = $request->validate([
            'vehicle_features' => 'nullable|array',
            'women_only_rides' => 'nullable|boolean',
            'preferred_vehicle_type' => 'nullable|string|in:basic,comfort,black,wav',
            'preferred_payment_method' => 'nullable|string',
            'auto_match' => 'nullable|boolean',
        ]);
        
        $passenger = Passenger::where('user_id', Auth::id())->firstOrFail();
        $user = Auth::user();
        
        // Initialize ride_preferences if it doesn't exist
        if (!$passenger->ride_preferences) {
            $passenger->ride_preferences = [];
        }
        
        // Update ride preferences
        foreach ($validated as $key => $value) {
            if ($value !== null) {
                $passenger->ride_preferences[$key] = $value;
            }
        }
        
        // Specifically handle women_only_rides in the user model as well
        if (isset($validated['women_only_rides'])) {
            $user->women_only_rides = $validated['women_only_rides'];
            $user->save();
        }
        
        $passenger->save();
        
        return back()->with('success', 'Preferences updated successfully');
    }
    
    /**
     * Add a favorite location
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addFavoriteLocation(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'address' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'type' => 'required|in:home,work,other'
        ]);
        
        $passenger = Passenger::where('user_id', Auth::id())->firstOrFail();
        $passenger->saveFavoriteLocation($validated);
        
        return back()->with('success', 'Location added to favorites');
    }
    
    /**
     * Remove a favorite location
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeFavoriteLocation(Request $request)
    {
        $validated = $request->validate([
            'location_id' => 'required|string'
        ]);
        
        $passenger = Passenger::where('user_id', Auth::id())->firstOrFail();
        
        // Get current preferences
        $preferences = $passenger->preferences ?? [];
        
        // Remove the specified location
        if (isset($preferences['favorite_locations'])) {
            foreach ($preferences['favorite_locations'] as $index => $location) {
                if (isset($location['id']) && $location['id'] === $validated['location_id']) {
                    unset($preferences['favorite_locations'][$index]);
                    break;
                }
            }
            // Reindex the array
            $preferences['favorite_locations'] = array_values($preferences['favorite_locations']);
            
            // Save updated preferences
            $passenger->preferences = $preferences;
            $passenger->save();
            
            return back()->with('success', 'Location removed from favorites');
        }
        
        return back()->with('error', 'Location not found');
    }
}