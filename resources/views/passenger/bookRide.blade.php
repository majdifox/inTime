
<!-- passenger/bookRide.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>inTime - Book a Ride</title>
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- OpenStreetMap with Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" 
          crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" 
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" 
            crossorigin=""></script>
    
    <!-- Add Leaflet Routing Machine for directions -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
    <script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>
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
            <!-- Left Column - Search Form -->
            <div class="w-full lg:w-1/3 flex flex-col gap-6">
                <!-- Ride Search Card -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold mb-4">Where are you going?</h2>
                    
                    <div id="search-form" class="space-y-4">
                        <div>
                            <label for="pickup_location" class="block text-sm font-medium text-gray-700 mb-1">Pickup Location</label>
                            <div class="relative">
                                <input type="text" id="pickup_location" name="pickup_location" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-black focus:border-black sm:text-sm" placeholder="Enter pickup location">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <circle cx="10" cy="10" r="8" stroke-width="1" />
                                    </svg>
                                    
                                </div>

                                 <!-- Add the locate me button -->
                                <div class="absolute inset-y-0 right-0 flex items-center">
                                    <button type="button" id="locate-me-btn" class="h-full px-2 text-gray-500 hover:text-blue-600" title="Use my current location">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                
                                <!-- Saved Locations Dropdown -->
                                @if(isset($savedLocations) && count($savedLocations) > 0)
                                <div class="absolute right-2 top-2">
                                    <button type="button" id="saved-pickup-btn" class="text-gray-500 hover:text-gray-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    <div id="saved-pickup-dropdown" class="hidden absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                        <div class="py-1" role="menu" aria-orientation="vertical">
                                            @foreach($savedLocations as $location)
                                            <button type="button" class="saved-location-item block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" 
                                                    data-address="{{ $location['address'] }}" 
                                                    data-lat="{{ $location['latitude'] }}" 
                                                    data-lng="{{ $location['longitude'] }}"
                                                    data-target="pickup">
                                                <span class="font-medium">{{ $location['name'] }}</span> - {{ $location['type'] }}
                                            </button>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                            <input type="hidden" id="pickup_latitude" name="pickup_latitude">
                            <input type="hidden" id="pickup_longitude" name="pickup_longitude">
                        </div>
                        
                        <div>
                            <label for="dropoff_location" class="block text-sm font-medium text-gray-700 mb-1">Destination</label>
                            <div class="relative">
                                <input type="text" id="dropoff_location" name="dropoff_location" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-black focus:border-black sm:text-sm" placeholder="Enter destination">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                
                                <!-- Saved Locations Dropdown -->
                                @if(isset($savedLocations) && count($savedLocations) > 0)
                                <div class="absolute right-2 top-2">
                                    <button type="button" id="saved-dropoff-btn" class="text-gray-500 hover:text-gray-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    <div id="saved-dropoff-dropdown" class="hidden absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                        <div class="py-1" role="menu" aria-orientation="vertical">
                                            @foreach($savedLocations as $location)
                                            <button type="button" class="saved-location-item block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" 
                                                    data-address="{{ $location['address'] }}" 
                                                    data-lat="{{ $location['latitude'] }}" 
                                                    data-lng="{{ $location['longitude'] }}"
                                                    data-target="dropoff">
                                                <span class="font-medium">{{ $location['name'] }}</span> - {{ $location['type'] }}
                                            </button>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                            <input type="hidden" id="dropoff_latitude" name="dropoff_latitude">
                            <input type="hidden" id="dropoff_longitude" name="dropoff_longitude">
                        </div>
                        
                        <button type="button" id="search-rides-btn" class="w-full bg-black text-white py-3 px-4 rounded-md font-medium hover:bg-gray-800 transition flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                            Find Rides
                        </button>
                    </div>
                </div>
                <!-- Vehicle Features Section -->
<div class="mb-4">
    <h4 class="text-sm font-medium text-gray-700 mb-2">Vehicle Features Needed:</h4>
    
    <div class="grid grid-cols-2 gap-2">
        <div class="flex items-start">
            <div class="flex items-center h-5">
                <input id="feature_ac" name="ride_preferences[vehicle_features][]" type="checkbox" value="ac" 
                    class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                    {{ isset($passenger->ride_preferences['vehicle_features']) && in_array('ac', $passenger->ride_preferences['vehicle_features']) ? 'checked' : '' }}>
            </div>
            <div class="ml-3 text-sm">
                <label for="feature_ac" class="font-medium text-gray-700">Air Conditioning</label>
            </div>
        </div>
        
        <!-- Add the other feature options here, similar to above -->
        
        <!-- Example for WiFi -->
        <div class="flex items-start">
            <div class="flex items-center h-5">
                <input id="feature_wifi" name="ride_preferences[vehicle_features][]" type="checkbox" value="wifi" 
                    class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                    {{ isset($passenger->ride_preferences['vehicle_features']) && in_array('wifi', $passenger->ride_preferences['vehicle_features']) ? 'checked' : '' }}>
            </div>
            <div class="ml-3 text-sm">
                <label for="feature_wifi" class="font-medium text-gray-700">WiFi</label>
            </div>
        </div>
        
        <!-- Continue with other options -->
    </div>
