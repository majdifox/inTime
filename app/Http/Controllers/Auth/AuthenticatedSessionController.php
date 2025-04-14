<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Controllers\HomeController;
use App\Controllers\driverController;
use App\Controllers\passengerController;
use Illuminate\Support\Facades\Mail;
use App\Mail\LoginNotificationMail;



class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();
    
        // Send login notification email
        $user = $request->user();
        Mail::to($user->email)->send(new LoginNotificationMail($user));
    
        
        if($user->role === 'admin'){
            return redirect('admin/dashboard');
        }
    
        if($user->role === 'driver'){
            return redirect('dashboard');
        }
    
        if($user->role === 'passenger'){
            return redirect('passenger/dashboard');
        }
    
        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}


