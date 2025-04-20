<!-- resources/views/drivers/profile-settings.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Profile Settings | inTime Driver</title>
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <!-- Header/Navigation (same as dashboard) -->
    <!-- ... -->

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif
        
        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif
        
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Left Column - Navigation -->
            <div class="w-full lg:w-1/4">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold mb-4">Profile Settings</h2>
                    
                    <nav class="space-y-2">
                        <a href="#profile-section" class="block px-3 py-2 rounded-md bg-blue-50 text-blue-600 font-medium">
                            Driver Profile
                        </a>
                        <a href="#vehicle-section" class="block px-3 py-2 rounded-md hover:bg-gray-50 transition">
                            Vehicle Information
                        </a>
                        <a href="#password-section" class="block px-3 py-2 rounded-md hover:bg-gray-50 transition">
                            Change Password
                        </a>
                        <a href="{{ route('driver.dashboard') }}" class="block px-3 py-2 rounded-md hover:bg-gray-50 transition">
                            Back to Dashboard
                        </a>
                    </nav>
                </div>
            </div>
            
            <!-- Right Column - Settings Forms -->
            <div class="w-full lg:w-3/4">
                <!-- Driver Profile Section -->
                <div id="profile-section" class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl font-bold mb-4">Driver Profile</h2>
                    
                    <form action="{{ route('driver.profile.update') }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PATCH')
                        
                        <!-- Profile Information -->
                        <div>
                            <h3 class="text-lg font-medium mb-3">Basic Information</h3>
                            
                            <div class="space-y-4">
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                                        <div class="mt-1">
                                            <input type="text" id="name" name="name" value="{{ $user->name }}" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" disabled>
                                            <p class="mt-1 text-xs text-gray-500">To change your name, please contact support.</p>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                                        <div class="mt-1">
                                            <input type="email" id="email" name="email" value="{{ $user->email }}" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" disabled>
                                            <p class="mt-1 text-xs text-gray-500">To change your email, please contact support.</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="bio" class="block text-sm font-medium text-gray-700">Bio</label>
                                    <div class="mt-1">
                                        <textarea id="bio" name="bio" rows="4" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ $driver->bio ?? '' }}</textarea>
                                        <p class="mt-1 text-xs text-gray-500">Brief description about yourself that passengers will see on your profile (max 500 characters).</p>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Languages</label>
                                    <div class="mt-1 grid grid-cols-2 gap-4">
                                        @foreach($availableLanguages as $key => $language)
                                            <div class="flex items-start">
                                                <div class="flex items-center h-5">
                                                    <input id="language_{{ $key }}" name="languages[]" type="checkbox" value="{{ $key }}" 
                                                        {{ isset($driver->languages) && in_array($key, $driver->languages) ? 'checked' : '' }}
                                                        class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                                </div>
                                                <div class="ml-3 text-sm">
                                                    <label for="language_{{ $key }}" class="font-medium text-gray-700">{{ $language }}</label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">Select languages you speak fluently.</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Women-Only Driver Setting (for female drivers only) -->
                        @if($user->gender === 'female')
                            <div class="border-t border-gray-200 pt-6">
                                <h3 class="text-lg font-medium mb-3">Service Preferences</h3>
                                
                                <div class="space-y-4">
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="women_only_driver" name="women_only_driver" type="checkbox" value="1" 
                                                {{ $driver->women_only_driver ? 'checked' : '' }}
                                                class="focus:ring-pink-500 h-4 w-4 text-pink-600 border-gray-300 rounded">
                                        </div>
                                        <div class="ml-3">
                                            <label for="women_only_driver" class="font-medium text-gray-700">Women-Only Driver</label>
                                            <p class="text-sm text-gray-500">When enabled, you'll only receive ride requests from female passengers.</p>
                                            
                                            @if($driver->women_only_driver && $driver->vehicle && $driver->vehicle->type !== 'women')
                                                <div class="mt-2 bg-yellow-100 p-2 rounded text-sm text-yellow-700">
                                                    <span class="font-medium">Note:</span> For consistent matching, consider updating your vehicle type to "Women".
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <div class="border-t border-gray-200 pt-6">
                            <button type="submit" class="w-full md:w-auto bg-blue-600 text-white py-2 px-6 rounded-md font-medium hover:bg-blue-700 transition">
                                Save Profile Changes
                            </button>
                        </div>