</div>
                
                <!-- Save Location Card (Hidden by default) -->
                <div id="save-location-card" class="bg-white rounded-lg shadow-md p-6 hidden">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold">Save Location</h2>
                        <button type="button" id="close-save-location" class="text-gray-400 hover:text-gray-500">
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
                            <input type="text" id="location_address" name="address" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-black focus:border-black sm:text-sm" readonly>
                            <input type="hidden" id="location_latitude" name="latitude">
                            <input type="hidden" id="location_longitude" name="longitude">
                        </div>
                        
                        <button type="submit" class="w-full bg-black text-white py-2 px-4 rounded-md font-medium hover:bg-gray-800 transition">
                            Save Location
                        </button>
                    </form>
                </div>
                
                <!-- Ride Options Card (Hidden by default) -->
                <div id="ride-options-card" class="bg-white rounded-lg shadow-md p-6 hidden">
                    <h2 class="text-xl font-bold mb-4">Available Rides</h2>
                    
                    <div id="ride-options-container" class="space-y-4">
                        <!-- Ride options will be populated here by JavaScript -->
                    </div>
                </div>
            </div>
            
            <!-- Right Column - Map, Distance Info -->
            <div class="w-full lg:w-2/3 flex flex-col gap-6">
                <!-- Map Container -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="h-96" id="map"></div>
                </div>
                
                <!-- Distance and Time Info (Hidden by default) -->
                <div id="route-info-card" class="bg-white rounded-lg shadow-md p-6 hidden">
                    <h2 class="text-xl font-bold mb-4">Trip Details</h2>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="border rounded-md p-4 text-center">
                            <p class="text-sm text-gray-500 mb-1">Distance</p>
                            <p class="text-lg font-bold" id="distance-value">- km</p>
                        </div>
                        
                        <div class="border rounded-md p-4 text-center">
                            <p class="text-sm text-gray-500 mb-1">Estimated Time</p>
                            <p class="text-lg font-bold" id="duration-value">- min</p>
                        </div>
                    </div>
                    
                    <div class="mt-4 space-y-3">
                        <div class="flex items-start">
                            <div class="mt-1 mr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <circle cx="12" cy="12" r="8" stroke-width="2" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium">Pickup Location</p>
                                <p class="text-sm text-gray-600" id="route-pickup-location">-</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="mt-1 mr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium">Destination</p>
                                <p class="text-sm text-gray-600" id="route-dropoff-location">-</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <button type="button" id="save-locations-btn" class="text-blue-600 text-sm font-medium hover:text-blue-800">
                            Save these locations for future rides
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>
    {{-- Add this HTML to your book ride page, after the ride options section --}}

<div class="card mt-4 mb-4" id="available-drivers-section" style="display: none;">
    <div class="card-header">
        <h5 class="mb-0">Available Drivers</h5>
    </div>
    <div class="card-body">
        <div id="available-drivers-container">
            <div class="text-center py-3">
                <p class="text-muted">Select a pickup and drop-off location to see available drivers</p>
            </div>
        </div>
    </div>
</div>

{{-- Add this hidden input to your form --}}
<input type="hidden" name="driver_id" id="selected-driver-id">
    <!-- Ride Confirmation Modal -->
    <div id="ride-confirmation-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            
            <div class="relative inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div>
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-5">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Confirm Your Ride</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Please review your ride details before confirming.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <div class="border rounded-md p-4">
                        <div class="flex items-center mb-4">
                            <div id="confirm-vehicle-icon" class="h-12 w-12 bg-gray-200 rounded-full mr-4 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m-4 4H4m0 0l4 4m-4-4l4-4" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-lg" id="confirm-vehicle-type">-</h4>
                                <p class="text-sm text-gray-600" id="confirm-vehicle-details">-</p>
                            </div>
                            <div class="ml-auto">
                                <p class="font-bold text-lg" id="confirm-fare">-</p>
                                <p class="text-xs text-gray-500" id="confirm-surge">-</p>
                            </div>
                        </div>
                        
                        <div class="space-y-3">
                            <div class="flex items-start">
                                <div class="mt-1 mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <circle cx="12" cy="12" r="8" stroke-width="2" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium">Pickup Location</p>
                                    <p class="text-sm text-gray-600" id="confirm-pickup-location">-</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="mt-1 mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium">Destination</p>
                                    <p class="text-sm text-gray-600" id="confirm-dropoff-location">-</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="mt-1 mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium">Estimated Arrival</p>
                                    <p class="text-sm text-gray-600" id="confirm-eta">-</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <form id="request-ride-form" action="{{ route('passenger.request.ride') }}" method="POST" class="mt-5">
                    @csrf
                    <input type="hidden" id="confirm-vehicle-type-input" name="vehicle_type">
                    
                    <div class="mt-4 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                        <!-- Ride Preferences Section -->
                        <div class="ride-preferences-section mt-4 border-t border-gray-200 pt-4">
                            <h3 class="text-lg font-medium mb-3">Ride Preferences</h3>
                            
                            <!-- Women-Only Preference (visible only to female passengers) -->
                            @if(Auth::user()->gender === 'female')
                            <div class="mb-4">
                                <div class="flex items-start">
                                    <div class="flex items-center">
                                        <button type="button" id="women-only-toggle" class="relative inline-flex h-6 w-11 items-center rounded-full {{ Auth::user()->women_only_rides ? 'bg-pink-500' : 'bg-gray-300' }} transition-colors duration-300">
                                            <span id="women-only-toggle-dot" class="inline-block h-5 w-5 transform rounded-full bg-white shadow-md transition-transform duration-300 {{ Auth::user()->women_only_rides ? 'translate-x-5' : 'translate-x-1' }}"></span>
                                        </button>
                                        <span id="women-only-text" class="ml-2 font-medium">Women-Only Rides {{ Auth::user()->women_only_rides ? 'On' : 'Off' }}</span>
                                    </div>
                                    
                                    @if(Auth::user()->women_only_rides)
                                        <div class="ml-auto px-2 py-1 rounded bg-pink-100 text-pink-800 text-xs">
                                            Active
                                        </div>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-500 mt-1">When enabled, you'll be matched only with female drivers for added safety and comfort.</p>
                                
                                <!-- Hidden checkbox that will be submitted with the form -->
                                <input id="women_only_rides" name="ride_preferences[women_only_rides]" type="checkbox" 
                                    class="hidden"
                                    {{ isset($passenger->ride_preferences['women_only_rides']) && $passenger->ride_preferences['women_only_rides'] ? 'checked' : '' }}
                                    {{ Auth::user()->women_only_rides ? 'checked' : '' }}>
                            </div>
                        @endif
                            
                            <!-- Other ride preferences can go here -->
                            <div class="mb-4">
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="quiet_ride" name="ride_preferences[quiet_ride]" type="checkbox" 
                                            class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                                            {{ isset($passenger->ride_preferences['quiet_ride']) && $passenger->ride_preferences['quiet_ride'] ? 'checked' : '' }}>
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="quiet_ride" class="font-medium text-gray-700">Quiet Ride</label>
                                        <p class="text-gray-500">Prefer minimal conversation during your ride.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="temperature_control" name="ride_preferences[temperature_control]" type="checkbox" 
                                            class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                                            {{ isset($passenger->ride_preferences['temperature_control']) && $passenger->ride_preferences['temperature_control'] ? 'checked' : '' }}>
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="temperature_control" class="font-medium text-gray-700">Temperature Control</label>
                                        <p class="text-gray-500">Let the driver know you prefer to control the AC/heating.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-black text-base font-medium text-white hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black sm:col-start-2 sm:text-sm">
                            Confirm Ride
                        </button>
                        <button type="button" id="select-driver-btn" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                            Select Driver
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Variables for map management
            let map = null;
            let pickupMarker = null;
            let dropoffMarker = null;
            let routingControl = null;
            
            // Variables for form state
            let searchResults = null;
            let selectedVehicleType = null;
            let rideId = null;
            
            // Initialize the map
            initMap();
            
            // Button event listeners
            document.getElementById('search-rides-btn').addEventListener('click', searchRides);
            document.getElementById('save-locations-btn').addEventListener('click', showSaveLocationForm);
            document.getElementById('close-save-location').addEventListener('click', hideSaveLocationForm);
            
            // Handle rider confirmation buttons
            const cancelRideBtn = document.getElementById('cancel-ride-confirmation');
            if (cancelRideBtn) {
                cancelRideBtn.addEventListener('click', hideRideConfirmationModal);
            }
            
            const selectDriverBtn = document.getElementById('select-driver-btn');
