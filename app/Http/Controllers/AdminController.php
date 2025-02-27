<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Driver;
use App\Models\Passenger;
use App\Models\Ride;
use App\Models\Vehicle;
use App\Models\vehicle_features;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Display admin dashboard
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        // Get statistics for ride summary
        $stats = [
            'total_completed' => Ride::where('ride_status', 'completed')->count(),
            'cancelled_rides' => Ride::where('reservation_status', 'cancelled')->count(),
            'general_income' => Ride::where('ride_status', 'completed')->sum('ride_cost'),
            'average_rating' => DB::table('reviews')->avg('rating') ?? 0,
        ];

        // Calculate percentage changes from last month
        $lastMonthStats = $this->getLastMonthStats();
        
        $percentChanges = [
            'total_completed_change' => $this->calculatePercentChange(
                $lastMonthStats['total_completed'], 
                $stats['total_completed']
            ),
            'cancelled_rides_change' => $this->calculatePercentChange(
                $lastMonthStats['cancelled_rides'], 
                $stats['cancelled_rides'],
                true // Invert for cancelled rides (decrease is good)
            ),
            'general_income_change' => $this->calculatePercentChange(
                $lastMonthStats['general_income'], 
                $stats['general_income']
            ),
            'average_rating_change' => number_format($stats['average_rating'] - ($lastMonthStats['average_rating'] ?? 0), 1),
        ];

        // Get supervision data
        $onlineDrivers = User::where('role', 'driver')
                            ->where('is_online', true)
                            ->count();
        
        $onRideDrivers = Driver::whereHas('rides', function($query) {
                            $query->where('ride_status', 'ongoing');
                        })->count();
        
        $availableDrivers = $onlineDrivers - $onRideDrivers;
        
        $driverStats = [
            'total_drivers' => User::where('role', 'driver')->count(),
            'active_drivers' => User::where('role', 'driver')
                                    ->where('account_status', 'activated')
                                    ->count(),
            'pending_drivers' => User::where('role', 'driver')
                                    ->where('account_status', 'pending')
                                    ->count(),
            'suspended_drivers' => User::where('role', 'driver')
                                    ->where('account_status', 'suspended')
                                    ->count(),
            'online_drivers' => $onlineDrivers,
            'available_drivers' => $availableDrivers,
            'on_ride_drivers' => $onRideDrivers,
        ];
        
        $pendingRides = Ride::where('reservation_status', 'pending')->count();
        
        return view('admin.dashboard', compact('stats', 'percentChanges', 'driverStats', 'pendingRides'));
    }

    public function showDriverDetails($id)
{
    try {
        // Get the driver with all related information using eager loading
        $driver = User::where('role', 'driver')
                    ->with(['driver' => function($query) {
                        $query->with(['vehicle.features']);
                    }])
                    ->findOrFail($id);
        
        // Get a list of all features for display
        $allFeatures = [
            'ac' => 'Air Conditioning',
            'wifi' => 'WiFi',
            'child_seat' => 'Child Seat',
            'usb_charger' => 'USB Charger',
            'pet_friendly' => 'Pet Friendly',
            'luggage_carrier' => 'Luggage Carrier'
        ];
        
        // Return view with driver data
        return view('admin.driver_details', compact('driver', 'allFeatures'));
    } catch (\Exception $e) {
        \Log::error('Error showing driver details: ' . $e->getMessage());
        \Log::error('Error trace: ' . $e->getTraceAsString());
        
        return redirect()->route('admin.users')
            ->with('error', 'Error loading driver details: ' . $e->getMessage());
    }
}
/**
 * Update user status directly (not AJAX)
 *
 * @param  int  $id
 * @param  string  $status
 * @return \Illuminate\Http\Response
 */
public function updateUserStatusDirect($id, $status)
{
    try {
        // Validate the status
        if (!in_array($status, ['activated', 'deactivated', 'pending', 'suspended', 'deleted'])) {
            return redirect()->back()->with('error', 'Invalid status specified');
        }
        
        $user = User::findOrFail($id);
        $user->account_status = $status;
        
        // If user is a driver and status is 'activated', also set is_verified to true
        if ($user->role == 'driver' && $status == 'activated' && $user->driver) {
            $user->driver->is_verified = true;
            $user->driver->save();
        }
        
        $user->save();
        
        return redirect()->back()->with('success', 'User status updated successfully');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Error updating user status: ' . $e->getMessage());
    }
}

/**
 * Delete user directly (not AJAX)
 *
 * @param  int  $id
 * @return \Illuminate\Http\Response
 */
