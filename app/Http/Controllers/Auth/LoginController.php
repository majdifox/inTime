<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\LoginNotificationMail;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Send login notification email
            $user = Auth::user();
            Mail::to($user->email)->send(new LoginNotificationMail($user));

            // Redirect based on role
            if ($user->role === 'admin') {
                return redirect()->intended('admin/dashboard');
            } else if ($user->role === 'driver') {
                return redirect()->intended('dashboard');
            } else if ($user->role === 'passenger') {
                return redirect()->intended('passenger/dashboard');
            }

            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }
}