if (selectDriverBtn) {
    selectDriverBtn.addEventListener('click', function() {
        const vehicleType = document.getElementById('confirm-vehicle-type-input').value;
        const pickupLat = document.getElementById('pickup_latitude').value;
        const pickupLng = document.getElementById('pickup_longitude').value;
        
        if (!vehicleType || !pickupLat || !pickupLng) {
            alert('Please complete your ride selection first');
            return;
        }
        
        // Redirect to the driver selection page with query parameters
        window.location.href = `/passenger/select-driver?vehicle_type=${vehicleType}&pickup_latitude=${pickupLat}&pickup_longitude=${pickupLng}`;
    });
}
            
            // Saved locations dropdowns
            const savedPickupBtn = document.getElementById('saved-pickup-btn');
            const savedDropoffBtn = document.getElementById('saved-dropoff-btn');
            const savedPickupDropdown = document.getElementById('saved-pickup-dropdown');
            const savedDropoffDropdown = document.getElementById('saved-dropoff-dropdown');
            
            if (savedPickupBtn) {
                savedPickupBtn.addEventListener('click', function() {
                    savedPickupDropdown.classList.toggle('hidden');
                    
                    if (savedDropoffDropdown) {
                        savedDropoffDropdown.classList.add('hidden');
                    }
                });
            }
            
            if (savedDropoffBtn) {
                savedDropoffBtn.addEventListener('click', function() {
                    savedDropoffDropdown.classList.toggle('hidden');
                    
                    if (savedPickupDropdown) {
                        savedPickupDropdown.classList.add('hidden');
                    }
                });
            }
            
            // Add event listeners to saved location items
            document.querySelectorAll('.saved-location-item').forEach(item => {
                item.addEventListener('click', function() {
                    const address = this.getAttribute('data-address');
                    const lat = this.getAttribute('data-lat');
                    const lng = this.getAttribute('data-lng');
                    const target = this.getAttribute('data-target');
                    
                    if (target === 'pickup') {
                        document.getElementById('pickup_location').value = address;
                        document.getElementById('pickup_latitude').value = lat;
                        document.getElementById('pickup_longitude').value = lng;
                        savedPickupDropdown.classList.add('hidden');
                        
                        if (pickupMarker) {
                            map.removeLayer(pickupMarker);
                        }
                        
                        pickupMarker = L.marker([lat, lng]).addTo(map);
                        pickupMarker.bindPopup('Pickup: ' + address).openPopup();
                        
                        map.setView([lat, lng], 14);
                    } else if (target === 'dropoff') {
                        document.getElementById('dropoff_location').value = address;
                        document.getElementById('dropoff_latitude').value = lat;
                        document.getElementById('dropoff_longitude').value = lng;
                        savedDropoffDropdown.classList.add('hidden');
                        
                        if (dropoffMarker) {
                            map.removeLayer(dropoffMarker);
                        }
                        
                        dropoffMarker = L.marker([lat, lng]).addTo(map);
                        dropoffMarker.bindPopup('Destination: ' + address).openPopup();
                        
                        map.setView([lat, lng], 14);
                    }
                    
                    // If both markers are set, calculate route
                    if (pickupMarker && dropoffMarker) {
                        calculateRoute();
                    }
                });
            });
            
            // Close dropdowns when clicking outside
            document.addEventListener('click', function(event) {
                if (savedPickupBtn && !savedPickupBtn.contains(event.target) && !savedPickupDropdown.contains(event.target)) {
                    savedPickupDropdown.classList.add('hidden');
                }
                
                if (savedDropoffBtn && !savedDropoffBtn.contains(event.target) && !savedDropoffDropdown.contains(event.target)) {
                    savedDropoffDropdown.classList.add('hidden');
                }
            });
            
            // Initialize map
            function initMap() {
                map = L.map('map').setView([31.7917, -7.0926], 6); // Center on Morocco
                
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                    maxZoom: 19
                }).addTo(map);
                
                // Get user's location if available
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        position => {
                            const userCoords = [position.coords.latitude, position.coords.longitude];
                            map.setView(userCoords, 14);
                            
                            // Set as pickup location
                            reverseGeocode(userCoords[0], userCoords[1], 'pickup');
                        },
                        error => {
                            console.error("Error getting location:", error);
                        }
                    );
                }
                
                // Enable click-to-set-location on map
                map.on('click', function(e) {
                    // Ask user if they want to set pickup or dropoff
                    const action = confirm('Set as pickup location? Click Cancel to set as destination.');
                    
                    if (action) {
                        // Set as pickup
                        if (pickupMarker) {
                            map.removeLayer(pickupMarker);
                        }
                        
                        pickupMarker = L.marker(e.latlng).addTo(map);
                        pickupMarker.bindPopup('Pickup Location').openPopup();
                        
                        reverseGeocode(e.latlng.lat, e.latlng.lng, 'pickup');
                    } else {
                        // Set as dropoff
                        if (dropoffMarker) {
                            map.removeLayer(dropoffMarker);
                        }
                        
                        dropoffMarker = L.marker(e.latlng).addTo(map);
                        dropoffMarker.bindPopup('Destination').openPopup();
                        
                        reverseGeocode(e.latlng.lat, e.latlng.lng, 'dropoff');
                    }
                    
                    // If both markers are set, calculate route
                    if (pickupMarker && dropoffMarker) {
                        calculateRoute();
                    }
                });
            }
            
            // Reverse geocode coordinates to address
            function reverseGeocode(lat, lng, target) {
                // Use Nominatim for reverse geocoding
                fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`)
                    .then(response => response.json())
                    .then(data => {
                        const address = data.display_name;
                        
                        if (target === 'pickup') {
                            document.getElementById('pickup_location').value = address;
                            document.getElementById('pickup_latitude').value = lat;
                            document.getElementById('pickup_longitude').value = lng;
                        } else if (target === 'dropoff') {
                            document.getElementById('dropoff_location').value = address;
                            document.getElementById('dropoff_latitude').value = lat;
                            document.getElementById('dropoff_longitude').value = lng;
                        }
                    })
                    .catch(error => {
                        console.error('Error reverse geocoding:', error);
                    });
            }
            
            // Calculate route between pickup and dropoff
            function calculateRoute() {
                const pickupLat = document.getElementById('pickup_latitude').value;
                const pickupLng = document.getElementById('pickup_longitude').value;
                const dropoffLat = document.getElementById('dropoff_latitude').value;
                const dropoffLng = document.getElementById('dropoff_longitude').value;
                
                if (!pickupLat || !pickupLng || !dropoffLat || !dropoffLng) {
                    return;
                }
                
                // Remove existing route if any
                if (routingControl) {
                    map.removeControl(routingControl);
                }
                
                // Create new route
                routingControl = L.Routing.control({
                    waypoints: [
                        L.latLng(pickupLat, pickupLng),
                        L.latLng(dropoffLat, dropoffLng)
                    ],
                    routeWhileDragging: false,
                    showAlternatives: false,
                    fitSelectedRoutes: true,
                    lineOptions: {
                        styles: [
                            { color: '#6366F1', opacity: 0.8, weight: 6 },
                            { color: '#4F46E5', opacity: 0.5, weight: 2 }
                        ]
                    },
                    createMarker: function() { return null; } // Don't create default markers
                }).addTo(map);
                
                // Minimize control panel
                routingControl.hide();
                
                // Show route info card when route is calculated
                routingControl.on('routesfound', function(e) {
                    const routes = e.routes;
                    const summary = routes[0].summary;
                    
                    // Update route info
                    document.getElementById('distance-value').textContent = (summary.totalDistance / 1000).toFixed(1) + ' km';
                    document.getElementById('duration-value').textContent = Math.round(summary.totalTime / 60) + ' min';
                    document.getElementById('route-pickup-location').textContent = document.getElementById('pickup_location').value;
                    document.getElementById('route-dropoff-location').textContent = document.getElementById('dropoff_location').value;
                    
                    // Show route info card
                    document.getElementById('route-info-card').classList.remove('hidden');
                });
            }
            
            // Search for available rides
            function searchRides() {
                const pickupLat = document.getElementById('pickup_latitude').value;
                const pickupLng = document.getElementById('pickup_longitude').value;
                const dropoffLat = document.getElementById('dropoff_latitude').value;
                const dropoffLng = document.getElementById('dropoff_longitude').value;
                const pickupLocation = document.getElementById('pickup_location').value;
                const dropoffLocation = document.getElementById('dropoff_location').value;
                
                if (!pickupLat || !pickupLng || !dropoffLat || !dropoffLng) {
                    alert('Please select pickup and destination locations');
                    return;
                }
                
                // Show loading state
                document.getElementById('search-rides-btn').disabled = true;
                document.getElementById('search-rides-btn').innerHTML = `
                    <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Searching...
                `;
                
                // Make AJAX request to calculate ride options
                fetch('{{ route('passenger.calculate.options') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        pickup_location: pickupLocation,
                        pickup_latitude: pickupLat,
                        pickup_longitude: pickupLng,
                        dropoff_location: dropoffLocation,
                        dropoff_latitude: dropoffLat,
                        dropoff_longitude: dropoffLng
                    })
                })
                .then(response => response.json())
                .then(data => {
    // Reset button state
    document.getElementById('search-rides-btn').disabled = false;
    document.getElementById('search-rides-btn').innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
        </svg>
        Find Rides
    `;
    
    // Store results
    searchResults = data;
    
    // Display results
    displayRideOptions(data.ride_options);
    
    // Update route info if not already shown
    if (document.getElementById('route-info-card').classList.contains('hidden')) {
        document.getElementById('distance-value').textContent = data.distance.km + ' km';
        document.getElementById('duration-value').textContent = data.duration.text;
        document.getElementById('route-pickup-location').textContent = pickupLocation;
        document.getElementById('route-dropoff-location').textContent = dropoffLocation;
        
        document.getElementById('route-info-card').classList.remove('hidden');
    }
    
    // Show message about driver selection
    setTimeout(() => {
        if (searchResults && Object.keys(searchResults.ride_options).length > 0) {
            const container = document.getElementById('ride-options-container');
            if (container && !document.getElementById('driver-availability-message')) {
                const message = document.createElement('div');
                message.id = 'driver-availability-message';
                message.className = 'mt-4 bg-blue-50 p-3 rounded-md text-sm text-blue-700';
                message.innerHTML = `
                    <p class="font-medium">Select a vehicle type to view available drivers in your area</p>
                    <p>You'll be able to choose your preferred driver based on ratings, distance, and more.</p>
                `;
                container.appendChild(message);
            }
        }
    }, 500);
})
            }
            
            // Display available ride options
            function displayRideOptions(rideOptions) {
                const container = document.getElementById('ride-options-container');
                container.innerHTML = '';
                
                // Exit if no options
                if (!rideOptions || Object.keys(rideOptions).length === 0) {
                    container.innerHTML = `
                        <div class="bg-yellow-50 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">No rides available</h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>Sorry, no rides are available for your route at this time. Please try again later.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    document.getElementById('ride-options-card').classList.remove('hidden');
                    return;
                }
                
                // Create vehicle type selection UI
                container.innerHTML = `
                    <div class="vehicle-types-section mb-4">
                        <h3 class="text-lg font-medium mb-3">Select Vehicle Type</h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                            <!-- Vehicle types will be added here -->
                        </div>
                        
                        <!-- Hidden input to store selected vehicle type -->
                        <input type="hidden" name="vehicle_type" id="selected_vehicle_type" value="share">
                    </div>
                `;
                
                const vehicleTypesGrid = container.querySelector('.grid');
                
                // Create a card for each ride option
                Object.values(rideOptions).forEach(option => {
                    // Skip women-only rides for non-female passengers
                    if (option.vehicle_type === 'women' && {{ Auth::user()->gender !== 'female' ? 'true' : 'false' }}) {
                        return;
                    }
                    
                    const card = document.createElement('div');
                    card.className = 'vehicle-type-card border rounded-lg p-4 cursor-pointer hover:border-blue-500 transition-colors';
                    card.setAttribute('data-vehicle-type', option.vehicle_type);
                    
                    // Determine icon based on vehicle type
                    let icon = '';
                    switch(option.vehicle_type) {
                        case 'share':
                            icon = '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>';
                            break;
                        case 'comfort':
                            icon = '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>';
                            break;
                        case 'women':
                            icon = '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-pink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>';
                            break;
                        case 'wav':
                            icon = '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>';
                            break;
                        case 'black':
                            icon = '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" /></svg>';
                            break;
                    }
                    
                    // HTML structure for the card
                    card.innerHTML = `
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-medium">${option.display_name}</h4>
                                <p class="text-sm text-gray-600">${option.wait_time_minutes} min wait</p>
                            </div>
                            <div class="vehicle-check hidden">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        </div>
                        <div class="text-sm mt-2">
                            <span class="font-semibold vehicle-price" data-base-price="${option.base_fare}">MAD ${option.total_fare.toFixed(2)}</span>
                            ${option.is_surge_active ? `<span class="text-red-500 ml-1">Surge x${option.surge_multiplier}</span>` : ''}
                        </div>
                        ${option.vehicle_type === 'women' ? 
                            `<div class="mt-2 text-xs bg-pink-50 text-pink-700 p-2 rounded">
                                <span class="font-medium">Women-only service:</span> Female drivers only
                            </div>` : ''}
                    `;
                    
                    card.addEventListener('click', function() {
    // Set selected type
    document.getElementById('selected_vehicle_type').value = option.vehicle_type;
    
    // Reset all cards
    document.querySelectorAll('.vehicle-type-card').forEach(el => {
        el.classList.remove('border-blue-500', 'bg-blue-50');
        el.querySelector('.vehicle-check').classList.add('hidden');
    });
    
    // Highlight this card
    this.classList.add('border-blue-500', 'bg-blue-50');
    this.querySelector('.vehicle-check').classList.remove('hidden');
    
    // Now trigger ride option selection
    selectRideOption(option);
});
                    
                    vehicleTypesGrid.appendChild(card);
                });
                
                // Show the ride options card
                document.getElementById('ride-options-card').classList.remove('hidden');
                
                // If only one option, select it automatically
                if (Object.keys(rideOptions).length === 1) {
                    const onlyOption = Object.values(rideOptions)[0];
                    document.querySelector(`.vehicle-type-card[data-vehicle-type="${onlyOption.vehicle_type}"]`).click();
                }
            }
            
// Select a ride option and redirect to driver selection
function selectRideOption(option) {
    selectedVehicleType = option.vehicle_type;
    
    // Update hidden input
    document.getElementById('confirm-vehicle-type-input').value = option.vehicle_type;
    
    // Instead of showing the confirmation modal directly,
    // redirect to the driver selection page
    const pickupLat = document.getElementById('pickup_latitude').value;
    const pickupLng = document.getElementById('pickup_longitude').value;
    
    if (!pickupLat || !pickupLng || !selectedVehicleType) {
        alert('Please complete your ride selection first');
        return;
    }
    
    // Redirect to the driver selection page with query parameters
    window.location.href = `/passenger/select-driver?vehicle_type=${selectedVehicleType}&pickup_latitude=${pickupLat}&pickup_longitude=${pickupLng}`;
}
            
            // Hide ride confirmation modal
            function hideRideConfirmationModal() {
                document.getElementById('ride-confirmation-modal').classList.add('hidden');
            }
            
            // Show save location form
            function showSaveLocationForm() {
                // Populate form with current location
                const locationToSave = window.confirm('Save pickup location? Click Cancel to save destination instead.');
                
                if (locationToSave) {
                    // Save pickup location
                    document.getElementById('location_address').value = document.getElementById('pickup_location').value;
                    document.getElementById('location_latitude').value = document.getElementById('pickup_latitude').value;
                    document.getElementById('location_longitude').value = document.getElementById('pickup_longitude').value;
                } else {
                    // Save dropoff location
                    document.getElementById('location_address').value = document.getElementById('dropoff_location').value;
                    document.getElementById('location_latitude').value = document.getElementById('dropoff_latitude').value;
                    document.getElementById('location_longitude').value = document.getElementById('dropoff_longitude').value;
                }
                
                // Show save location card
                document.getElementById('save-location-card').classList.remove('hidden');
            }
            
            // Hide save location form
            function hideSaveLocationForm() {
                document.getElementById('save-location-card').classList.add('hidden');
            }
            
            // Close modals when user clicks outside
            window.addEventListener('click', function(event) {
                const confirmationModal = document.getElementById('ride-confirmation-modal');
                if (event.target === confirmationModal) {
                    hideRideConfirmationModal();
                }
            });
            
            // Add CSRF token to all AJAX requests
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            document.addEventListener('DOMContentLoaded', function() {
                const xhr = new XMLHttpRequest();
                xhr.open('GET', '/sanctum/csrf-cookie');
                xhr.setRequestHeader('X-CSRF-TOKEN', token);
                xhr.withCredentials = true;
                xhr.send();
            });
        });

        // Add this JavaScript to your book ride page, after the existing scripts

/**
 * Check for available drivers after calculating ride options
 */
function checkAvailableDrivers(vehicleType) {
    // Only proceed if we have coordinates
    const pickupLat = document.getElementById('pickup_latitude').value;
    const pickupLng = document.getElementById('pickup_longitude').value;
    
    if (!pickupLat || !pickupLng) {
        return;
    }
    
    // Show loading state
    const container = document.getElementById('available-drivers-container');
    if (container) {
        container.innerHTML = '<div class="text-center py-3">Searching for drivers...</div>';
    }
    
    // Make fetch request to get available drivers
    fetch(`{{ route('passenger.available.drivers') }}?vehicle_type=${vehicleType}&pickup_latitude=${pickupLat}&pickup_longitude=${pickupLng}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(response => {
        if (response.success && response.drivers && response.drivers.length > 0) {
            // We have drivers, display them
            renderAvailableDrivers(response.drivers, vehicleType);
        } else {
            // No drivers available
            if (container) {
                container.innerHTML = `
                    <div class="bg-yellow-100 p-4 rounded-md">
                        <h5 class="font-medium">No drivers available</h5>
                        <p class="mb-0">There are no available drivers in your area right now. Please try again later or choose a different vehicle type.</p>
                    </div>
                `;
            }
        }
    })
    .catch(error => {
        if (container) {
            container.innerHTML = `
                <div class="bg-red-100 p-4 rounded-md">
                    <h5 class="font-medium">Error</h5>
                    <p class="mb-0">There was an error checking for available drivers. Please try again.</p>
                </div>
            `;
        }
    });
}

// Locate Me button functionality
const locateMeBtn = document.getElementById('locate-me-btn');
if (locateMeBtn) {
    locateMeBtn.addEventListener('click', function() {
        if (navigator.geolocation) {
            // Show loading indicator
            locateMeBtn.innerHTML = `
                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            `;
            
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    // Get coordinates
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    // Update hidden inputs
                    document.getElementById('pickup_latitude').value = lat;
                    document.getElementById('pickup_longitude').value = lng;
                    
                    // Reverse geocode to get address
                    reverseGeocode(lat, lng, 'pickup');
                    
                    // Update map
                    if (pickupMarker) {
                        map.removeLayer(pickupMarker);
                    }
                    
                    pickupMarker = L.marker([lat, lng]).addTo(map);
                    pickupMarker.bindPopup('Your Current Location').openPopup();
                    
                    map.setView([lat, lng], 15);
                    
                    // Reset button
                    locateMeBtn.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                        </svg>
                    `;
                    
                    // If both markers are set, calculate route
                    if (pickupMarker && dropoffMarker) {
                        calculateRoute();
                    }
                },
                function(error) {
                    // Reset button
                    locateMeBtn.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                        </svg>
                    `;
                    
                    // Show error message
                    let message = 'Could not get your location.';
                    
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            message = 'Location access denied. Please allow location access in your browser settings.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            message = 'Location information is unavailable.';
                            break;
                        case error.TIMEOUT:
                            message = 'Location request timed out.';
                            break;
                    }
                    
                    alert(message);
                }
            );
        } else {
            alert('Geolocation is not supported by your browser.');
        }
    });
}

