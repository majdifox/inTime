<!-- passenger/preferences.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>inTime - Ride Preferences</title>
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <!-- Header/Navigation -->
    <header class="px-4 py-4 flex items-center justify-between bg-white shadow-sm">
        <div class="flex items-center space-x-8">
            <!-- Logo -->
            <a href="{{ route('passenger.dashboard') }}" class="text-2xl font-bold">inTime</a>
            
            <!-- Navigation Links -->
            <nav class="hidden md:flex space-x-6">
                <a href="{{ route('passenger.history') }}" class="font-medium">Ride History</a>
                <a href="{{ route('profile.edit') }}" class="font-medium">My Profile</a>
            </nav>
        </div>
        
        <div class="flex justify-center space-x-4">
            <a href="{{ route('passenger.dashboard') }}" class="bg-black text-white py-2 px-6 rounded-md font-medium hover:bg-gray-800 transition">
                Back to Dashboard
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="bg-red-500 text-white py-2 px-6 rounded-md font-medium hover:bg-red-600 transition">
                    {{ __('Log Out') }}
                </button>
            </form>
        </div>
        
        <div class="h-10 w-10 rounded-full bg-gray-300 overflow-hidden">
            @if(Auth::user()->profile_picture)
                <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Profile" class="h-full w-full object-cover">
            @else
                <img src="/api/placeholder/40/40" alt="Profile" class="h-full w-full object-cover">
            @endif
        </div>
    </header>

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
            <!-- Left Column - Ride Preferences -->
            <div class="w-full lg:w-1/2 flex flex-col gap-6">
                <!-- Ride Preferences Card -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold mb-6">Ride Preferences</h2>
                    
                    <form action="{{ route('passenger.save.preferences') }}" method="POST" id="preferences-form">
                        @csrf
                        
                        <div class="space-y-6">
                            <!-- Vehicle Type Preference -->
                            <div>
                                <label for="vehicle_type" class="block text-sm font-medium text-gray-700 mb-1">Preferred Vehicle Type</label>
                                <select id="vehicle_type" name="preferences[vehicle_type]" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-black focus:border-black sm:text-sm">
                                    <option value="any" {{ isset($passenger->preferences['vehicle_type']) && $passenger->preferences['vehicle_type'] == 'any' ? 'selected' : '' }}>Any</option>
                                    <option value="share" {{ isset($passenger->preferences['vehicle_type']) && $passenger->preferences['vehicle_type'] == 'share' ? 'selected' : '' }}>Share</option>
                                    <option value="comfort" {{ isset($passenger->preferences['vehicle_type']) && $passenger->preferences['vehicle_type'] == 'comfort' ? 'selected' : '' }}>Comfort</option>
                                    <option value="women" {{ isset($passenger->preferences['vehicle_type']) && $passenger->preferences['vehicle_type'] == 'women' ? 'selected' : '' }}>Women-Only</option>
                                    <option value="wav" {{ isset($passenger->preferences['vehicle_type']) && $passenger->preferences['vehicle_type'] == 'wav' ? 'selected' : '' }}>Wheelchair Accessible</option>
                                    <option value="black" {{ isset($passenger->preferences['vehicle_type']) && $passenger->preferences['vehicle_type'] == 'black' ? 'selected' : '' }}>Black (Premium)</option>
                                </select>
                            </div>
                            
                            <!-- Women-Only Rides Toggle -->
                            @if(Auth::user()->gender === 'female')
                            <div class="border-t border-gray-200 pt-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900">Women-Only Rides</h3>
                                        <p class="text-sm text-gray-500 mt-1">When enabled, you'll only see female drivers who have women-only mode active</p>
                                    </div>
                                    <button type="button" id="women-only-toggle" class="relative inline-flex h-6 w-11 items-center rounded-full {{ Auth::user()->women_only_rides ? 'bg-pink-500' : 'bg-gray-300' }} transition-colors duration-300">
                                        <span id="women-only-circle" class="inline-block h-5 w-5 transform rounded-full bg-white shadow-md transition-transform duration-300 {{ Auth::user()->women_only_rides ? 'translate-x-5' : 'translate-x-1' }}"></span>
                                    </button>
                                    <input type="hidden" name="preferences[women_only_rides]" id="women-only-input" value="{{ Auth::user()->women_only_rides ? '1' : '0' }}">
                                </div>
                                
                                <div class="mt-4 bg-pink-50 p-4 rounded-md">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-pink-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-pink-800">Women-Only Mode Information</h3>
                                            <div class="mt-2 text-sm text-pink-700">
                                                <ul class="list-disc pl-5 space-y-1">
                                                    <li>Only female drivers with women-only mode enabled will be visible</li>
                                                    <li>Drivers will have a pink icon next to their profile</li>
                                                    <li>This feature is designed to provide an additional layer of security</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            <!-- Ride Feature Preferences -->
                            <div class="border-t border-gray-200 pt-4">
                                <h3 class="text-lg font-medium text-gray-900 mb-3">Ride Features</h3>
                                
                                <div class="space-y-4">
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="quiet_ride" name="preferences[quiet_ride]" type="checkbox" 
                                                class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                                                {{ isset($passenger->ride_preferences['quiet_ride']) && $passenger->ride_preferences['quiet_ride'] ? 'checked' : '' }}>
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="quiet_ride" class="font-medium text-gray-700">Quiet Ride</label>
                                            <p class="text-gray-500">Prefer minimal conversation during your ride</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="temperature_control" name="preferences[temperature_control]" type="checkbox" 
                                                class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                                                {{ isset($passenger->ride_preferences['temperature_control']) && $passenger->ride_preferences['temperature_control'] ? 'checked' : '' }}>
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="temperature_control" class="font-medium text-gray-700">Temperature Control</label>
                                            <p class="text-gray-500">Let the driver know you prefer to control the AC/heating</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="luggage_space" name="preferences[luggage_space]" type="checkbox" 
                                                class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                                                {{ isset($passenger->ride_preferences['luggage_space']) && $passenger->ride_preferences['luggage_space'] ? 'checked' : '' }}>
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="luggage_space" class="font-medium text-gray-700">Extra Luggage Space</label>
                                            <p class="text-gray-500">You typically have luggage or large items with you</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Accessibility Needs -->
                            <div class="border-t border-gray-200 pt-4">
                                <h3 class="text-lg font-medium text-gray-900 mb-3">Accessibility Needs</h3>
                                
                                <div class="space-y-4">
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="wheelchair_accessible" name="preferences[accessibility_needs]" type="checkbox" 
                                                class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                                                {{ isset($passenger->preferences['accessibility_needs']) && $passenger->preferences['accessibility_needs'] ? 'checked' : '' }}>
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="wheelchair_accessible" class="font-medium text-gray-700">Wheelchair Accessible Vehicle</label>
                                            <p class="text-gray-500">You'll be matched with wheelchair-accessible vehicles when available</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="assistance_required" name="preferences[assistance_required]" type="checkbox" 
                                                class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                                                {{ isset($passenger->ride_preferences['assistance_required']) && $passenger->ride_preferences['assistance_required'] ? 'checked' : '' }}>
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="assistance_required" class="font-medium text-gray-700">Driver Assistance Required</label>
                                            <p class="text-gray-500">You need the driver to assist with entry/exit or loading</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="pt-4">
                                <button type="submit" class="w-full bg-black text-white py-3 px-4 rounded-md font-medium hover:bg-gray-800 transition">
                                    Save Preferences
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Right Column - Saved Locations -->
            <div class="w-full lg:w-1/2 flex flex-col gap-6">
                <!-- Saved Locations Card -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold">Saved Locations</h2>
                        <button type="button" id="add-location-btn" class="text-blue-600 text-sm font-medium hover:text-blue-800 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            Add New Location
                        </button>
                    </div>
                    
                    <!-- Saved Locations List -->
                    <div class="space-y-4">
                        @if(isset($passenger->preferences['favorite_locations']) && count($passenger->preferences['favorite_locations']) > 0)
                            @foreach($passenger->preferences['favorite_locations'] as $location)
                                <div class="border rounded-md p-4 hover:border-blue-300 transition-colors">
                                    <div class="flex justify-between">
                                        <div class="flex items-center">
                                            <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                                @if($location['type'] === 'home')
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                                                    </svg>
                                                @elseif($location['type'] === 'work')
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd" />
                                                        <path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z" />
                                                    </svg>
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                                    </svg>
                                                @endif
                                            </div>
                                            <div>
                                                <h3 class="font-medium">{{ $location['name'] }}</h3>
                                                <p class="text-sm text-gray-500">{{ ucfirst($location['type']) }}</p>
                                            </div>
                                        </div>
                                        <button type="button" class="text-gray-400 hover:text-gray-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                            </svg>
                                        </button>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-2">{{ $location['address'] }}</p>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-8 bg-gray-50 rounded-md">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <p class="text-gray-500 font-medium">No saved locations yet</p>
                                <p class="text-sm text-gray-400 mt-1">Add your frequently visited places for faster booking</p>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Add Location Form (Hidden by default) -->
                <div id="add-location-form" class="bg-white rounded-lg shadow-md p-6 hidden">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold">Add New Location</h2>
                        <button type="button" id="close-location-form" class="text-gray-400 hover:text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    
                    <form action="{{ route('passenger.save.location') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="location_name" class="block text-sm font-medium text-gray-700 mb-1">Location Name</label>
                            <input type="text" id="location_name" name="name" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-black focus:border-black sm:text-sm" placeholder="E.g., Home, Office, Gym">
                        </div>
                        
                        <div>
                            <label for="location_type" class="block text-sm font-medium text-gray-700 mb-1">Location Type</label>
                            <select id="location_type" name="type" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-black focus:border-black sm:text-sm">
                                <option value="home">Home</option>
                                <option value="work">Work</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="location_address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <input type="text" id="location_address" name="address" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-black focus:border-black sm:text-sm" placeholder="Enter an address">
                        </div>
                        
                        <!-- Hidden fields for coordinates -->
                        <input type="hidden" id="location_latitude" name="latitude" value="0">
                        <input type="hidden" id="location_longitude" name="longitude" value="0">
                        
                        <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md font-medium hover:bg-blue-700 transition">
                            Save Location
                        </button>
                    </form>
                </div>
                
                <!-- Account Information Card -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold">Account Information</h2>
                        <a href="{{ route('profile.edit') }}" class="text-blue-600 text-sm font-medium hover:text-blue-800">
                            Edit Profile
                        </a>
                    </div>
                    
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="h-16 w-16 rounded-full bg-gray-300 overflow-hidden">
                            @if(Auth::user()->profile_picture)
                                <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Profile" class="h-full w-full object-cover">
                            @else
                                <div class="h-full w-full flex items-center justify-center bg-gray-400 text-white">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <h3 class="font-medium text-lg">{{ Auth::user()->name }}</h3>
                            <p class="text-gray-600">{{ Auth::user()->email }}</p>
                            <p class="text-sm text-gray-500">{{ Auth::user()->phone }}</p>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Gender</span>
                            <span class="font-medium">{{ ucfirst(Auth::user()->gender) }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Total Rides</span>
                            <span class="font-medium">{{ $passenger->total_rides ?? 0 }}</span>
                        </div>
                        
                        @if(isset($passenger->rating) && $passenger->rating > 0)
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Rating</span>
                            <div class="flex items-center">
                                <span class="font-medium mr-1">{{ number_format($passenger->rating, 1) }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- JavaScript for functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle women-only mode
            const womenOnlyToggle = document.getElementById('women-only-toggle');
            const womenOnlyCircle = document.getElementById('women-only-circle');
            const womenOnlyInput = document.getElementById('women-only-input');
            
            if (womenOnlyToggle) {
                womenOnlyToggle.addEventListener('click', function() {
                    const isCurrentlyEnabled = womenOnlyToggle.classList.contains('bg-pink-500');
                    const newState = !isCurrentlyEnabled;
                    
                    // Update UI
                    womenOnlyCircle.classList.toggle('translate-x-1', !newState);
                    womenOnlyCircle.classList.toggle('translate-x-5', newState);
                    womenOnlyToggle.classList.toggle('bg-gray-300', !newState);
                    womenOnlyToggle.classList.toggle('bg-pink-500', newState);
                    
                    // Update form value
                    womenOnlyInput.value = newState ? '1' : '0';
                });
            }
            
            // Location form toggle
            const addLocationBtn = document.getElementById('add-location-btn');
            const addLocationForm = document.getElementById('add-location-form');
            const closeLocationForm = document.getElementById('close-location-form');
            
            if (addLocationBtn && addLocationForm) {
                addLocationBtn.addEventListener('click', function() {
                    addLocationForm.classList.remove('hidden');
                });
            }
            
            if (closeLocationForm && addLocationForm) {
                closeLocationForm.addEventListener('click', function() {
                    addLocationForm.classList.add('hidden');
                });
            }
            
            // Simple address to coordinates geocoding (using Nominatim for demo purposes)
            const locationAddress = document.getElementById('location_address');
            const locationLatitude = document.getElementById('location_latitude');
            const locationLongitude = document.getElementById('location_longitude');
            
            if (locationAddress) {
                locationAddress.addEventListener('blur', function() {
                    const address = this.value;
                    if (address.trim() === '') return;
                    
                    // In a real application, you would use a geocoding service
                    // This is a simplified example using OpenStreetMap's Nominatim
                    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data && data.length > 0) {
                                const result = data[0];
                                locationLatitude.value = result.lat;
                                locationLongitude.value = result.lon;
                            }
                        })
                        .catch(error => {
                            console.error('Error geocoding address:', error);
                        });
                });
            }
        });
    </script>
</body>
</html>