<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;


class Driver
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is a driver
        if(Auth::user()->role != 'driver') {
            return redirect()->route('home');
        }
        
        // Check account status
        $status = Auth::user()->account_status;
        
        // If deactivated, allow them to access registration, otherwise redirect to under review
        if ($status === 'deactivated') {
            // Allow access to registration page and store route
            if ($request->routeIs('driverRegistration.create') || $request->routeIs('driver.store')) {
                return $next($request);
            }
            // Redirect to driver registration if they try to access any other page
            return redirect()->route('driverRegistration.create');
        }
        
        // If pending/under review, only allow them to see the "under review" page
        if ($status === 'pending') {
            // Allow access to under review page
            if ($request->routeIs('driver.under.review')) {
                return $next($request);
            }
            // Redirect to under review page
            return redirect()->route('driver.under.review');
        }
        
        // If suspended or deleted, show appropriate message
        if (in_array($status, ['suspended', 'deleted'])) {
            return redirect()->route('home')->with('error', 'Your account has been ' . $status . '. Please contact support for assistance.');
        }
        
        // If activated, allow access to all pages
        if ($status === 'activated') {
            return $next($request);
        }
        
        // Fallback for any other statuses
        return redirect()->route('home');
    }
}