/**
 * Render available drivers in the UI
 */
function renderAvailableDrivers(drivers, vehicleType) {
    var container = $('#available-drivers-container');
    
    if (drivers.length === 0) {
        container.html(
            '<div class="alert alert-warning">' +
            '<h5><i class="fas fa-exclamation-triangle mr-2"></i> No drivers available</h5>' +
            '<p class="mb-0">There are no available drivers in your area right now. Please try again later or choose a different vehicle type.</p>' +
            '</div>'
        );
        return;
    }
    
    var html = '<h5>' + drivers.length + ' Available Driver' + (drivers.length > 1 ? 's' : '') + '</h5>';
    html += '<div class="row">';
    
    // Loop through drivers and create cards
    drivers.forEach(function(driver) {
        html += '<div class="col-md-6 mb-3">' +
                '<div class="card h-100">' +
                '<div class="card-body">' +
                '<div class="d-flex align-items-center mb-3">' +
                '<div class="mr-3">';
        
        if (driver.profile_picture) {
            html += '<img src="/storage/' + driver.profile_picture + '" alt="' + driver.name + 
                   '" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">';
        } else {
            html += '<div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" ' +
                   'style="width: 50px; height: 50px;">' + driver.name.charAt(0).toUpperCase() + '</div>';
        }
        
        html += '</div>' +
                '<div>' +
                '<h5 class="mb-0">' + driver.name + '</h5>' +
                '<div class="text-muted small">' +
                '<span class="text-warning">';
        
        // Generate stars for rating
        var fullStars = Math.floor(driver.rating);
        var halfStar = driver.rating % 1 >= 0.5;
        
        for (var i = 0; i < 5; i++) {
            if (i < fullStars) {
                html += '<i class="fas fa-star"></i>';
            } else if (i === fullStars && halfStar) {
                html += '<i class="fas fa-star-half-alt"></i>';
            } else {
                html += '<i class="far fa-star"></i>';
            }
        }
        
        html += ' ' + driver.rating.toFixed(1) + '</span>' +
                ' • ' + driver.completed_rides + ' rides' +
                '</div>' +
                '</div>' +
                '</div>' +
                
                '<div class="driver-info mb-2">' +
                '<div class="row">' +
                '<div class="col-6">' +
                '<small class="text-muted">Vehicle</small>' +
                '<p class="mb-0">' + driver.vehicle.make + ' ' + driver.vehicle.model + '</p>' +
                '<p class="mb-0">' + driver.vehicle.color + ' • ' + driver.vehicle.plate_number + '</p>' +
                '</div>' +
                '<div class="col-6">' +
                '<small class="text-muted">ETA</small>' +
                '<p class="mb-0"><strong>' + driver.eta_minutes + ' mins</strong></p>' +
                '<p class="mb-0">' + driver.distance_km.toFixed(1) + ' km away</p>' +
                '</div>' +
                '</div>' +
                '</div>';
        
        if (driver.women_only_driver) {
            html += '<div class="mb-2"><span class="badge badge-info">Women Only Driver</span></div>';
        }
        
        html += '<button type="button" class="btn btn-sm btn-primary btn-block select-driver" ' +
               'data-driver-id="' + driver.id + '">' +
               'Select This Driver</button>' +
               '</div>' +
               '</div>' +
               '</div>';
    });
    
    html += '</div>';
    
    // Add auto-select option
    html += '<div class="form-group mt-3">' +
           '<div class="custom-control custom-checkbox">' +
           '<input type="checkbox" class="custom-control-input" id="auto-select-driver" checked>' +
           '<label class="custom-control-label" for="auto-select-driver">' +
           'Automatically match me with the best available driver</label>' +
           '</div>' +
           '</div>';
    
    container.html(html);
    
    // Add event listener for driver selection
    $('.select-driver').click(function() {
        var driverId = $(this).data('driver-id');
        $('#selected-driver-id').val(driverId);
        $('#auto-select-driver').prop('checked', false);
        
        // Highlight selected driver
        $('.select-driver').removeClass('btn-success').addClass('btn-primary').text('Select This Driver');
        $(this).removeClass('btn-primary').addClass('btn-success').text('Selected ✓');
    });
    
    // Add event listener for auto-select checkbox
    $('#auto-select-driver').change(function() {
        if ($(this).is(':checked')) {
            $('.select-driver').removeClass('btn-success').addClass('btn-primary').text('Select This Driver');
            $('#selected-driver-id').val('');
        }
    });
}

