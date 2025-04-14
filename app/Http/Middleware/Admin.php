<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class Admin
{
   /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->role == 'admin') {
            return $next($request);
        }
        
        return redirect('/')->with('error', 'Unauthorized access. Admin privileges required.');
    }
    // public function handle(Request $request, Closure $next): Response
    // {

    //     if(Auth::user()->role != 'admin'){

    //         return redirect('admin/dashboard');
    //     }
    //     return $next($request);
    // }
}
