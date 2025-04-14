<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Redirect based on user role
        $user = auth()->user();
        
        if ($user->role == 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role == 'driver') {
            return redirect()->route('driver.dashboard');
        } else {
            return redirect()->route('passenger.dashboard');
        }
    }
    
    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function adminDashboard()
    {
        // Get statistics for admin dashboard
        $totalUsers = User::count();
        $totalDrivers = User::where('role', 'driver')->count();
        $totalPassengers = User::where('role', 'passenger')->count();
  
        // Get recent users for dashboard
        $recentUsers = User::orderBy('created_at', 'desc')
                         ->take(5)
                         ->get();
        
        return view('admin.dashboard', compact(
            'totalUsers', 
            'totalDrivers', 
            'totalPassengers', 
            'recentUsers'
        ));
    }
}