// Modify the existing calculateRideOptions success handler to call checkAvailableDrivers
// Find this part in your existing code:
// success: function(response) {
//     // existing code
// }

// And add this line at the end of the success function:
// Then call checkAvailableDrivers with the selected vehicle type
checkAvailableDrivers($('#vehicle-type-selector .active').data('vehicle-type'));

// Also add an event handler for vehicle type selection to update available drivers
$('#vehicle-type-selector .vehicle-type').click(function() {
    // After the existing code that updates ride option display:
    var vehicleType = $(this).data('vehicle-type');
    checkAvailableDrivers(vehicleType);
});

// Add this to your document ready function in bookRide.blade.php
document.getElementById('select-driver-btn').addEventListener('click', function() {
    const vehicleType = document.getElementById('selected_vehicle_type').value;
    const pickupLat = document.getElementById('pickup_latitude').value;
    const pickupLng = document.getElementById('pickup_longitude').value;
    
    if (!vehicleType || !pickupLat || !pickupLng) {
        alert('Please complete your ride selection first');
        return;
    }
    
    // Redirect to the driver selection page with query parameters
    window.location.href = `{{ route('passenger.select.driver') }}?vehicle_type=${vehicleType}&pickup_latitude=${pickupLat}&pickup_longitude=${pickupLng}`;
});
// Add this to your existing document.addEventListener('DOMContentLoaded', function() {...}) section
// This will restore locations from URL parameters
function getUrlParameter(name) {
    name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
    var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
    var results = regex.exec(location.search);
    return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
}