public function deleteUserDirect($id)
{
    try {
        $user = User::findOrFail($id);
        
        // Delete profile picture if exists
        if ($user->profile_picture) {
            Storage::delete($user->profile_picture);
        }
        
        // Delete related records based on role
        if ($user->role == 'driver') {
            $driver = $user->driver;
            
            if ($driver) {
                // Delete driver documents
                if ($driver->license_photo) {
                    Storage::delete($driver->license_photo);
                }
                if ($driver->insurance_document) {
                    Storage::delete($driver->insurance_document);
                }
                if ($driver->good_conduct_certificate) {
                    Storage::delete($driver->good_conduct_certificate);
                }
                
                // Delete vehicle photos if exists
                $vehicle = $driver->vehicle;
                if ($vehicle && $vehicle->vehicle_photo) {
                    Storage::delete($vehicle->vehicle_photo);
                }
            }
        }
        
        $user->delete();
        
        return redirect()->route('admin.users')->with('success', 'User deleted successfully');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Error deleting user: ' . $e->getMessage());
    }
}

    /**
     * Show users management page
     *
     * @return \Illuminate\Http\Response
     */
    public function usersManagement()
    {
        // Get the first few drivers for initial display
        $drivers = User::where('role', 'driver')
                      ->with(['driver' => function($query) {
                          $query->with('vehicle');
                      }])
                      ->orderBy('created_at', 'desc')
                      ->paginate(10);
        
        // Get the first few passengers for initial display
        $passengers = User::where('role', 'passenger')
                         ->with('passenger')
                         ->orderBy('created_at', 'desc')
                         ->paginate(10);
        
        return view('admin.users', compact('drivers', 'passengers'));
    }

    
    /**
     * Get drivers list (for AJAX)
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getDrivers(Request $request)
{
    try {
        $query = User::where('role', 'driver')
                    ->with(['driver' => function($query) {
                        $query->with('vehicle');
                    }]);
        
        // Apply search if provided
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        // Apply status filter if provided
        if ($request->has('status') && !empty($request->status)) {
            $query->where('account_status', $request->status);
        }
        
        // Apply sorting
        $sortField = $request->get('sort_field', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);
        
        $drivers = $query->paginate(10);
        
        if ($request->ajax()) {
            $view = view('admin.partials.drivers_table', compact('drivers'))->render();
            $pagination = view('admin.partials.pagination', ['paginator' => $drivers])->render();
            
            return response()->json([
                'success' => true,
                'html' => $view,
                'pagination' => $pagination
            ]);
        }
        
        return view('admin.users', compact('drivers'));
    } catch (\Exception $e) {
        \Log::error('Error fetching drivers: ' . $e->getMessage());
        
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading drivers: ' . $e->getMessage(),
                'html' => '<tr><td colspan="9" class="px-6 py-4 text-center text-red-500">Error loading data</td></tr>',
                'pagination' => ''
            ], 500);
        }
        
        return redirect()->back()->with('error', 'Error loading drivers: ' . $e->getMessage());
    }
}

    /**
     * Get passengers list (for AJAX)
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    /**
 * Get passengers list (for AJAX)
 *
 * @param Request $request
 * @return \Illuminate\Http\Response
 */
