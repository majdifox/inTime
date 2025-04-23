<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Driver;
use App\Models\Passenger;
use App\Models\Ride;
use App\Models\Vehicle;
use App\Models\VehicleFeature;
use App\Models\Review;
use App\Models\FareSetting;
use App\Models\RideRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Display admin dashboard with enhanced statistics
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
            'today_rides' => Ride::whereDate('created_at', Carbon::today())->count(),
            'today_income' => Ride::whereDate('created_at', Carbon::today())
                                 ->where('ride_status', 'completed')
                                 ->sum('ride_cost'),
            'active_rides' => Ride::where('ride_status', 'ongoing')
                                 ->whereNull('dropoff_time')
                                 ->count()
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

        // Get driver statistics
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
            'online_drivers' => User::where('role', 'driver')
                                   ->where('is_online', true)
                                   ->count(),
            'onboarded_today' => User::where('role', 'driver')
                                    ->whereDate('created_at', Carbon::today())
                                    ->count()
        ];
        
        // Calculate driver availability
        $driverStats['on_ride_drivers'] = Driver::whereHas('rides', function($query) {
            $query->where('ride_status', 'ongoing')
                 ->whereNull('dropoff_time');
        })->count();
        
        $driverStats['available_drivers'] = $driverStats['online_drivers'] - $driverStats['on_ride_drivers'];
        
        // Get pending rides
        $pendingRides = Ride::where('reservation_status', 'pending')->count();
        
        // Get recent activity for dashboard
        $recentActivity = $this->getRecentActivity();
        
        return view('admin.dashboard', compact(
            'stats', 
            'percentChanges', 
            'driverStats', 
            'pendingRides',
            'recentActivity'
        ));
    }

    /**
     * Display user management page with tabs for drivers and passengers
     * 
     * @return \Illuminate\Http\Response
     */
    public function usersManagement(Request $request)
    {
        $tab = $request->get('tab', 'drivers');
        $search = $request->get('search', '');
        $status = $request->get('status', '');
        
        // Get counts for tabs
        $driversCount = User::where('role', 'driver')->count();
        $passengersCount = User::where('role', 'passenger')->count();
        $pendingCount = User::where('account_status', 'pending')->count();
        $suspendedCount = User::where('account_status', 'suspended')->count();
        
        // Load appropriate users based on tab
        if ($tab === 'drivers') {
            $users = $this->getDriversQuery($search, $status)
                          ->orderBy('created_at', 'desc')
                          ->paginate(10);
            
            return view('admin.users_drivers', compact(
                'users', 
                'tab',
                'search',
                'status',
                'driversCount',
                'passengersCount',
                'pendingCount',
                'suspendedCount'
            ));
        } elseif ($tab === 'passengers') {
            $users = $this->getPassengersQuery($search, $status)
                          ->orderBy('created_at', 'desc')
                          ->paginate(10);
            
            return view('admin.users_passengers', compact(
                'users', 
                'tab',
                'search',
                'status',
                'driversCount',
                'passengersCount',
                'pendingCount',
                'suspendedCount'
            ));
        } elseif ($tab === 'pending') {
            $users = User::where('account_status', 'pending')
                         ->with(['driver.vehicle.features'])
                         ->when(!empty($search), function($query) use ($search) {
                             $query->where(function($q) use ($search) {
                                 $q->where('name', 'like', "%{$search}%")
                                   ->orWhere('email', 'like', "%{$search}%")
                                   ->orWhere('phone', 'like', "%{$search}%");
                             });
                         })
                         ->orderBy('created_at', 'desc')
                         ->paginate(10);
            
            return view('admin.users_pending', compact(
                'users', 
                'tab',
                'search',
                'status',
                'driversCount',
                'passengersCount',
                'pendingCount',
                'suspendedCount'
            ));
        } elseif ($tab === 'suspended') {
            $users = User::where('account_status', 'suspended')
                         ->with(['driver.vehicle'])
                         ->when(!empty($search), function($query) use ($search) {
                             $query->where(function($q) use ($search) {
                                 $q->where('name', 'like', "%{$search}%")
                                   ->orWhere('email', 'like', "%{$search}%")
                                   ->orWhere('phone', 'like', "%{$search}%");
                             });
                         })
                         ->orderBy('created_at', 'desc')
                         ->paginate(10);
            
            return view('admin.users_suspended', compact(
                'users', 
                'tab',
                'search',
                'status',
                'driversCount',
                'passengersCount',
                'pendingCount',
                'suspendedCount'
            ));
        }
    }

    /**
     * Show detailed information about a specific driver
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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
            
            // Get driver's ride history
            $rideHistory = Ride::where('driver_id', $driver->driver->id ?? 0)
                            ->with('passenger.user')
                            ->orderBy('created_at', 'desc')
                            ->take(10)
                            ->get();
            
            // Count total earnings and reviews
            $totalEarnings = Ride::where('driver_id', $driver->driver->id ?? 0)
                                ->where('ride_status', 'completed')
                                ->sum('ride_cost');
            
            $reviewsCount = Review::where('reviewed_id', $driver->id)->count();
            
            // Return view with all driver data
            return view('admin.driver_details', compact(
                'driver', 
                'allFeatures',
                'rideHistory',
                'totalEarnings',
                'reviewsCount'
            ));
        } catch (\Exception $e) {
            \Log::error('Error showing driver details: ' . $e->getMessage());
            \Log::error('Error trace: ' . $e->getTraceAsString());
            
            return redirect()->route('admin.users')
                ->with('error', 'Error loading driver details: ' . $e->getMessage());
        }
    }

    /**
     * Show pending driver applications with document verification functionality
     */
    public function driverVerifications()
    {
        try {
            // Get all pending driver applications with their documents
            $pendingDrivers = User::where('role', 'driver')
                                ->where('account_status', 'pending')
                                ->with(['driver' => function($query) {
                                    $query->with(['vehicle.features']);
                                }])
                                ->orderBy('created_at', 'desc')
                                ->paginate(10);
            
            // Document types for filtering
            $documentTypes = [
                'license' => 'License',
                'insurance' => 'Insurance',
                'vehicle' => 'Vehicle Registration',
                'good_conduct' => 'Good Conduct Certificate'
            ];
            
            return view('admin.driver_verifications', compact('pendingDrivers', 'documentTypes'));
        } catch (\Exception $e) {
            \Log::error('Error loading driver verifications: ' . $e->getMessage());
            
            return redirect()->route('admin.dashboard')
                ->with('error', 'Error loading driver verifications: ' . $e->getMessage());
        }
    }

    /**
     * Process driver verification (approve/reject)
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function processDriverVerification(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'action' => 'required|in:approve,reject',
                'verification_notes' => 'nullable|string|max:500',
            ]);
            
            $driver = User::where('role', 'driver')
                        ->where('account_status', 'pending')
                        ->with('driver')
                        ->findOrFail($id);
            
            if ($validated['action'] === 'approve') {
                // Update user account status
                $driver->account_status = 'activated';
                $driver->save();
                
                // Mark driver as verified
                if ($driver->driver) {
                    $driver->driver->is_verified = true;
                    $driver->driver->verification_notes = $validated['verification_notes'];
                    $driver->driver->save();
                }
                
                // TODO: Send approval notification
                
                return redirect()->back()->with('success', 'Driver application approved successfully.');
            } else {
                // Reject the application
                $driver->account_status = 'deactivated';
                $driver->save();
                
                if ($driver->driver) {
                    $driver->driver->is_verified = false;
                    $driver->driver->verification_notes = $validated['verification_notes'];
                    $driver->driver->save();
                }
                
                // TODO: Send rejection notification
                
                return redirect()->back()->with('success', 'Driver application rejected.');
            }
        } catch (\Exception $e) {
            \Log::error('Error processing driver verification: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error processing driver verification: ' . $e->getMessage());
        }
    }

    /**
     * Verify a specific document type for a driver
     *
     * @param  Request  $request
     * @param  int  $driverId
     * @return \Illuminate\Http\Response
     */
    public function verifyDocument(Request $request, $driverId)
    {
        try {
            $validated = $request->validate([
                'document_type' => 'required|in:license,insurance,vehicle,good_conduct',
                'status' => 'required|in:verified,rejected',
                'notes' => 'nullable|string|max:255',
            ]);
            
            $driverModel = Driver::findOrFail($driverId);
            
            // Update verification status based on document type
            switch ($validated['document_type']) {
                case 'license':
                    $driverModel->license_verified = ($validated['status'] === 'verified');
                    $driverModel->license_verification_notes = $validated['notes'];
                    break;
                    
                case 'insurance':
                    $driverModel->insurance_verified = ($validated['status'] === 'verified');
                    $driverModel->insurance_verification_notes = $validated['notes'];
                    break;
                    
                case 'vehicle':
                    if ($driverModel->vehicle) {
                        $driverModel->vehicle->registration_verified = ($validated['status'] === 'verified');
                        $driverModel->vehicle->verification_notes = $validated['notes'];
                        $driverModel->vehicle->save();
                    }
                    break;
                    
                case 'good_conduct':
                    $driverModel->good_conduct_verified = ($validated['status'] === 'verified');
                    $driverModel->good_conduct_verification_notes = $validated['notes'];
                    break;
            }
            
            $driverModel->save();
            
            // Check if all documents are verified, and if so, automatically verify the driver
            $allVerified = $driverModel->license_verified && 
                          ($driverModel->insurance_verified || !$driverModel->insurance_document) && 
                          (!$driverModel->vehicle || $driverModel->vehicle->registration_verified) && 
                          ($driverModel->good_conduct_verified || !$driverModel->good_conduct_certificate);
            
            if ($allVerified) {
                $driverModel->is_verified = true;
                $driverModel->save();
                
                // Update user account status if all documents are verified
                $user = User::find($driverModel->user_id);
                if ($user && $user->account_status === 'pending') {
                    $user->account_status = 'activated';
                    $user->save();
                }
                
                return redirect()->back()->with('success', 'Document verified and driver automatically approved.');
            }
            
            return redirect()->back()->with('success', 'Document verification status updated.');
        } catch (\Exception $e) {
            \Log::error('Error verifying document: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error verifying document: ' . $e->getMessage());
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
            $user->save();
            
            // Special handling for driver verification
            if ($status === 'activated' && $user->role === 'driver' && $user->driver) {
                $user->driver->is_verified = true;
                $user->driver->save();
            } elseif (in_array($status, ['deactivated', 'suspended']) && $user->role === 'driver' && $user->driver) {
                $user->is_online = false;
                $user->save();
            }
            
            // Activity logging
            $action = ucfirst($status) . ' user account';
            $this->logAdminActivity($action, 'Changed status of ' . $user->name . ' to ' . $status);
            
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
            
            // Activity logging
            $this->logAdminActivity('Delete user', 'Permanently deleted user: ' . $user->name);
            
            $user->delete();
            
            return redirect()->route('admin.users')->with('success', 'User deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting user: ' . $e->getMessage());
        }
    }

    /**
     * Display driver document viewer for verification
     *
     * @param  int  $id
     * @param  string  $document
     * @return \Illuminate\Http\Response
     */
    public function viewDriverDocument($id, $document)
    {
        try {
            $driver = Driver::with('user')->findOrFail($id);
            
            $documentPath = null;
            $documentType = null;
            
            switch ($document) {
                case 'license':
                    $documentPath = $driver->license_photo;
                    $documentType = 'Driver License';
                    $isVerified = $driver->license_verified;
                    break;
                    
                case 'insurance':
                    $documentPath = $driver->insurance_document;
                    $documentType = 'Insurance Document';
                    $isVerified = $driver->insurance_verified;
                    break;
                    
                case 'good_conduct':
                    $documentPath = $driver->good_conduct_certificate;
                    $documentType = 'Good Conduct Certificate';
                    $isVerified = $driver->good_conduct_verified;
                    break;
                    
                case 'vehicle':
                    if ($driver->vehicle) {
                        $documentPath = $driver->vehicle->vehicle_photo;
                        $documentType = 'Vehicle Photo';
                        $isVerified = $driver->vehicle->registration_verified;
                    }
                    break;
                    
                default:
                    return redirect()->back()->with('error', 'Invalid document type');
            }
            
            if (!$documentPath) {
                return redirect()->back()->with('error', 'Document not found');
            }
            
            return view('admin.document_viewer', compact('driver', 'documentPath', 'documentType', 'document', 'isVerified'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error viewing document: ' . $e->getMessage());
        }
    }

    /**
     * Show ride management page with filters
     */
    public function ridesManagement(Request $request)
    {
        $status = $request->get('status', 'all');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $searchTerm = $request->get('search');
        
        $ridesQuery = Ride::with(['passenger.user', 'driver.user', 'driver.vehicle']);
        
        // Apply status filter
        if ($status !== 'all') {
            if ($status === 'ongoing') {
                $ridesQuery->where('ride_status', 'ongoing');
            } elseif ($status === 'completed') {
                $ridesQuery->where('ride_status', 'completed');
            } elseif ($status === 'cancelled') {
                $ridesQuery->where('reservation_status', 'cancelled');
            } elseif ($status === 'pending') {
                $ridesQuery->where('reservation_status', 'pending');
            }
        }
        
        // Apply date range filter
        if ($dateFrom) {
            $ridesQuery->whereDate('created_at', '>=', $dateFrom);
        }
        
        if ($dateTo) {
            $ridesQuery->whereDate('created_at', '<=', $dateTo);
        }
        
        // Apply search filter
        if ($searchTerm) {
            $ridesQuery->where(function($query) use ($searchTerm) {
                $query->whereHas('passenger.user', function($q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%")
                      ->orWhere('email', 'like', "%{$searchTerm}%")
                      ->orWhere('phone', 'like', "%{$searchTerm}%");
                })->orWhereHas('driver.user', function($q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%")
                      ->orWhere('email', 'like', "%{$searchTerm}%")
                      ->orWhere('phone', 'like', "%{$searchTerm}%");
                });
            });
        }
        
        // Order by latest
        $ridesQuery->orderBy('created_at', 'desc');
        
        // Paginate results
        $rides = $ridesQuery->paginate(15);
        
        // Get summary statistics
        $stats = [
            'total' => Ride::count(),
            'completed' => Ride::where('ride_status', 'completed')->count(),
            'ongoing' => Ride::where('ride_status', 'ongoing')->count(),
            'cancelled' => Ride::where('reservation_status', 'cancelled')->count(),
            'pending' => Ride::where('reservation_status', 'pending')->count(),
        ];
        
        return view('admin.rides_management', compact(
            'rides', 
            'status', 
            'dateFrom', 
            'dateTo', 
            'searchTerm', 
            'stats'
        ));
    }

    /**
     * Show detailed ride information
     */
    public function rideDetails($id)
    {
        try {
            $ride = Ride::with([
                'passenger.user',
                'driver.user',
                'driver.vehicle',
                'reviews'
            ])->findOrFail($id);
            
            // Get previous and next ride IDs for navigation
            $previousRide = Ride::where('id', '<', $id)
                              ->orderBy('id', 'desc')
                              ->first();
                              
            $nextRide = Ride::where('id', '>', $id)
                          ->orderBy('id', 'asc')
                          ->first();
            
            return view('admin.ride_details', compact('ride', 'previousRide', 'nextRide'));
        } catch (\Exception $e) {
            return redirect()->route('admin.rides.management')
                ->with('error', 'Error loading ride details: ' . $e->getMessage());
        }
    }

    /**
     * Show earnings and financial reports
     */
    public function earningsManagement(Request $request)
    {
        $period = $request->get('period', 'week');
        $customDateFrom = $request->get('date_from');
        $customDateTo = $request->get('date_to');
        
        // Determine date range based on period
        $dateFrom = null;
        $dateTo = Carbon::now();
        
        switch ($period) {
            case 'today':
                $dateFrom = Carbon::today();
                break;
            case 'week':
                $dateFrom = Carbon::now()->startOfWeek();
                break;
            case 'month':
                $dateFrom = Carbon::now()->startOfMonth();
                break;
            case 'year':
                $dateFrom = Carbon::now()->startOfYear();
                break;
            case 'custom':
                $dateFrom = $customDateFrom ? Carbon::parse($customDateFrom) : Carbon::now()->subMonth();
                $dateTo = $customDateTo ? Carbon::parse($customDateTo) : Carbon::now();
                break;
        }
        
        // Calculate earnings
        $earnings = $this->calculateEarnings($dateFrom, $dateTo);
        
        // Get top earning drivers
        $topDrivers = $this->getTopEarningDrivers($dateFrom, $dateTo, 10);
        
        // Get earnings by vehicle type
        $earningsByVehicleType = $this->getEarningsByVehicleType($dateFrom, $dateTo);
        
        // Get daily earnings for chart
        $dailyEarnings = $this->getDailyEarnings($dateFrom, $dateTo);
        
        return view('admin.earnings_management', compact(
            'earnings',
            'topDrivers',
            'earningsByVehicleType',
            'dailyEarnings',
            'period',
            'customDateFrom',
            'customDateTo'
        ));
    }

    /**
     * Show system settings page
     */
    public function settings()
    {
        // Get current fare settings
        $fareSettings = FareSetting::all();
        
        // Get other system settings (can be expanded)
        $systemSettings = [
            'platform_fee' => '15%',
            'cancellation_fee' => 'DH 30.00',
            'min_driver_rating' => '4.0',
            'surge_threshold' => '80%'
        ];
        
        return view('admin.settings', compact('fareSettings', 'systemSettings'));
    }

    /**
     * Update fare settings
     */
    public function updateFareSettings(Request $request)
    {
        try {
            $validated = $request->validate([
                'vehicle_type' => 'required|array',
                'vehicle_type.*' => 'required|string',
                'base_fare' => 'required|array',
                'base_fare.*' => 'required|numeric|min:0',
                'per_km_price' => 'required|array',
                'per_km_price.*' => 'required|numeric|min:0',
                'per_minute_price' => 'required|array',
                'per_minute_price.*' => 'required|numeric|min:0',
                'minimum_fare' => 'required|array',
                'minimum_fare.*' => 'required|numeric|min:0',
            ]);
            
            foreach ($validated['vehicle_type'] as $index => $type) {
                FareSetting::updateOrCreate(
                    ['vehicle_type' => $type],
                    [
                        'base_fare' => $validated['base_fare'][$index],
                        'per_km_price' => $validated['per_km_price'][$index],
                        'per_minute_price' => $validated['per_minute_price'][$index],
                        'minimum_fare' => $validated['minimum_fare'][$index],
                    ]
                );
            }
            
            // Log activity
            $this->logAdminActivity('Update Fare Settings', 'Updated fare settings for ' . count($validated['vehicle_type']) . ' vehicle types');
            
            return redirect()->back()->with('success', 'Fare settings updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error updating fare settings: ' . $e->getMessage());
        }
    }

    /**
     * Generate system reports
     */
    public function reports(Request $request)
    {
        $reportType = $request->get('type', 'rides');
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        
        switch ($reportType) {
            case 'rides':
                $reportData = $this->generateRidesReport($dateFrom, $dateTo);
                break;
                
            case 'drivers':
                $reportData = $this->generateDriversReport($dateFrom, $dateTo);
                break;
                
            case 'earnings':
                $reportData = $this->generateEarningsReport($dateFrom, $dateTo);
                break;
                
            case 'passengers':
                $reportData = $this->generatePassengersReport($dateFrom, $dateTo);
                break;
                
            default:
                $reportData = [];
        }
        
        return view('admin.reports', compact('reportType', 'dateFrom', 'dateTo', 'reportData'));
    }

    /**
     * Export report to CSV
     */
    public function exportReport(Request $request)
    {
        $reportType = $request->get('type', 'rides');
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        
        $fileName = $reportType . '_report_' . date('Y-m-d') . '.csv';
        
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];
        
        $callback = function() use ($reportType, $dateFrom, $dateTo) {
            $file = fopen('php://output', 'w');
            
            switch ($reportType) {
                case 'rides':
                    $this->writeRidesReportToCSV($file, $dateFrom, $dateTo);
                    break;
                    
                case 'drivers':
                    $this->writeDriversReportToCSV($file, $dateFrom, $dateTo);
                    break;
                    
                case 'earnings':
                    $this->writeEarningsReportToCSV($file, $dateFrom, $dateTo);
                    break;
                    
                case 'passengers':
                    $this->writePassengersReportToCSV($file, $dateFrom, $dateTo);
                    break;
            }
            
            fclose($file);
        };
        
        // Log activity
        $this->logAdminActivity('Export Report', 'Exported ' . $reportType . ' report for period ' . $dateFrom . ' to ' . $dateTo);
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show admin activity log
     */
    public function activityLog(Request $request)
    {
        $activities = DB::table('admin_activity_log')
                       ->orderBy('created_at', 'desc')
                       ->paginate(20);
                       
        return view('admin.activity_log', compact('activities'));
    }

    /**
     * Reset user password
     */
    public function resetUserPassword(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'new_password' => 'required|min:8',
            ]);
            
            $user = User::findOrFail($id);
            $user->password = Hash::make($validated['new_password']);
            $user->save();
            
            // Log activity
            $this->logAdminActivity('Reset Password', 'Reset password for user ' . $user->name);
            
            return redirect()->back()->with('success', 'Password reset successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error resetting password: ' . $e->getMessage());
        }
    }

    /**
     * Show drivers map with real-time locations
     */
    public function driversMap()
    {
        $onlineDrivers = Driver::with(['user', 'driverLocation', 'vehicle'])
                           ->whereHas('user', function($q) {
                               $q->where('is_online', true)
                                 ->where('account_status', 'activated');
                           })
                           ->whereHas('driverLocation', function($q) {
                               $q->where('last_updated', '>', now()->subMinutes(15));
                           })
                           ->get();
        
        return view('admin.drivers_map', compact('onlineDrivers'));
    }

    /**
     * Process driver payout request
     */
    public function processDriverPayout(Request $request, $driverId)
    {
        try {
            $validated = $request->validate([
                'amount' => 'required|numeric|min:1',
                'notes' => 'nullable|string|max:255',
            ]);
            
            $driver = Driver::findOrFail($driverId);
            
            // Verify driver has sufficient balance
            if ($driver->balance < $validated['amount']) {
                return redirect()->back()->with('error', 'Driver has insufficient balance for this payout.');
            }
            
            // Record the payout
            DB::table('driver_payouts')->insert([
                'driver_id' => $driverId,
                'amount' => $validated['amount'],
                'status' => 'completed',
                'notes' => $validated['notes'],
                'processed_by' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Deduct from driver balance
            $driver->balance -= $validated['amount'];
            $driver->save();
            
            // Log activity
            $this->logAdminActivity('Driver Payout', 'Processed payout of DH ' . $validated['amount'] . ' for driver ' . $driver->user->name);
            
            return redirect()->back()->with('success', 'Payout processed successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error processing payout: ' . $e->getMessage());
        }
    }

    /**
     * View all driver payouts
     */
    public function driverPayouts(Request $request)
    {
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $driverId = $request->get('driver_id');
        
        $payoutsQuery = DB::table('driver_payouts')
                         ->join('drivers', 'driver_payouts.driver_id', '=', 'drivers.id')
                         ->join('users as drivers_users', 'drivers.user_id', '=', 'drivers_users.id')
                         ->join('users as admin_users', 'driver_payouts.processed_by', '=', 'admin_users.id')
                         ->select(
                             'driver_payouts.*',
                             'drivers_users.name as driver_name',
                             'admin_users.name as admin_name'
                         );
        
        // Apply filters
        if ($dateFrom) {
            $payoutsQuery->whereDate('driver_payouts.created_at', '>=', $dateFrom);
        }
        
        if ($dateTo) {
            $payoutsQuery->whereDate('driver_payouts.created_at', '<=', $dateTo);
        }
        
        if ($driverId) {
            $payoutsQuery->where('driver_payouts.driver_id', $driverId);
        }
        
        $payouts = $payoutsQuery->orderBy('driver_payouts.created_at', 'desc')
                               ->paginate(15);
        
        // Get all drivers for filter dropdown
        $drivers = Driver::with('user')
                      ->whereHas('user', function($q) {
                          $q->where('account_status', 'activated');
                      })
                      ->get()
                      ->map(function($driver) {
                          return [
                              'id' => $driver->id,
                              'name' => $driver->user->name,
                              'balance' => $driver->balance
                          ];
                      });
        
        return view('admin.driver_payouts', compact('payouts', 'drivers', 'dateFrom', 'dateTo', 'driverId'));
    }

    /* ==================== HELPER METHODS ==================== */

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
     * Get recent activity for dashboard
     */
    private function getRecentActivity()
    {
        // Get recently completed rides
        $recentRides = Ride::with(['passenger.user', 'driver.user'])
                         ->where('ride_status', 'completed')
                         ->orderBy('dropoff_time', 'desc')
                         ->take(5)
                         ->get();
        
        // Get recently registered drivers
        $recentDrivers = User::where('role', 'driver')
                          ->orderBy('created_at', 'desc')
                          ->take(5)
                          ->get();
        
        // Get recent reviews
        $recentReviews = Review::with(['ride', 'reviewer', 'reviewed'])
                            ->orderBy('created_at', 'desc')
                            ->take(5)
                            ->get();
        
        return [
            'rides' => $recentRides,
            'drivers' => $recentDrivers,
            'reviews' => $recentReviews
        ];
    }

    /**
     * Get drivers query with filters
     */
    private function getDriversQuery($search = '', $status = '')
    {
        $query = User::where('role', 'driver')
                   ->with(['driver' => function($query) {
                       $query->with('vehicle');
                   }]);
        
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        if (!empty($status)) {
            $query->where('account_status', $status);
        }
        
        return $query;
    }

    /**
     * Get passengers query with filters
     */
    private function getPassengersQuery($search = '', $status = '')
    {
        $query = User::where('role', 'passenger')
                   ->with('passenger');
        
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        if (!empty($status)) {
            $query->where('account_status', $status);
        }
        
        return $query;
    }

    /**
     * Log admin activity
     */
    private function logAdminActivity($action, $details = null)
    {
        DB::table('admin_activity_log')->insert([
            'admin_id' => Auth::id(),
            'admin_name' => Auth::user()->name,
            'action' => $action,
            'details' => $details,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Calculate earnings within a date range
     */
    private function calculateEarnings($dateFrom, $dateTo)
    {
        $completedRides = Ride::where('ride_status', 'completed')
                            ->whereBetween('created_at', [$dateFrom, $dateTo])
                            ->get();
        
        $totalEarnings = $completedRides->sum('ride_cost');
        $platformFee = $totalEarnings * 0.15; // Assuming 15% platform fee
        $driverPayouts = $totalEarnings - $platformFee;
        
        $avgFarePerRide = $completedRides->count() > 0
            ? $totalEarnings / $completedRides->count()
            : 0;
            
        return [
            'total_earnings' => round($totalEarnings, 2),
            'platform_fee' => round($platformFee, 2),
            'driver_payouts' => round($driverPayouts, 2),
            'rides_count' => $completedRides->count(),
            'avg_fare' => round($avgFarePerRide, 2),
        ];
    }

    /**
     * Get top earning drivers
     */
    private function getTopEarningDrivers($dateFrom, $dateTo, $limit = 10)
    {
        return Ride::where('ride_status', 'completed')
                 ->whereBetween('created_at', [$dateFrom, $dateTo])
                 ->select('driver_id', DB::raw('SUM(ride_cost) as total_earnings'), DB::raw('COUNT(*) as rides_count'))
                 ->groupBy('driver_id')
                 ->orderBy('total_earnings', 'desc')
                 ->take($limit)
                 ->with(['driver.user'])
                 ->get()
                 ->map(function($ride) {
                     return [
                         'driver_id' => $ride->driver_id,
                         'driver_name' => $ride->driver->user->name ?? 'Unknown',
                         'profile_picture' => $ride->driver->user->profile_picture ?? null,
                         'total_earnings' => round($ride->total_earnings, 2),
                         'rides_count' => $ride->rides_count,
                         'avg_per_ride' => round($ride->total_earnings / $ride->rides_count, 2)
                     ];
                 });
    }

    /**
     * Get earnings by vehicle type
     */
    private function getEarningsByVehicleType($dateFrom, $dateTo)
    {
        return Ride::where('ride_status', 'completed')
                 ->whereBetween('created_at', [$dateFrom, $dateTo])
                 ->select('vehicle_type', DB::raw('SUM(ride_cost) as total_earnings'), DB::raw('COUNT(*) as rides_count'))
                 ->groupBy('vehicle_type')
                 ->orderBy('total_earnings', 'desc')
                 ->get()
                 ->map(function($ride) {
                     return [
                         'vehicle_type' => ucfirst($ride->vehicle_type ?? 'Unknown'),
                         'total_earnings' => round($ride->total_earnings, 2),
                         'rides_count' => $ride->rides_count,
                         'avg_per_ride' => round($ride->total_earnings / $ride->rides_count, 2)
                     ];
                 });
    }

    /**
     * Get daily earnings for chart
     */
    private function getDailyEarnings($dateFrom, $dateTo)
    {
        return Ride::where('ride_status', 'completed')
                 ->whereBetween('created_at', [$dateFrom, $dateTo])
                 ->select(
                     DB::raw('DATE(created_at) as date'),
                     DB::raw('SUM(ride_cost) as total_earnings'),
                     DB::raw('COUNT(*) as rides_count')
                 )
                 ->groupBy('date')
                 ->orderBy('date', 'asc')
                 ->get()
                 ->map(function($item) {
                     return [
                         'date' => $item->date,
                         'earnings' => round($item->total_earnings, 2),
                         'rides' => $item->rides_count
                     ];
                 });
    }

    /**
     * Generate rides report
     */
    private function generateRidesReport($dateFrom, $dateTo)
    {
        $rides = Ride::whereBetween('created_at', [$dateFrom, $dateTo])
                    ->with(['passenger.user', 'driver.user'])
                    ->get();
        
        $summary = [
            'total_rides' => $rides->count(),
            'completed_rides' => $rides->where('ride_status', 'completed')->count(),
            'cancelled_rides' => $rides->where('reservation_status', 'cancelled')->count(),
            'total_earnings' => $rides->where('ride_status', 'completed')->sum('ride_cost'),
            'avg_distance' => $rides->where('ride_status', 'completed')->avg('distance_in_km'),
            'avg_rating' => Review::whereBetween('created_at', [$dateFrom, $dateTo])->avg('rating') ?? 0
        ];
        
        // Group by vehicle type
        $byVehicleType = $rides->groupBy('vehicle_type')
                              ->map(function($typeRides, $type) {
                                  return [
                                      'type' => ucfirst($type ?? 'Unknown'),
                                      'count' => $typeRides->count(),
                                      'earnings' => $typeRides->where('ride_status', 'completed')->sum('ride_cost'),
                                      'avg_price' => $typeRides->where('ride_status', 'completed')->avg('ride_cost') ?? 0
                                  ];
                              })
                              ->values();
        
        // Daily breakdown
        $dailyBreakdown = $rides->groupBy(function($ride) {
                                    return $ride->created_at->format('Y-m-d');
                                })
                                ->map(function($dayRides, $day) {
                                    return [
                                        'date' => $day,
                                        'total' => $dayRides->count(),
                                        'completed' => $dayRides->where('ride_status', 'completed')->count(),
                                        'cancelled' => $dayRides->where('reservation_status', 'cancelled')->count(),
                                        'earnings' => $dayRides->where('ride_status', 'completed')->sum('ride_cost')
                                    ];
                                })
                                ->values();
        
        return [
            'summary' => $summary,
            'by_vehicle_type' => $byVehicleType,
            'daily_breakdown' => $dailyBreakdown,
            'date_range' => [$dateFrom, $dateTo]
        ];
    }

    /**
     * Generate drivers report
     */
    private function generateDriversReport($dateFrom, $dateTo)
    {
        $drivers = Driver::with(['user', 'vehicle'])
                      ->whereHas('user', function($q) {
                          $q->where('role', 'driver');
                      })
                      ->get();
        
        $activeDrivers = $drivers->filter(function($driver) {
            return $driver->user && $driver->user->account_status === 'activated';
        });
        
        $rides = Ride::whereBetween('created_at', [$dateFrom, $dateTo])
                    ->get();
        
        // Summary stats
        $summary = [
            'total_drivers' => $drivers->count(),
            'active_drivers' => $activeDrivers->count(),
            'pending_drivers' => $drivers->filter(function($driver) {
                return $driver->user && $driver->user->account_status === 'pending';
            })->count(),
            'new_drivers' => User::where('role', 'driver')
                               ->whereBetween('created_at', [$dateFrom, $dateTo])
                               ->count(),
            'avg_driver_rating' => $activeDrivers->avg('rating') ?? 0,
            'total_driver_earnings' => $rides->where('ride_status', 'completed')->sum('ride_cost') * 0.85 // Assuming 15% platform fee
        ];
        
        // Top drivers by rides
        $driverRides = [];
        foreach ($drivers as $driver) {
            $driverRides[$driver->id] = [
                'id' => $driver->id,
                'name' => $driver->user->name ?? 'Unknown',
                'rides' => 0,
                'earnings' => 0,
                'rating' => $driver->rating ?? 0,
                'status' => $driver->user->account_status ?? 'unknown'
            ];
        }
        
        foreach ($rides->where('ride_status', 'completed') as $ride) {
            if (isset($driverRides[$ride->driver_id])) {
                $driverRides[$ride->driver_id]['rides']++;
                $driverRides[$ride->driver_id]['earnings'] += $ride->ride_cost * 0.85; // Assuming 15% platform fee
            }
        }
        
        $topDriversByRides = collect($driverRides)
                               ->sortByDesc('rides')
                               ->take(10)
                               ->values();
        
        $topDriversByEarnings = collect($driverRides)
                                 ->sortByDesc('earnings')
                                 ->take(10)
                                 ->values();
        
        // Vehicle type distribution
        $vehicleDistribution = $drivers->groupBy(function($driver) {
                                   return $driver->vehicle ? $driver->vehicle->type : 'Unknown';
                               })
                               ->map(function($typeDrivers, $type) {
                                   return [
                                       'type' => ucfirst($type),
                                       'count' => $typeDrivers->count(),
                                       'percentage' => round($typeDrivers->count() / max(1, count($typeDrivers)) * 100, 1)
                                   ];
                               })
                               ->values();
        
        return [
            'summary' => $summary,
            'top_drivers_by_rides' => $topDriversByRides,
            'top_drivers_by_earnings' => $topDriversByEarnings,
            'vehicle_distribution' => $vehicleDistribution,
            'date_range' => [$dateFrom, $dateTo]
        ];
    }

    /**
     * Write rides report to CSV
     */
    private function writeRidesReportToCSV($file, $dateFrom, $dateTo)
    {
        // Write header
        fputcsv($file, [
            'Ride ID',
            'Date',
            'Passenger',
            'Driver',
            'From',
            'To',
            'Status',
            'Distance (km)',
            'Duration (min)',
            'Price',
            'Vehicle Type',
            'Rating'
        ]);
        
        // Get rides data
        $rides = Ride::whereBetween('created_at', [$dateFrom, $dateTo])
                    ->with(['passenger.user', 'driver.user', 'reviews'])
                    ->orderBy('created_at', 'desc')
                    ->get();
        
        // Write data rows
        foreach ($rides as $ride) {
            $passengerName = $ride->passenger && $ride->passenger->user ? $ride->passenger->user->name : 'Unknown';
            $driverName = $ride->driver && $ride->driver->user ? $ride->driver->user->name : 'Unknown';
            
            $status = $ride->ride_status === 'completed' ? 'Completed' : 
                     ($ride->reservation_status === 'cancelled' ? 'Cancelled' : 'Ongoing');
            
            $duration = null;
            if ($ride->pickup_time && $ride->dropoff_time) {
                $duration = Carbon::parse($ride->pickup_time)->diffInMinutes(Carbon::parse($ride->dropoff_time));
            }
            
            $rating = $ride->reviews->avg('rating') ?? 'N/A';
            
            fputcsv($file, [
                $ride->id,
                $ride->created_at->format('Y-m-d H:i:s'),
                $passengerName,
                $driverName,
                $ride->pickup_location,
                $ride->dropoff_location,
                $status,
                $ride->distance_in_km ?? 'N/A',
                $duration ?? 'N/A',
                $ride->ride_cost,
                ucfirst($ride->vehicle_type ?? 'Unknown'),
                $rating
            ]);
        }
    }

    /**
     * Get daily earnings for the last X days
     */
    private function getDailyEarningsForLastDays($days = 30)
    {
        $startDate = Carbon::now()->subDays($days);
        $endDate = Carbon::now();
        
        $dailyEarnings = Ride::where('ride_status', 'completed')
                           ->whereDate('created_at', '>=', $startDate)
                           ->whereDate('created_at', '<=', $endDate)
                           ->select(
                               DB::raw('DATE(created_at) as date'),
                               DB::raw('SUM(ride_cost) as total'),
                               DB::raw('COUNT(*) as rides')
                           )
                           ->groupBy('date')
                           ->orderBy('date', 'asc')
                           ->get();
        
        // Fill in any missing dates with zero values
        $result = [];
        $currentDate = $startDate->copy();
        
        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            $dayData = $dailyEarnings->firstWhere('date', $dateStr);
            
            $result[] = [
                'date' => $dateStr,
                'total' => $dayData ? $dayData->total : 0,
                'rides' => $dayData ? $dayData->rides : 0
            ];
            
            $currentDate->addDay();
        }
        
        return $result;
    }
}