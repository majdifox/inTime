<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WomenOnlyRestriction
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if the request contains a vehicle_type parameter set to 'women'
        if ($request->has('vehicle_type') && $request->input('vehicle_type') === 'women') {
            // Check if the current user is female
            if (Auth::check() && Auth::user()->gender !== 'female') {
                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => 'Only female passengers can request the "Women" vehicle type.'
                    ], 403);
                }
                
                return redirect()->back()->with('error', 'Only female passengers can request the "Women" vehicle type.');
            }
        }
        
        return $next($request);
    }
}