public function getPassengers(Request $request)
{
    try {
        $query = User::where('role', 'passenger')
                    ->with('passenger');
        
        // Apply search if provided
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        // Apply status filter if provided
        if ($request->has('status') && !empty($request->status)) {
            $query->where('account_status', $request->status);
        }
        
        // Apply sorting
        $sortField = $request->get('sort_field', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);
        
        $passengers = $query->paginate(10);
        
        if ($request->ajax()) {
            $view = view('admin.partials.passengers_table', compact('passengers'))->render();
            $pagination = view('admin.partials.pagination', ['paginator' => $passengers])->render();
            
            return response()->json([
                'success' => true,
                'html' => $view,
                'pagination' => $pagination
            ]);
        }
        
        return view('admin.users', compact('passengers'));
    } catch (\Exception $e) {
        \Log::error('Error fetching passengers: ' . $e->getMessage());
        
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading passengers: ' . $e->getMessage(),
                'html' => '<tr><td colspan="9" class="px-6 py-4 text-center text-red-500">Error loading data</td></tr>',
                'pagination' => ''
            ], 500);
        }
        
        return redirect()->back()->with('error', 'Error loading passengers: ' . $e->getMessage());
    }
}

    /**
     * Get driver details (for AJAX modal)
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getDriverDetails($id)
{
    try {
        // Use eager loading to get all related data in one go
        $driver = User::where('role', 'driver')
                    ->with([
                        'driver' => function($query) {
                            $query->with('vehicle');
                        }
                    ])
                    ->findOrFail($id);
        
        // Log what we found for debugging
        \Log::info('Found driver user:', ['id' => $id, 'name' => $driver->name]);
        
        // Handle image paths for profile picture
        if ($driver->profile_picture) {
            if (strpos($driver->profile_picture, 'public/') === 0) {
                $driver->profile_picture = Storage::url($driver->profile_picture);
            } else if (strpos($driver->profile_picture, 'storage/') !== 0) {
                $driver->profile_picture = Storage::url($driver->profile_picture);
            }
            \Log::info('Profile picture path:', ['path' => $driver->profile_picture]);
        }
        
        // Handle image paths for license photo
        if ($driver->driver && $driver->driver->license_photo) {
            if (strpos($driver->driver->license_photo, 'public/') === 0) {
                $driver->driver->license_photo = Storage::url($driver->driver->license_photo);
            } else if (strpos($driver->driver->license_photo, 'storage/') !== 0) {
                $driver->driver->license_photo = Storage::url($driver->driver->license_photo);
            }
            \Log::info('License photo path:', ['path' => $driver->driver->license_photo]);
        }
        
        // Handle image paths for vehicle photo
        if ($driver->driver && $driver->driver->vehicle && $driver->driver->vehicle->vehicle_photo) {
            if (strpos($driver->driver->vehicle->vehicle_photo, 'public/') === 0) {
                $driver->driver->vehicle->vehicle_photo = Storage::url($driver->driver->vehicle->vehicle_photo);
            } else if (strpos($driver->driver->vehicle->vehicle_photo, 'storage/') !== 0) {
                $driver->driver->vehicle->vehicle_photo = Storage::url($driver->driver->vehicle->vehicle_photo);
            }
            \Log::info('Vehicle photo path:', ['path' => $driver->driver->vehicle->vehicle_photo]);
        }
        
        \Log::info('Full driver data:', ['data' => $driver->toArray()]);
        
        return response()->json([
            'success' => true,
            'driver' => $driver
        ]);
    } catch (\Exception $e) {
        \Log::error('Error fetching driver details: ' . $e->getMessage());
        \Log::error('Error trace: ' . $e->getTraceAsString());
        
        return response()->json([
            'success' => false,
            'message' => 'Error fetching driver details: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Get passenger details (for AJAX modal)
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getPassengerDetails($id)
{
    try {
        $passenger = User::where('role', 'passenger')
                         ->with('passenger')
                         ->findOrFail($id);
        
        // Ensure we're returning proper image paths
        if ($passenger->profile_picture) {
            // Check if the path already contains 'storage/' and not 'public/'
            if (strpos($passenger->profile_picture, 'public/') === 0) {
                $passenger->profile_picture = Storage::url($passenger->profile_picture);
            } else if (strpos($passenger->profile_picture, 'storage/') !== 0) {
                $passenger->profile_picture = Storage::url($passenger->profile_picture);
            }
        }
        
        // Add default values for passenger-specific properties if they don't exist
        if (!$passenger->passenger) {
            $passenger->passenger = (object)[
                'rating' => null,
                'total_rides' => 0,
                'preferences' => []
            ];
        }
        
        return response()->json([
            'success' => true,
            'passenger' => $passenger
        ]);
    } catch (\Exception $e) {
        \Log::error('Error fetching passenger details: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Error fetching passenger details: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Update user status
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateUserStatus(Request $request, $id)
{
    try {
        $user = User::findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'required|in:activated,deactivated,suspended,deleted',
        ]);
        
        $user->account_status = $validated['status'];
        
        // If user is a driver and status is 'activated', also set is_verified to true
        if ($user->role == 'driver' && $validated['status'] == 'activated' && $user->driver) {
            $user->driver->is_verified = true;
            $user->driver->save();
        }
        
        $user->save();

        // Get the updated user data to return
        $updatedUser = User::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'message' => 'User status updated successfully',
            'user' => $updatedUser
        ]);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        \Log::error('User not found when updating status: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'User not found'
        ], 404);
    } catch (\Illuminate\Validation\ValidationException $e) {
        \Log::error('Validation error when updating user status: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Validation error',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        \Log::error('Error updating user status: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error updating user status: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Delete user
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        
        // Delete profile picture if exists
        if ($user->profile_picture) {
            Storage::delete($user->profile_picture);
        }
        
        // Delete related records based on role
        if ($user->role == 'driver') {
            $driver = $user->driver;
            
            if ($driver) {
                // Delete driver documents
                if ($driver->license_photo) {
                    Storage::delete($driver->license_photo);
                }
                if ($driver->insurance_document) {
                    Storage::delete($driver->insurance_document);
                }
                if ($driver->good_conduct_certificate) {
                    Storage::delete($driver->good_conduct_certificate);
                }
                
                // Delete vehicle photos if exists
                $vehicle = $driver->vehicle;
                if ($vehicle && $vehicle->vehicle_photo) {
                    Storage::delete($vehicle->vehicle_photo);
                }
            }
        }
        
        $user->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }

    /**
     * Helper function to get last month stats
     */
    private function getLastMonthStats()
    {
        $lastMonth = now()->subMonth();
        $startOfLastMonth = $lastMonth->copy()->startOfMonth();
        $endOfLastMonth = $lastMonth->copy()->endOfMonth();
        
        return [
            'total_completed' => Ride::where('ride_status', 'completed')
                                    ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
                                    ->count(),
            'cancelled_rides' => Ride::where('reservation_status', 'cancelled')
                                     ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
                                     ->count(),
            'general_income' => Ride::where('ride_status', 'completed')
                                    ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
                                    ->sum('ride_cost'),
            'average_rating' => DB::table('reviews')
                                  ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
                                  ->avg('rating') ?? 0,
                                  
        ];
    }

    /**
     * Helper function to calculate percent change
     */
    private function calculatePercentChange($oldValue, $newValue, $invert = false)
    {
        if ($oldValue == 0) {
            return $newValue > 0 ? 100 : 0;
        }
        
        $percentChange = (($newValue - $oldValue) / $oldValue) * 100;
        
        if ($invert) {
            $percentChange = -$percentChange;
        }
        
        return round($percentChange);
    }

    /**
     * View pending rides
     */
    public function pendingRides()
    {
        $pendingRides = Ride::where('reservation_status', 'pending')
                           ->with(['passenger.user', 'driver.user'])
                           ->orderBy('reservation_date', 'asc')
                           ->paginate(10);
        
        return view('admin.pending_rides', compact('pendingRides'));
    }
    
    /**
     * Get ride details
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getRideDetails($id)
    {
        $ride = Ride::with([
                'passenger.user',
                'driver.user',
                'driver.vehicle'
            ])
            ->findOrFail($id);
        
        return response()->json([
            'ride' => $ride
        ]);
    }
    
    /**
     * Update ride status
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateRideStatus(Request $request, $id)
    {
        $ride = Ride::findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'required|in:pending,not_accepted,accepted,cancelled',
        ]);
        
        $ride->reservation_status = $validated['status'];
        $ride->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Ride status updated successfully',
            'ride' => $ride
        ]);
    }
    /**
 * Verify a driver
 *
 * @param  int  $id
 * @return \Illuminate\Http\Response
 */