// Check for location parameters in URL
const pickupLocation = getUrlParameter('pickup_location');
const pickupLatitude = getUrlParameter('pickup_latitude');
const pickupLongitude = getUrlParameter('pickup_longitude');
const dropoffLocation = getUrlParameter('dropoff_location');
const dropoffLatitude = getUrlParameter('dropoff_latitude');
const dropoffLongitude = getUrlParameter('dropoff_longitude');
const vehicleType = getUrlParameter('vehicle_type');

// If we have location data in the URL, restore it
if (pickupLocation && pickupLatitude && pickupLongitude && 
    dropoffLocation && dropoffLatitude && dropoffLongitude) {
    
    // Restore pickup location
    document.getElementById('pickup_location').value = pickupLocation;
    document.getElementById('pickup_latitude').value = pickupLatitude;
    document.getElementById('pickup_longitude').value = pickupLongitude;
    
    // Restore dropoff location
    document.getElementById('dropoff_location').value = dropoffLocation;
    document.getElementById('dropoff_latitude').value = dropoffLatitude;
    document.getElementById('dropoff_longitude').value = dropoffLongitude;
    
    // Wait a moment for the map to initialize
    setTimeout(function() {
        // Add pickup marker
        if (map) {
            if (pickupMarker) {
                map.removeLayer(pickupMarker);
            }
            
            pickupMarker = L.marker([pickupLatitude, pickupLongitude]).addTo(map);
            pickupMarker.bindPopup('Pickup: ' + pickupLocation);
            
            // Add dropoff marker
            if (dropoffMarker) {
                map.removeLayer(dropoffMarker);
            }
            
            dropoffMarker = L.marker([dropoffLatitude, dropoffLongitude]).addTo(map);
            dropoffMarker.bindPopup('Destination: ' + dropoffLocation);
            
            // If both markers are set, calculate route
            if (pickupMarker && dropoffMarker) {
                calculateRoute();
                
                // Center map to show both markers
                const bounds = L.latLngBounds([
                    [pickupLatitude, pickupLongitude],
                    [dropoffLatitude, dropoffLongitude]
                ]);
                map.fitBounds(bounds, { padding: [50, 50] });
                
                // Automatically search for rides again
                searchRides();
            }
        }
    }, 500); // 500ms delay to ensure map is fully loaded
    
    // Remove the URL parameters without refreshing the page
    if (history.pushState) {
        var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname;
        window.history.pushState({path:newurl},'',newurl);
    }
}
// Check if we have URL parameters for ride details
const urlParams = new URLSearchParams(window.location.search);
const fromDriverSelection = urlParams.get('from_driver_selection');

