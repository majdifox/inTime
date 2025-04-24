<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\PassengerController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DriverProfileController;


use App\Http\Controllers\Auth\GoogleLoginController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
require __DIR__.'/auth.php';

// Default route to home controller
Route::get('/', [HomeController::class, 'index'])->name('home');

// Dashboard route for all authenticated users
Route::get('/dashboard', [HomeController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Driver routes - all protected by authentication and driver middleware
Route::middleware(['auth', 'isdriver'])->group(function () {
    // Main dashboard
    Route::get('/driver/dashboard', [DriverController::class, 'dashboard'])->name('driver.dashboard');
    
    // Online status and location updates
    Route::post('/driver/status', [DriverController::class, 'updateOnlineStatus'])->name('driver.update.status');
    Route::post('/driver/location', [DriverController::class, 'updateLocation'])->name('driver.update.location');
    
    // Women-only driver mode toggle
    Route::post('/driver/women-only-mode', [DriverController::class, 'toggleWomenOnlyMode'])->name('driver.toggle.women.only');
    
    // Ride management
    Route::get('/driver/active-rides', [DriverController::class, 'activeRides'])->name('driver.active.rides');
    Route::get('/driver/incoming-requests', [DriverController::class, 'incomingRequests'])->name('driver.incoming.requests');
    Route::post('/driver/request/{requestId}/respond', [DriverController::class, 'respondToRequest'])->name('driver.request.respond');
    
    // Legacy ride management
    Route::get('/driver/awaiting-rides', [DriverController::class, 'awaitingRides'])->name('driver.awaiting.rides');
    Route::get('/driver/attention-needed', [DriverController::class, 'attentionNeeded'])->name('driver.attention.needed');
    Route::post('/driver/ride/{id}/respond', [DriverController::class, 'respondToRideRequest'])->name('driver.ride.respond');
    
    // Ride actions
    Route::post('/driver/ride/{id}/start', [DriverController::class, 'startRide'])->name('driver.ride.start');
    Route::post('/driver/ride/{id}/complete', [DriverController::class, 'completeRide'])->name('driver.ride.complete');
    
    // Driver registration
    Route::get('/registration', [DriverController::class, 'driverRegistration'])->name('driverRegistration.create');
    Route::post('/driver/register', [DriverController::class, 'driverRegistrationStore'])->name('driver.store');
    
    // Account status
    Route::get('/under-review', [DriverController::class, 'underReview'])->name('driver.under.review');
    
    // Driver history and earnings
    Route::get('/driver/history', [DriverController::class, 'rideHistory'])->name('driver.history');
    Route::get('/driver/earnings', [DriverController::class, 'earnings'])->name('driver.earnings');
    Route::get('/driver/reviews', [DriverController::class, 'reviews'])->name('driver.reviews');
    
    // Driver profile and vehicle management
    Route::patch('/driver/profile', [DriverController::class, 'updateProfile'])->name('driver.profile.update');
    Route::patch('/driver/vehicle', [DriverController::class, 'updateVehicle'])->name('driver.vehicle.update');

    Route::get('/driver/debug/status', [DriverController::class, 'debugDriverStatus'])->name('driver.debug.status');
Route::get('/driver/status/check', [DriverController::class, 'checkOnlineStatus'])->name('driver.status.check');
Route::get('/driver/request/{requestId}/status', [DriverController::class, 'checkRequestStatus'])
    ->name('driver.request.status');
    Route::post('/driver/heartbeat', [DriverController::class, 'heartbeat'])->name('driver.heartbeat');
Route::post('/driver/set-offline', [DriverController::class, 'setOffline'])->name('driver.set.offline');
Route::post('/driver/force-location-refresh', [DriverController::class, 'forceLocationRefresh'])
    ->name('driver.force.location.refresh');

    Route::post('/driver/toggle-women-only', [DriverController::class, 'toggleWomenOnlyMode'])->name('driver.toggle.women.only');
    Route::patch('/driver/vehicle', [DriverController::class, 'updateVehicle'])->name('driver.vehicle.update');
    Route::get('/driver/profile-settings', [DriverController::class, 'profileSettings'])->name('driver.profile.settings');
    Route::patch('/driver/password', [DriverController::class, 'updatePassword'])->name('driver.password.update');
      // Private profile view
      Route::get('/driver/profile/private', [App\Http\Controllers\DriverProfileController::class, 'privateProfile'])
      ->name('driver.profile.private');
  
  // Profile-related routes (these already exist but should be reviewed)
  Route::patch('/driver/profile', [App\Http\Controllers\DriverController::class, 'updateProfile'])
      ->name('driver.profile.update');
  
  Route::patch('/driver/vehicle', [App\Http\Controllers\DriverController::class, 'updateVehicle'])
      ->name('driver.vehicle.update');
  
  Route::patch('/driver/password', [App\Http\Controllers\DriverProfileController::class, 'updatePassword'])
      ->name('driver.password.update');
  
  // Toggle women-only mode
  Route::post('/driver/toggle-women-only', [App\Http\Controllers\DriverController::class, 'toggleWomenOnlyMode'])
      ->name('driver.toggle.women.only');

// Driver ride actions
Route::post('/driver/ride/{id}/start', [DriverController::class, 'startRide'])->name('driver.ride.start');
Route::post('/driver/ride/{id}/complete', [DriverController::class, 'completeRide'])->name('driver.ride.complete');

// Driver rating system
Route::get('/driver/ride/{ride}/rate', [DriverController::class, 'rateRide'])->name('driver.rate.ride');
Route::post('/driver/ride/{ride}/submit-rating', [DriverController::class, 'submitRating'])->name('driver.submit.rating');
});


// driver public profile route
Route::get('/driver/{id}/profile', [DriverProfileController::class, 'show'])->name('driver.public.profile');
Route::get('/driver/profile/{id}', [App\Http\Controllers\DriverProfileController::class, 'show'])
    ->name('driver.public.profile');


// Passenger routes
Route::middleware(['auth', 'ispassenger'])->prefix('passenger')->name('passenger.')->group(function () {
    // Dashboard and active ride tracking
    Route::get('/dashboard', [PassengerController::class, 'index'])->name('dashboard');
    Route::get('/active-ride', [PassengerController::class, 'activeRide'])->name('active.ride');
    
    // Ride booking and matching flow
    Route::get('/book', [PassengerController::class, 'bookRide'])->name('book');
    Route::post('/calculate-options', [PassengerController::class, 'calculateRideOptions'])->name('calculate.options');
    
    // Driver selection flow (new)
    Route::get('/available-drivers', [PassengerController::class, 'getAvailableDrivers'])->name('available.drivers');
    
    // Request ride (with or without driver selection)
    Route::post('/request-ride', [PassengerController::class, 'requestRide'])->name('request.ride');
    Route::get('/matching/{ride}', [PassengerController::class, 'rideMatching'])->name('ride.matching');
    Route::get('/matching/{ride}/status', [PassengerController::class, 'checkMatchingStatus'])->name('matching.status');
    
    // Legacy driver finding flow
    Route::post('/find-drivers', [PassengerController::class, 'findDrivers'])->name('find.drivers');
    Route::post('/book-with-driver', [PassengerController::class, 'bookRideWithDriver'])->name('book.with.driver');
    
    // Ride management
    Route::post('/cancel-ride/{rideId}', [PassengerController::class, 'cancelRide'])->name('cancel.ride');
    Route::get('/history', [PassengerController::class, 'rideHistory'])->name('history');
    
    // Review system
    Route::post('/ride/{ride}/rate', [PassengerController::class, 'rateRide'])->name('rate.ride');
    
    // Passenger preferences
    Route::post('/save-location', [PassengerController::class, 'saveFavoriteLocation'])->name('save.location');
    Route::post('/save-preferences', [PassengerController::class, 'saveRidePreferences'])->name('save.preferences');

    Route::get('/debug/drivers', [PassengerController::class, 'debugAvailableDrivers'])
        ->name('debug.drivers');

    Route::get('/nearby-drivers', [PassengerController::class, 'nearbyDrivers'])
        ->name('nearby.drivers');
        Route::get('/select-driver', [PassengerController::class, 'selectDriver'])->name('select.driver');
        Route::post('/passenger/clear-session', [PassengerController::class, 'clearSessionData'])
    ->name('passenger.clear.session');
    Route::post('/save-preferences', [PassengerController::class, 'saveRidePreferences'])->name('save.preferences');
    
 // Profile routes
 Route::get('/profile/{id}', [App\Http\Controllers\PassengerProfileController::class, 'show'])
 ->name('public.profile');

Route::get('/profile', [App\Http\Controllers\PassengerProfileController::class, 'privateProfile'])
 ->name('profile.private');

Route::post('/profile/preferences', [App\Http\Controllers\PassengerProfileController::class, 'updatePreferences'])
 ->name('profile.update.preferences');

Route::post('/profile/location/add', [App\Http\Controllers\PassengerProfileController::class, 'addFavoriteLocation'])
 ->name('profile.add.location');

Route::post('/profile/location/remove', [App\Http\Controllers\PassengerProfileController::class, 'removeFavoriteLocation'])
 ->name('profile.remove.location');

 Route::post('/passenger/ride/{ride}/rate', [PassengerController::class, 'rateRide'])->name('passenger.rate.ride');
});

// Admin routes
Route::middleware(['auth', 'isadmin'])->prefix('admin')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Users Management
    Route::get('/users', [AdminController::class, 'usersManagement'])->name('admin.users');
    Route::get('/drivers', [AdminController::class, 'getDrivers'])->name('admin.drivers');
    Route::get('/passengers', [AdminController::class, 'getPassengers'])->name('admin.passengers');
    
    // API routes for AJAX
    Route::get('/driver/{id}', [AdminController::class, 'getDriverDetails'])->name('admin.driver.details');
    Route::get('/passenger/{id}', [AdminController::class, 'getPassengerDetails'])->name('admin.passenger.details');
    
    // Fare settings management
    Route::get('/fare-settings', [AdminController::class, 'fareSettings'])->name('admin.fare.settings');
    Route::post('/fare-settings', [AdminController::class, 'updateFareSettings'])->name('admin.fare.settings.update');
    
    // Status update via AJAX
    Route::patch('/user/{id}/status', [AdminController::class, 'updateUserStatus'])->name('admin.user.status');
    Route::delete('/user/{id}', [AdminController::class, 'deleteUser'])->name('admin.user.delete');

    // Direct driver details page route
    Route::get('/driver-details/{id}', [AdminController::class, 'showDriverDetails'])->name('admin.driver.show');
    
    // Action routes
    Route::get('/user/{id}/status/{status}', [AdminController::class, 'updateUserStatusDirect'])->name('admin.user.status');
    Route::get('/user/{id}/delete', [AdminController::class, 'deleteUserDirect'])->name('admin.user.delete');
    
    // Rides Management
    Route::get('/rides/pending', [AdminController::class, 'pendingRides'])->name('admin.rides.pending');
    Route::get('/rides/{id}', [AdminController::class, 'getRideDetails'])->name('admin.rides.details');
    Route::patch('/rides/{id}/status', [AdminController::class, 'updateRideStatus'])->name('admin.rides.update.status');

    Route::patch('/user/{id}/verify', [AdminController::class, 'verifyDriver'])->name('admin.user.verify');
Route::patch('/user/{id}/unverify', [AdminController::class, 'unverifyDriver'])->name('admin.user.unverify');
});