public function verifyDriver($id)
{
    try {
        $user = User::where('role', 'driver')->findOrFail($id);
        
        if (!$user->driver) {
            return redirect()->back()->with('error', 'Driver profile not found');
        }
        
        $user->driver->is_verified = true;
        $user->driver->save();
        
        // Update account status as well
        $user->account_status = 'activated';
        $user->save();
        
        return redirect()->back()->with('success', 'Driver has been verified successfully');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Error verifying driver: ' . $e->getMessage());
    }
}

/**
 * Unverify a driver
 *
 * @param  int  $id
 * @return \Illuminate\Http\Response
 */
public function unverifyDriver($id)
{
    try {
        $user = User::where('role', 'driver')->findOrFail($id);
        
        if (!$user->driver) {
            return redirect()->back()->with('error', 'Driver profile not found');
        }
        
        $user->driver->is_verified = false;
        $user->driver->save();
        
        return redirect()->back()->with('success', 'Driver verification has been revoked successfully');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Error unverifying driver: ' . $e->getMessage());
    }
}

public function showPassengerDetails($id)
{
    try {
        // Get the passenger with all related information
        $passenger = User::where('role', 'passenger')
                    ->with('passenger')
                    ->findOrFail($id);
        
        // Return view with passenger data
        return view('admin.passenger_details', compact('passenger'));
    } catch (\Exception $e) {
        \Log::error('Error showing passenger details: ' . $e->getMessage());
        \Log::error('Error trace: ' . $e->getTraceAsString());
        
        return redirect()->route('admin.users')
            ->with('error', 'Error loading passenger details: ' . $e->getMessage());
    }
}
}