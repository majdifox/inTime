<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class GoogleLoginController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Find existing user or create new one
            $user = User::firstOrNew(['email' => $googleUser->email]);

            // If user doesn't exist, fill in details
            if (!$user->exists) {
                $user->name = $googleUser->name;
                $user->email = $googleUser->email;
                
                // Generate a secure, random password
                $user->password = Hash::make(Str::random(40));
                
                // Set default role
                $user->role = 'passenger';
                
                // Use a default phone number or leave null
                $user->phone = '+000000000';
                
                $user->save();
            }

            // Log in the user
            Auth::login($user);

            // Redirect to dashboard
            return redirect()->intended('/dashboard')
                ->with('success', 'Successfully logged in with Google');

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Google Login Error: ' . $e->getMessage());

            // Redirect with error message
            return redirect('/login')
                ->with('error', 'Unable to authenticate with Google. Please try again.');
        }
    }
}