// Public driver profile routes (accessible to all users)
Route::get('/driver/{id}/profile', [DriverProfileController::class, 'show'])->name('driver.profile');

// Passenger Profile Routes
Route::get('/passenger/{id}/profile', [App\Http\Controllers\PassengerProfileController::class, 'show'])
    ->name('passenger.public.profile');
    
Route::middleware(['auth', 'ispassenger'])->group(function () {
    Route::get('/passenger/profile', [App\Http\Controllers\PassengerProfileController::class, 'privateProfile'])
        ->name('passenger.profile.private');
    
    Route::post('/passenger/profile/preferences', [App\Http\Controllers\PassengerProfileController::class, 'updatePreferences'])
        ->name('passenger.profile.update.preferences');
    
    Route::post('/passenger/profile/location/add', [App\Http\Controllers\PassengerProfileController::class, 'addFavoriteLocation'])
        ->name('passenger.profile.add.location');
    
    Route::post('/passenger/profile/location/remove', [App\Http\Controllers\PassengerProfileController::class, 'removeFavoriteLocation'])
        ->name('passenger.profile.remove.location');
});
// Google authentication
Route::get('/auth/google', [GoogleLoginController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleLoginController::class, 'handleGoogleCallback']);

// API routes for driver location
Route::middleware('auth')->prefix('api')->group(function() {
    Route::get('/driver-location/{id}', function($id) {
        $driverLocation = App\Models\DriverLocation::where('driver_id', $id)
            ->where('last_updated', '>', now()->subMinutes(5))
            ->first();
            
        if ($driverLocation) {
            return response()->json([
                'success' => true,
                'location' => $driverLocation
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Driver location not available'
        ]);
    });
});


Route::get('/api/server-time', function() {
    return response()->json([
        'server_time' => now()->toIso8601String()
    ]);
});

Route::get('/test-ride-complete', function() {
    return response()->json([
        'success' => true,
        'message' => 'Test route is working'
    ]);
});