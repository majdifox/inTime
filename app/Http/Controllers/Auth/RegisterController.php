<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'birthday' => 'nullable|date',
            'gender' => 'required|in:male,female',
            'role' => 'required|in:driver,passenger'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Handle profile picture upload
        $profilePicturePath = null;
        if ($request->hasFile('profile_picture')) {
            $profilePicturePath = $request->file('profile_picture')->store('profile_pictures', 'public');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'profile_picture' => $profilePicturePath,
            'birthday' => $request->birthday,
            'gender' => $request->gender,
            'role' => $request->role,
            'account_status' => 'activated',
        ]);

        event(new Registered($user));

        auth()->login($user);

        // Redirect based on role
        if ($user->role === 'driver') {
            return redirect()->route('driver.dashboard');
        } else if ($user->role === 'passenger') {
            return redirect()->route('passenger.dashboard');
        } else {
            return redirect()->route('home');
        }
    }
}