if (fromDriverSelection === 'true') {
    // Get the location values from URL parameters
    const pickupLocation = urlParams.get('pickup_location');
    const pickupLatitude = urlParams.get('pickup_latitude');
    const pickupLongitude = urlParams.get('pickup_longitude');
    const dropoffLocation = urlParams.get('dropoff_location');
    const dropoffLatitude = urlParams.get('dropoff_latitude');
    const dropoffLongitude = urlParams.get('dropoff_longitude');
    const vehicleType = urlParams.get('vehicle_type');
    
    console.log('Restoring data from URL parameters');
    console.log('Pickup:', pickupLocation, pickupLatitude, pickupLongitude);
    console.log('Dropoff:', dropoffLocation, dropoffLatitude, dropoffLongitude);
    
    if (pickupLocation && pickupLatitude && pickupLongitude && 
        dropoffLocation && dropoffLatitude && dropoffLongitude) {
        
        // Save parameters so they're available after map initialization
        window.rideDataToRestore = {
            pickupLocation,
            pickupLatitude, 
            pickupLongitude,
            dropoffLocation,
            dropoffLatitude,
            dropoffLongitude,
            vehicleType
        };
        
        // Clear URL parameters without reloading
        window.history.replaceState({}, document.title, window.location.pathname);
    }
}
// Restore saved ride data after map and controls are initialized
setTimeout(() => {
    if (window.rideDataToRestore) {
        console.log('Restoring ride data after map init');
        const data = window.rideDataToRestore;
        
        // Set form values
        document.getElementById('pickup_location').value = data.pickupLocation;
        document.getElementById('pickup_latitude').value = data.pickupLatitude;
        document.getElementById('pickup_longitude').value = data.pickupLongitude;
        document.getElementById('dropoff_location').value = data.dropoffLocation;
        document.getElementById('dropoff_latitude').value = data.dropoffLatitude;
        document.getElementById('dropoff_longitude').value = data.dropoffLongitude;
        
        // Add pickup marker
        if (map) {
            if (pickupMarker) {
                map.removeLayer(pickupMarker);
            }
            
            pickupMarker = L.marker([data.pickupLatitude, data.pickupLongitude]).addTo(map);
            pickupMarker.bindPopup('Pickup: ' + data.pickupLocation);
            
            // Add dropoff marker
            if (dropoffMarker) {
                map.removeLayer(dropoffMarker);
            }
            
            dropoffMarker = L.marker([data.dropoffLatitude, data.dropoffLongitude]).addTo(map);
            dropoffMarker.bindPopup('Destination: ' + data.dropoffLocation);
            
            // Calculate route
            calculateRoute();
            
            // Center map to show both markers
            const bounds = L.latLngBounds([
                [data.pickupLatitude, data.pickupLongitude],
                [data.dropoffLatitude, data.dropoffLongitude]
            ]);
            map.fitBounds(bounds, { padding: [50, 50] });
            
            // Automatically search for rides
            searchRides();
        }
    }
}, 1000); // Give the map 1 second to fully initialize

document.addEventListener('DOMContentLoaded', function() {
    // Find the women-only toggle elements
    const toggleButton = document.getElementById('women-only-toggle');
    const toggleDot = document.getElementById('women-only-toggle-dot');
    const toggleText = document.getElementById('women-only-text');
    const checkboxInput = document.getElementById('women_only_rides');
    
    if (!toggleButton || !checkboxInput) return;
    
    // Get CSRF token for AJAX requests
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Initial state
    let isEnabled = checkboxInput.checked;
    
    // Add event listener to toggle button
    toggleButton.addEventListener('click', function() {
        // Update UI state
        isEnabled = !isEnabled;
        updateToggleUI();
        
        // Update hidden checkbox value
        checkboxInput.checked = isEnabled;
        
        // Save preference via AJAX
        savePreference(isEnabled);
    });
    
    // Function to update toggle UI
    function updateToggleUI() {
        if (isEnabled) {
            toggleButton.classList.remove('bg-gray-300');
            toggleButton.classList.add('bg-pink-500');
            toggleDot.classList.remove('translate-x-1');
            toggleDot.classList.add('translate-x-5');
            toggleText.textContent = 'Women-Only Rides On';
            
            // Add active badge if it doesn't exist
            if (!toggleButton.parentNode.parentNode.querySelector('.bg-pink-100')) {
                const badge = document.createElement('div');
                badge.className = 'ml-auto px-2 py-1 rounded bg-pink-100 text-pink-800 text-xs';
                badge.textContent = 'Active';
                toggleButton.parentNode.parentNode.appendChild(badge);
            }
        } else {
            toggleButton.classList.remove('bg-pink-500');
            toggleButton.classList.add('bg-gray-300');
            toggleDot.classList.remove('translate-x-5');
            toggleDot.classList.add('translate-x-1');
            toggleText.textContent = 'Women-Only Rides Off';
            
            // Remove active badge if it exists
            const badge = toggleButton.parentNode.parentNode.querySelector('.bg-pink-100');
            if (badge) {
                badge.remove();
            }
        }
    }
    
    // Function to save preference
    function savePreference(enabled) {
        fetch('/passenger/preferences/update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                preferences: {
                    women_only_rides: enabled
                }
            })
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                console.error('Failed to update preference:', data.message);
                // Revert toggle if error
                isEnabled = !isEnabled;
                updateToggleUI();
                checkboxInput.checked = isEnabled;
                
                // Show error message
                showNotification('Error updating preference', 'error');
            } else {
                showNotification('Preference updated successfully', 'success');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Revert toggle if error
            isEnabled = !isEnabled;
            updateToggleUI();
            checkboxInput.checked = isEnabled;
            
            // Show error message
            showNotification('Network error. Please try again.', 'error');
        });
    }
    
    // Function to show notification
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 px-4 py-2 rounded shadow-lg ${type === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'} z-50`;
        notification.textContent = message;
        document.body.appendChild(notification);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
});
    </script>
</body>
</html>
