<!-- passenger/dashboard.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>inTime - Passenger Dashboard</title>
    
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
                <a href="{{ route('passenger.profile.private') }}" class="font-medium">My Profile</a>


            </nav>
        </div>
        
        <div class="flex justify-center space-x-4">
            <a href="{{ route('home') }}" class="bg-black text-white py-2 px-6 rounded-md font-medium hover:bg-gray-800 transition">
                Return to Home
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
            <!-- Left Column - Book a Ride, User Info -->
            <div class="w-full lg:w-1/3 flex flex-col gap-6">
                <!-- Book a Ride Card -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold mb-4">Book a Ride</h2>
                    
                    <form action="{{ route('passenger.book') }}" method="GET">
                        <div class="space-y-4">
                            <button type="submit" class="w-full bg-black text-white py-3 px-4 rounded-md font-medium hover:bg-gray-800 transition flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.707-10.293a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L9.414 11H13a1 1 0 100-2H9.414l1.293-1.293z" clip-rule="evenodd" />
                                </svg>
                                Book Now
                            </button>
                        </div>
                    </form>
                </div>
<!-- In passenger/rideHistory.blade.php in the ride card -->
@if(isset($ride) && $ride->driver)
    <a href="{{ route('driver.public.profile', $ride->driver->id) }}" class="text-blue-600 hover:text-blue-800 text-sm">
        View Driver Profile
    </a>
@endif

                
                <!-- User Info Card -->
                <div class="bg-white rounded-lg shadow-md p-6">
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
                            <h2 class="text-xl font-bold">{{ Auth::user()->name }}</h2>
                            <p class="text-gray-600">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
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
                <!-- Ride Preferences Summary Card (clickable to open modal) -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6 cursor-pointer" id="open-preferences-modal">
    <div class="flex justify-between items-center">
        <h2 class="text-xl font-bold">Ride Preferences</h2>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
        </svg>
    </div>
    
    <div class="mt-4 space-y-2">
        <div class="flex justify-between">
            <span class="text-gray-600">Preferred Vehicle Type</span>
            <span class="font-medium" id="preferred-vehicle-summary">{{ $passenger->ride_preferences['preferred_vehicle'] ?? 'Any' }}</span>
        </div>
        
        @if(Auth::user()->gender === 'female')
        <div class="flex justify-between">
            <span class="text-gray-600">Women-Only Rides 2</span>
            <span class="font-medium" id="women-only-summary">
                @if(Auth::user()->women_only_rides)
                <span class="text-pink-600">Enabled</span>
                @else
                Disabled
                @endif
            </span>
        </div>
        
        @endif
        
        <div class="flex justify-between">
            <span class="text-gray-600">Quiet Ride</span>
            <span class="font-medium" id="quiet-ride-summary">
                {{ isset($passenger->ride_preferences['quiet_ride']) && $passenger->ride_preferences['quiet_ride'] ? 'Enabled' : 'Disabled' }}
            </span>
        </div>
    </div>
</div>

<!-- Ride Preferences Modal -->
<div id="preferences-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" id="modal-backdrop"></div>
        
        <div class="relative inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div class="absolute top-0 right-0 pt-4 pr-4">
                <button type="button" id="close-preferences-modal" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
                    <span class="sr-only">Close</span>
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div>
                <div class="mt-3 text-center sm:mt-0 sm:text-left">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        Edit Ride Preferences x
                    </h3>
                    
                    <div class="mt-4">
                        <form id="preferences-form" method="POST">
                            @csrf
                            
                            <!-- Preferred Vehicle Type -->
                            <div class="mb-4">
                                <label for="preferred_vehicle" class="block text-sm font-medium text-gray-700 mb-1">
                                    Preferred Vehicle Type
                                </label>
                                <select id="preferred_vehicle" name="preferences[preferred_vehicle]" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-black focus:border-black sm:text-sm rounded-md">
                                    <option value="">Any</option>
                                    <option value="basic" {{ isset($passenger->ride_preferences['preferred_vehicle']) && $passenger->ride_preferences['preferred_vehicle'] == 'basic' ? 'selected' : '' }}>Basic</option>
                                    <option value="comfort" {{ isset($passenger->ride_preferences['preferred_vehicle']) && $passenger->ride_preferences['preferred_vehicle'] == 'comfort' ? 'selected' : '' }}>Comfort</option>
                                    <option value="black" {{ isset($passenger->ride_preferences['preferred_vehicle']) && $passenger->ride_preferences['preferred_vehicle'] == 'black' ? 'selected' : '' }}>Black</option>
                                    <option value="wav" {{ isset($passenger->ride_preferences['preferred_vehicle']) && $passenger->ride_preferences['preferred_vehicle'] == 'wav' ? 'selected' : '' }}>WAV</option>
                                </select>
                            </div>
                            
                            <!-- Women-Only Rides (visible only to female passengers) -->
                            @if(Auth::user()->gender === 'female')
                            <div class="mb-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Women-Only Rides
                                        </label>
                                        <span class="text-xs text-gray-500">When enabled, you'll be matched only with female drivers.</span>
                                    </div>
                                    
                                    <div class="flex items-center">
                                        <button type="button" id="women-only-toggle" class="relative inline-flex h-6 w-11 items-center rounded-full {{ Auth::user()->women_only_rides ? 'bg-pink-500' : 'bg-gray-300' }} transition-colors duration-300">
                                            <span id="women-only-toggle-dot" class="inline-block h-5 w-5 transform rounded-full bg-white shadow-md transition-transform duration-300 {{ Auth::user()->women_only_rides ? 'translate-x-5' : 'translate-x-1' }}"></span>
                                        </button>
                                    </div>
                                </div>
                                <input type="hidden" id="women_only_rides" name="preferences[women_only_rides]" value="{{ Auth::user()->women_only_rides ? '1' : '0' }}">
                            </div>
                            @endif
                            
                            <!-- Quiet Ride Preference -->
                            <div class="mb-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Quiet Ride
                                        </label>
                                        <span class="text-xs text-gray-500">Prefer minimal conversation during your ride.</span>
                                    </div>
                                    
                                    <div class="flex items-center">
                                        <button type="button" id="quiet-ride-toggle" class="relative inline-flex h-6 w-11 items-center rounded-full {{ isset($passenger->ride_preferences['quiet_ride']) && $passenger->ride_preferences['quiet_ride'] ? 'bg-blue-500' : 'bg-gray-300' }} transition-colors duration-300">
                                            <span id="quiet-ride-toggle-dot" class="inline-block h-5 w-5 transform rounded-full bg-white shadow-md transition-transform duration-300 {{ isset($passenger->ride_preferences['quiet_ride']) && $passenger->ride_preferences['quiet_ride'] ? 'translate-x-5' : 'translate-x-1' }}"></span>
                                        </button>
                                    </div>
                                </div>
                                <input type="hidden" id="quiet_ride" name="preferences[quiet_ride]" value="{{ isset($passenger->ride_preferences['quiet_ride']) && $passenger->ride_preferences['quiet_ride'] ? '1' : '0' }}">
                            </div>
                            
                            <!-- Save Button -->
                            <div class="mt-5 sm:mt-6">
                                <button type="button" id="save-preferences-btn" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-black text-base font-medium text-white hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black sm:text-sm">
                                    Save Preferences
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
                
              
            
            <!-- Right Column - Map, Active/Recent Rides -->
            <div class="w-full lg:w-2/3 flex flex-col gap-6">
                <!-- Map Container -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="h-96" id="map"></div>
                </div>
                
                <!-- Active Ride -->
                @if($activeRide)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold mb-4">Active Ride</h2>
                    
                    <div class="border-l-4 border-blue-500 pl-4 py-2">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="font-medium">
                                    @if($activeRide->driver)
                                        Ride with {{ $activeRide->driver->user->name }}
                                    @else
                                        Finding a driver...
                                    @endif
                                </h3>
                                <p class="text-sm text-gray-500">
                                    @if($activeRide->pickup_time)
                                        Started: {{ $activeRide->pickup_time->format('g:i A') }}
                                    @elseif($activeRide->reservation_status == 'matching')
                                        Matching with a driver...
                                    @else
                                        Scheduled: {{ $activeRide->reservation_date->format('M d, Y g:i A') }}
                                    @endif
                                </p>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if($activeRide->reservation_status == 'matching') 
                                    bg-yellow-100 text-yellow-800
                                @elseif($activeRide->pickup_time && !$activeRide->dropoff_time) 
                                    bg-green-100 text-green-800
                                @else 
                                    bg-blue-100 text-blue-800
                                @endif
                            ">
                                @if($activeRide->reservation_status == 'matching')
                                    Finding Driver
                                @elseif($activeRide->pickup_time && !$activeRide->dropoff_time)
                                    In Progress
                                @elseif($activeRide->reservation_status == 'accepted' && !$activeRide->pickup_time)
                                    Driver En Route
                                @else
                                    {{ ucfirst($activeRide->reservation_status) }}
                                @endif
                            </span>
                        </div>
                        
                        <div class="space-y-3 mb-4">
                            <div class="flex items-start">
                                <div class="mt-1 mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <circle cx="12" cy="12" r="8" stroke-width="2" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium">Pickup Location</p>
                                    <p class="text-sm text-gray-600">{{ $activeRide->pickup_location }}</p>
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
                                <p class="text-sm font-medium">Dropoff Location</p>
                                    <p class="text-sm text-gray-600">{{ $activeRide->dropoff_location }}</p>
                                </div>
                            </div>
                            
                            @if($activeRide->price)
                            <div class="flex items-start">
                                <div class="mt-1 mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium">Price</p>
                                    <p class="text-sm text-gray-600">
                                        MAD {{ number_format($activeRide->price, 2) }}
                                        @if($activeRide->surge_multiplier > 1)
                                            <span class="text-xs text-red-600 ml-1">(Surge x{{ $activeRide->surge_multiplier }})</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            @endif
                            
                            @if($activeRide->vehicle_type)
                            <div class="flex items-start">
                                <div class="mt-1 mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium">Vehicle Type</p>
                                    <p class="text-sm text-gray-600">{{ ucfirst($activeRide->vehicle_type) }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                        
                        <div class="flex space-x-3">
                            <a href="{{ route('passenger.active.ride') }}" class="bg-black text-white py-2 px-4 rounded-md text-sm font-medium hover:bg-gray-800 transition flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                View Details
                            </a>
                            
                            @if($activeRide->driver && $activeRide->reservation_status == 'accepted')
                                <a href="tel:{{ $activeRide->driver->user->phone }}" class="border border-gray-300 text-gray-700 py-2 px-4 rounded-md text-sm font-medium hover:bg-gray-50 transition flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                    </svg>
                                    Call Driver
                                </a>
                            @endif
                            
                            @if(in_array($activeRide->reservation_status, ['matching', 'pending', 'accepted']) && !$activeRide->pickup_time)
                                <form action="{{ route('passenger.cancel.ride', $activeRide->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this ride?')">
                                    @csrf
                                    <button type="submit" class="border border-red-300 text-red-700 py-2 px-4 rounded-md text-sm font-medium hover:bg-red-50 transition flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                        Cancel Ride
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                {{-- Add this to the passenger dashboard view, near the top of the content section --}}
<div class="row mb-4">
    <div class="col-md-6">
        <a href="{{ route('passenger.book') }}" class="btn btn-primary btn-block py-3">
            <i class="fas fa-car-side mr-2"></i> Book a Ride
        </a>
    </div>
    <div class="col-md-6">
        <a href="{{ route('passenger.nearby.drivers') }}" class="btn btn-info btn-block py-3">
            <i class="fas fa-search-location mr-2"></i> Find Nearby Drivers
        </a>
    </div>
</div>

                <!-- Recent Rides or Historical Rides -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold mb-4">{{ $activeRide ? 'Recent Rides' : 'Your Rides' }}</h2>
                    
                    @if(count($rideHistory) === 0)
                        <div class="bg-gray-50 rounded-md p-4 text-center">
                            <p class="text-gray-500">You don't have any past rides yet</p>
                            <p class="text-sm text-gray-400 mt-1">Book your first ride to get started</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($rideHistory as $ride)
                                <div class="border rounded-md p-4">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <h3 class="font-medium">
                                                @if($ride->driver)
                                                    Ride with {{ $ride->driver->user->name }}
                                                @else
                                                    Cancelled Ride
                                                @endif
                                            </h3>
                                            <p class="text-sm text-gray-500">{{ $ride->dropoff_time ? $ride->dropoff_time->format('M d, Y g:i A') : $ride->reservation_date->format('M d, Y g:i A') }}</p>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            @if($ride->ride_status == 'completed') 
                                                bg-green-100 text-green-800
                                            @elseif($ride->reservation_status == 'cancelled') 
                                                bg-red-100 text-red-800
                                            @else 
                                                bg-gray-100 text-gray-800
                                            @endif
                                        ">
                                            @if($ride->ride_status == 'completed')
                                                Completed
                                            @elseif($ride->reservation_status == 'cancelled')
                                                Cancelled
                                            @else
                                                {{ ucfirst($ride->reservation_status) }}
                                            @endif
                                        </span>
                                    </div>
                                    
                                    <div class="space-y-2 mb-3">
                                        <div class="flex items-start">
                                            <div class="mt-0.5 mr-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                            </div>
                                            <p class="text-sm text-gray-600">{{ $ride->pickup_location }} → {{ $ride->dropoff_location }}</p>
                                        </div>
                                        
                                        @if($ride->price)
                                        <div class="flex items-start">
                                            <div class="mt-0.5 mr-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                            <p class="text-sm text-gray-600">MAD {{ number_format($ride->price, 2) }}</p>
                                        </div>
                                        @endif
                                        
                                        @if($ride->vehicle_type)
                                        <div class="flex items-start">
                                            <div class="mt-0.5 mr-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                                </svg>
                                            </div>
                                            <p class="text-sm text-gray-600">{{ ucfirst($ride->vehicle_type) }}</p>
                                        </div>
                                        @endif
                                    </div>
                                    
                                    @if($ride->ride_status == 'completed' && !$ride->is_reviewed)
                                        <button type="button" class="text-blue-600 text-sm font-medium hover:text-blue-800" 
                                                onclick="openRateRideModal({{ $ride->id }})">
                                            Rate this ride
                                        </button>
                                    @endif
                                </div>
                            @endforeach
                            
                            <div class="text-center mt-4">
                                <a href="{{ route('passenger.history') }}" class="text-blue-600 text-sm font-medium hover:text-blue-800">
                                    View all rides
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>

    <!-- Rate Ride Modal -->
    <div id="rate-ride-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            
            <div class="relative inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div>
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-5">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Rate Your Ride</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Please rate your experience and provide any feedback you may have.
                            </p>
                        </div>
                    </div>
                </div>
                
                <form id="rate-ride-form" method="POST" class="mt-5">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                        <div class="flex items-center justify-center space-x-1">
                            <input type="hidden" name="rating" id="rating-value" value="5">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button" class="rating-star" data-value="{{ $i }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                </button>
                            @endfor
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="comment" class="block text-sm font-medium text-gray-700">Comment (Optional)</label>
                        <div class="mt-1">
                            <textarea id="comment" name="comment" rows="3" class="shadow-sm focus:ring-black focus:border-black block w-full sm:text-sm border-gray-300 rounded-md"></textarea>
                        </div>
                    </div>
                    
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-black text-base font-medium text-white hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black sm:col-start-2 sm:text-sm">
                            Submit Rating
                        </button>
                        <button type="button" id="cancel-rating" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Vehicle Features Preferences Section -->
<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Preferred Vehicle Features
    </label>
    <p class="text-xs text-gray-500 mb-2">Select vehicle features that are important to you.</p>
    
    <div class="grid grid-cols-2 gap-2">
        <div class="flex items-start">
            <div class="flex items-center h-5">
                <input id="feature_ac" name="preferences[vehicle_features][]" type="checkbox" value="ac" 
                    class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                    {{ isset($passenger->ride_preferences['vehicle_features']) && in_array('ac', $passenger->ride_preferences['vehicle_features']) ? 'checked' : '' }}>
            </div>
            <div class="ml-3 text-sm">
                <label for="feature_ac" class="font-medium text-gray-700">Air Conditioning</label>
            </div>
        </div>
        
        <div class="flex items-start">
            <div class="flex items-center h-5">
                <input id="feature_wifi" name="preferences[vehicle_features][]" type="checkbox" value="wifi" 
                    class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                    {{ isset($passenger->ride_preferences['vehicle_features']) && in_array('wifi', $passenger->ride_preferences['vehicle_features']) ? 'checked' : '' }}>
            </div>
            <div class="ml-3 text-sm">
                <label for="feature_wifi" class="font-medium text-gray-700">WiFi</label>
            </div>
        </div>
        
        <div class="flex items-start">
            <div class="flex items-center h-5">
                <input id="feature_child_seat" name="preferences[vehicle_features][]" type="checkbox" value="child_seat" 
                    class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                    {{ isset($passenger->ride_preferences['vehicle_features']) && in_array('child_seat', $passenger->ride_preferences['vehicle_features']) ? 'checked' : '' }}>
            </div>
            <div class="ml-3 text-sm">
                <label for="feature_child_seat" class="font-medium text-gray-700">Child Seat</label>
            </div>
        </div>
        
        <div class="flex items-start">
            <div class="flex items-center h-5">
                <input id="feature_usb_charger" name="preferences[vehicle_features][]" type="checkbox" value="usb_charger" 
                    class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                    {{ isset($passenger->ride_preferences['vehicle_features']) && in_array('usb_charger', $passenger->ride_preferences['vehicle_features']) ? 'checked' : '' }}>
            </div>
            <div class="ml-3 text-sm">
                <label for="feature_usb_charger" class="font-medium text-gray-700">USB Charger</label>
            </div>
        </div>
        
        <div class="flex items-start">
            <div class="flex items-center h-5">
                <input id="feature_pet_friendly" name="preferences[vehicle_features][]" type="checkbox" value="pet_friendly" 
                    class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                    {{ isset($passenger->ride_preferences['vehicle_features']) && in_array('pet_friendly', $passenger->ride_preferences['vehicle_features']) ? 'checked' : '' }}>
            </div>
            <div class="ml-3 text-sm">
                <label for="feature_pet_friendly" class="font-medium text-gray-700">Pet Friendly</label>
            </div>
        </div>
        
        <div class="flex items-start">
            <div class="flex items-center h-5">
                <input id="feature_luggage_carrier" name="preferences[vehicle_features][]" type="checkbox" value="luggage_carrier" 
                    class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                    {{ isset($passenger->ride_preferences['vehicle_features']) && in_array('luggage_carrier', $passenger->ride_preferences['vehicle_features']) ? 'checked' : '' }}>
            </div>
            <div class="ml-3 text-sm">
                <label for="feature_luggage_carrier" class="font-medium text-gray-700">Luggage Carrier</label>
            </div>
        </div>
    </div>
</div>

    <!-- Preferences Modal -->
    <div id="preferences-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            
            <div class="relative inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div>
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-gray-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-5">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Ride Preferences</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Customize your ride preferences. These settings will apply to all future rides.
                            </p>
                        </div>
                    </div>
                </div>
                
                <form action="{{ route('passenger.save.preferences') }}" method="POST" class="mt-5">
                    @csrf
                    <div class="mb-4">
                        <label for="vehicle_type" class="block text-sm font-medium text-gray-700">Preferred Vehicle Type</label>
                        <select id="vehicle_type" name="preferences[vehicle_type]" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-black focus:border-black sm:text-sm rounded-md">
                            <option value="any" {{ isset($passenger->ride_preferences['vehicle_type']) && $passenger->ride_preferences['vehicle_type'] == 'any' ? 'selected' : '' }}>Any</option>
                            <option value="share" {{ isset($passenger->ride_preferences['vehicle_type']) && $passenger->ride_preferences['vehicle_type'] == 'share' ? 'selected' : '' }}>Share</option>
                            <option value="comfort" {{ isset($passenger->ride_preferences['vehicle_type']) && $passenger->ride_preferences['vehicle_type'] == 'comfort' ? 'selected' : '' }}>Comfort</option>
                            <option value="women" {{ isset($passenger->ride_preferences['vehicle_type']) && $passenger->ride_preferences['vehicle_type'] == 'women' ? 'selected' : '' }}>Women Only</option>
                            <option value="wav" {{ isset($passenger->ride_preferences['vehicle_type']) && $passenger->ride_preferences['vehicle_type'] == 'wav' ? 'selected' : '' }}>Wheelchair Accessible</option>
                            <option value="black" {{ isset($passenger->ride_preferences['vehicle_type']) && $passenger->ride_preferences['vehicle_type'] == 'black' ? 'selected' : '' }}>Black (Premium)</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <div class="flex items-center">
                            <input id="women_only_rides" name="ride_preferences[women_only_rides]" type="checkbox" class="h-4 w-4 text-black focus:ring-black border-gray-300 rounded" {{ Auth::user()->women_only_rides ? 'checked' : '' }}>
                            <label for="women_only_rides" class="ml-2 block text-sm text-gray-700">
                                Women-Only Rides x
                            </label>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Only available for female passengers. You'll be matched with female drivers.</p>
                    </div>
                    
                    <div class="mb-4">
                        <div class="flex items-center">
                            <input id="quiet_ride" name="ride_preferences[quiet_ride]" type="checkbox" class="h-4 w-4 text-black focus:ring-black border-gray-300 rounded" {{ isset($passenger->ride_preferences['quiet_ride']) && $passenger->ride_preferences['quiet_ride'] ? 'checked' : '' }}>
                            <label for="quiet_ride" class="ml-2 block text-sm text-gray-700">
                                Quiet Ride
                            </label>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">You prefer minimal conversation during your ride.</p>
                    </div>
                    
                    <div class="mb-4">
                        <div class="flex items-center">
                            <input id="assistance_required" name="ride_preferences[assistance_required]" type="checkbox" class="h-4 w-4 text-black focus:ring-black border-gray-300 rounded" {{ isset($passenger->ride_preferences['assistance_required']) && $passenger->ride_preferences['assistance_required'] ? 'checked' : '' }}>
                            <label for="assistance_required" class="ml-2 block text-sm text-gray-700">
                                Driver Assistance Required
                            </label>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">You need the driver to assist with entry/exit or loading.</p>
                    </div>
                    
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-black text-base font-medium text-white hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black sm:col-start-2 sm:text-sm">
                            Save Preferences
                        </button>
                        <button type="button" id="cancel-preferences" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript for functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize map
            const map = L.map('map').setView([31.63, -8.0], 13); // Default center on Marrakech, Morocco
            
            // Add OpenStreetMap tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(map);
            
            // Variables for modals
            const rateRideModal = document.getElementById('rate-ride-modal');
            const rateRideForm = document.getElementById('rate-ride-form');
            const cancelRatingBtn = document.getElementById('cancel-rating');
            const ratingStars = document.querySelectorAll('.rating-star');
            const ratingValue = document.getElementById('rating-value');
            
            const preferencesModal = document.getElementById('preferences-modal');
            const editPreferencesBtn = document.getElementById('edit-preferences');
            const cancelPreferencesBtn = document.getElementById('cancel-preferences');
            
            // Variables for markers
            let userMarker = null;
            let driverMarker = null;
            let pickupMarker = null;
            let dropoffMarker = null;
            let routingControl = null;
            
            // Check for user location and center map
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    position => {
                        const userCoords = [position.coords.latitude, position.coords.longitude];
                        
                        // Add user location marker
                        userMarker = L.marker(userCoords).addTo(map);
                        userMarker.bindPopup('Your current location').openPopup();
                        
                        // Center map on user location
                        map.setView(userCoords, 14);
                        
                        // If there's an active ride, show pickup and dropoff markers and route
                        @if($activeRide && $activeRide->pickup_latitude && $activeRide->pickup_longitude && $activeRide->dropoff_latitude && $activeRide->dropoff_longitude)
                            showRideOnMap(
                                {{ $activeRide->pickup_latitude }}, 
                                {{ $activeRide->pickup_longitude }}, 
                                {{ $activeRide->dropoff_latitude }}, 
                                {{ $activeRide->dropoff_longitude }}, 
                                "{{ $activeRide->pickup_location }}", 
                                "{{ $activeRide->dropoff_location }}"
                            );
                            
                            // If driver is assigned and has location, show driver marker
                            @if($activeRide->driver && $activeRide->driver->driverLocation)
                                updateDriverMarker(
                                    {{ $activeRide->driver->driverLocation->latitude }},
                                    {{ $activeRide->driver->driverLocation->longitude }}
                                );
                            @endif
                        @endif
                    },
                    error => {
                        console.error("Error getting location:", error);
                        
                        // If geolocation fails, still show ride if active
                        @if($activeRide && $activeRide->pickup_latitude && $activeRide->pickup_longitude && $activeRide->dropoff_latitude && $activeRide->dropoff_longitude)
                            showRideOnMap(
                                {{ $activeRide->pickup_latitude }}, 
                                {{ $activeRide->pickup_longitude }}, 
                                {{ $activeRide->dropoff_latitude }}, 
                                {{ $activeRide->dropoff_longitude }}, 
                                "{{ $activeRide->pickup_location }}", 
                                "{{ $activeRide->dropoff_location }}"
                            );
                        @endif
                    }
                );
            }
            
            // Function to show ride on map
            function showRideOnMap(pickupLat, pickupLng, dropoffLat, dropoffLng, pickupName, dropoffName) {
                const pickupCoords = [pickupLat, pickupLng];
                const dropoffCoords = [dropoffLat, dropoffLng];
                
                // Add pickup marker
                pickupMarker = L.marker(pickupCoords, {
                    alt: 'Pickup Location'
                }).addTo(map);
                pickupMarker.bindPopup(`<strong>Pickup</strong><br>${pickupName}`);
                
                // Add dropoff marker
                dropoffMarker = L.marker(dropoffCoords, {
                    alt: 'Dropoff Location'
                }).addTo(map);
                dropoffMarker.bindPopup(`<strong>Dropoff</strong><br>${dropoffName}`);
                
                // Create a bounds object and fit the map to show all markers
                const bounds = L.latLngBounds(pickupCoords, dropoffCoords);
                map.fitBounds(bounds, { padding: [50, 50] });
                
                // Add routing between pickup and dropoff
                routingControl = L.Routing.control({
                    waypoints: [
                        L.latLng(pickupCoords[0], pickupCoords[1]),
                        L.latLng(dropoffCoords[0], dropoffCoords[1])
                    ],
                    routeWhileDragging: false,
                    showAlternatives: false,
                    fitSelectedRoutes: false,
                    lineOptions: {
                        styles: [
                            { color: '#6366F1', opacity: 0.8, weight: 6 },
                            { color: '#4F46E5', opacity: 0.5, weight: 2 }
                        ]
                    },
                    createMarker: function() { return null; } // Don't create default markers
                }).addTo(map);
                
                // Minimize the control sidebar
                routingControl.hide();
            }
            
            // Function to update driver marker on map
            function updateDriverMarker(lat, lng) {
                if (!driverMarker) {
                    // Create a custom icon for the driver
                    const driverIcon = L.divIcon({
                        className: 'custom-driver-marker',
                        iconSize: [24, 24],
                        iconAnchor: [12, 12],
                        popupAnchor: [0, -12]
                    });
                    
                    driverMarker = L.marker([lat, lng], {
                        icon: driverIcon,
                        alt: 'Driver Location'
                    }).addTo(map);
                    driverMarker.bindPopup('Driver Location').openPopup();
                } else {
                    driverMarker.setLatLng([lat, lng]);
                }
            }
            
            // Open rate ride modal function
            window.openRateRideModal = function(rideId) {
                if (rateRideForm) {
                    rateRideForm.action = `{{ url('passenger/ride') }}/${rideId}/rate`;
                    rateRideModal.classList.remove('hidden');
                }
            }
            
            // Star rating functionality
            if (ratingStars && ratingValue) {
                ratingStars.forEach(star => {
                    star.addEventListener('click', function() {
                        const value = this.getAttribute('data-value');
                        ratingValue.value = value;
                        
                        // Update star visuals
                        ratingStars.forEach(s => {
                            const starValue = s.getAttribute('data-value');
                            const svg = s.querySelector('svg');
                            if (starValue <= value) {
                                svg.classList.add('text-yellow-400');
                                svg.classList.remove('text-gray-300');
                            } else {
                                svg.classList.add('text-gray-300');
                                svg.classList.remove('text-yellow-400');
                            }
                        });
                    });
                });
            }
            
            // Cancel rating button
            if (cancelRatingBtn) {
                cancelRatingBtn.addEventListener('click', function() {
                    rateRideModal.classList.add('hidden');
                });
            }
            
            // Edit preferences button
            if (editPreferencesBtn) {
                editPreferencesBtn.addEventListener('click', function() {
                    preferencesModal.classList.remove('hidden');
                });
            }
            
            // Cancel preferences button
            if (cancelPreferencesBtn) {
                cancelPreferencesBtn.addEventListener('click', function() {
                    preferencesModal.classList.add('hidden');
                });
            }
            
            // Close modals when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target === rateRideModal) {
                    rateRideModal.classList.add('hidden');
                }
                if (event.target === preferencesModal) {
                    preferencesModal.classList.add('hidden');
                }
            });
            
            // Add CSS for driver marker
            const style = document.createElement('style');
            style.innerHTML = `
                .custom-driver-marker {
                    background-color: rgb(59, 130, 246);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    height: 24px;
                    width: 24px;
                    border-radius: 50%;
                    box-shadow: rgba(0, 0, 0, 0.16) 0px 4px 16px;
                    position: relative;
                }
                
                .custom-driver-marker::after {
                    content: "";
                    background-color: rgb(255, 255, 255);
                    height: 6px;
                    width: 6px;
                    border-radius: 50%;
                    position: absolute;
                }
            `;
            document.head.appendChild(style);
            
            // Update driver location periodically for active rides
            @if($activeRide && $activeRide->driver && $activeRide->reservation_status == 'accepted' && !$activeRide->dropoff_time)
            setInterval(function() {
                fetch(`/api/driver-location/{{ $activeRide->driver->id }}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.location) {
                            updateDriverMarker(data.location.latitude, data.location.longitude);
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching driver location:', error);
                    });
            }, 5000); // Update every 5 seconds
            @endif
            
            // Refresh map when the window is resized
            window.addEventListener('resize', function() {
                map.invalidateSize();
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
    // Create a debug panel
    const debugPanel = document.createElement('div');
    debugPanel.style.position = 'fixed';
    debugPanel.style.bottom = '10px';
    debugPanel.style.right = '10px';
    debugPanel.style.backgroundColor = '#f8f9fa';
    debugPanel.style.padding = '15px';
    debugPanel.style.borderRadius = '5px';
    debugPanel.style.boxShadow = '0 0 10px rgba(0,0,0,0.1)';
    debugPanel.style.zIndex = '9999';
    debugPanel.style.minWidth = '300px';
    
    // Add title
    const title = document.createElement('h4');
    title.textContent = 'Passenger Debug Tools';
    title.style.marginBottom = '10px';
    debugPanel.appendChild(title);
    
    // Add clear session button
    const clearSessionButton = document.createElement('button');
    clearSessionButton.textContent = 'Clear Session Data';
    clearSessionButton.className = 'btn btn-warning btn-sm';
    clearSessionButton.style.marginRight = '10px';
    clearSessionButton.style.marginBottom = '10px';
    clearSessionButton.addEventListener('click', function() {
        fetch('/passenger/clear-session', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            alert('Session cleared: ' + data.message);
            console.log('Session cleared:', data);
        })
        .catch(error => {
            console.error('Error clearing session:', error);
        });
    });
    debugPanel.appendChild(clearSessionButton);
    
    // Add a refresh location button to force location refresh
    const refreshLocationButton = document.createElement('button');
    refreshLocationButton.textContent = 'Refresh Nearby Drivers';
    refreshLocationButton.className = 'btn btn-primary btn-sm';
    refreshLocationButton.style.marginBottom = '10px';
    refreshLocationButton.addEventListener('click', function() {
        // Get current location or use default test location
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    window.location.href = `/passenger/nearby-drivers?latitude=${lat}&longitude=${lng}`;
                },
                function(error) {
                    console.error('Geolocation error:', error);
                    alert('Could not get your location. Using default location.');
                    // Use a default location as fallback
                    window.location.href = '/passenger/nearby-drivers?latitude=32.2603695&longitude=-9.2453128';
                }
            );
        } else {
            alert('Geolocation is not supported by this browser. Using default location.');
            window.location.href = '/passenger/nearby-drivers?latitude=32.2603695&longitude=-9.2453128';
        }
    });
    debugPanel.appendChild(refreshLocationButton);
    
    // Add location status
    const locationStatus = document.createElement('div');
    locationStatus.style.fontSize = '12px';
    locationStatus.style.marginTop = '10px';
    locationStatus.textContent = 'Location: Getting status...';
    debugPanel.appendChild(locationStatus);
    
    // Add session info
    const sessionInfo = document.createElement('div');
    sessionInfo.style.fontSize = '12px';
    sessionInfo.style.marginTop = '5px'; 
    sessionInfo.textContent = 'Session: Checking...';
    debugPanel.appendChild(sessionInfo);
    
    // Add to body
    document.body.appendChild(debugPanel);
    
    // Check for geolocation support
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                locationStatus.textContent = `Location: ${position.coords.latitude.toFixed(6)}, ${position.coords.longitude.toFixed(6)}`;
            },
            function(error) {
                locationStatus.textContent = `Location error: ${error.message}`;
            }
        );
    } else {
        locationStatus.textContent = 'Location: Geolocation not supported';
    }
    
    // Check session status
    fetch('/api/server-time')
        .then(response => response.json())
        .then(data => {
            sessionInfo.textContent = `Server time: ${new Date(data.server_time).toLocaleString()}`;
        })
        .catch(error => {
            sessionInfo.textContent = 'Session: Error checking server';
            console.error('Error fetching server time:', error);
        });
});

// Add this to driver debugging page to force location update
function forceLocationUpdate() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                
                fetch('/driver/force-location-refresh', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        latitude: lat,
                        longitude: lng
                    })
                })
                .then(response => response.json())
                .then(data => {
                    alert('Location forcefully updated: ' + data.message);
                    console.log('Location update:', data);
                    // Reload the page to see the changes
                    window.location.reload();
                })
                .catch(error => {
                    console.error('Error updating location:', error);
                    alert('Error updating location: ' + error);
                });
            },
            function(error) {
                console.error('Geolocation error:', error);
                alert('Could not get your location. Error: ' + error.message);
            }
        );
    } else {
        alert('Geolocation is not supported by this browser.');
    }
}
document.addEventListener('DOMContentLoaded', function() {
    const openModalBtn = document.getElementById('open-preferences-modal');
    const modal = document.getElementById('preferences-modal');
    const closeModalBtn = document.getElementById('close-preferences-modal');
    const modalBackdrop = document.getElementById('modal-backdrop');
    const saveBtn = document.getElementById('save-preferences-btn');
    const form = document.getElementById('preferences-form');
    
    // Toggle buttons
    const womenOnlyToggle = document.getElementById('women-only-toggle');
    const womenOnlyDot = document.getElementById('women-only-toggle-dot');
    const womenOnlyInput = document.getElementById('women_only_rides');
    
    const quietRideToggle = document.getElementById('quiet-ride-toggle');
    const quietRideDot = document.getElementById('quiet-ride-toggle-dot');
    const quietRideInput = document.getElementById('quiet_ride');
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Open modal
    if (openModalBtn) {
        openModalBtn.addEventListener('click', function() {
            modal.classList.remove('hidden');
        });
    }
    
    // Close modal
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', function() {
            modal.classList.add('hidden');
        });
    }
    
    // Close modal on outside click
    if (modalBackdrop) {
        modalBackdrop.addEventListener('click', function() {
            modal.classList.add('hidden');
        });
    }
    
    // Toggle women-only rides
    if (womenOnlyToggle) {
        womenOnlyToggle.addEventListener('click', function() {
            const isEnabled = womenOnlyToggle.classList.contains('bg-pink-500');
            
            // Update UI
            if (isEnabled) {
                womenOnlyToggle.classList.remove('bg-pink-500');
                womenOnlyToggle.classList.add('bg-gray-300');
                womenOnlyDot.classList.remove('translate-x-5');
                womenOnlyDot.classList.add('translate-x-1');
                womenOnlyInput.value = '0';
            } else {
                womenOnlyToggle.classList.remove('bg-gray-300');
                womenOnlyToggle.classList.add('bg-pink-500');
                womenOnlyDot.classList.remove('translate-x-1');
                womenOnlyDot.classList.add('translate-x-5');
                womenOnlyInput.value = '1';
            }
        });
    }
    
    // Toggle quiet ride
    if (quietRideToggle) {
        quietRideToggle.addEventListener('click', function() {
            const isEnabled = quietRideToggle.classList.contains('bg-blue-500');
            
            // Update UI
            if (isEnabled) {
                quietRideToggle.classList.remove('bg-blue-500');
                quietRideToggle.classList.add('bg-gray-300');
                quietRideDot.classList.remove('translate-x-5');
                quietRideDot.classList.add('translate-x-1');
                quietRideInput.value = '0';
            } else {
                quietRideToggle.classList.remove('bg-gray-300');
                quietRideToggle.classList.add('bg-blue-500');
                quietRideDot.classList.remove('translate-x-1');
                quietRideDot.classList.add('translate-x-5');
                quietRideInput.value = '1';
            }
        });
    }
    
    // Save preferences
    if (saveBtn) {
        saveBtn.addEventListener('click', function() {
            // Get form data
            const formData = new FormData(form);
            const preferences = {};
            
            // Convert form data to JSON object
            for (let entry of formData.entries()) {
                // Handle nested preferences object
                if (entry[0].startsWith('preferences[')) {
                    const key = entry[0].match(/\[([^\]]+)\]/)[1];
                    preferences[key] = entry[1];
                }
            }
            
            // Convert string values to appropriate types
            if (preferences.women_only_rides) {
                preferences.women_only_rides = preferences.women_only_rides === '1';
            }
            
            if (preferences.quiet_ride) {
                preferences.quiet_ride = preferences.quiet_ride === '1';
            }
            
            // Send AJAX request
            fetch('{{ route("passenger.save.preferences") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ preferences: preferences })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Show success message
                    showNotification('Preferences saved successfully', 'success');
                    
                    // Update summary in dashboard
                    updatePreferenceSummary(preferences);
                    
                    // Close modal
                    modal.classList.add('hidden');
                } else {
                    throw new Error(data.message || 'Failed to save preferences');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error saving preferences: ' + error.message, 'error');
            });
        });
    }
    
    // Function to show notification
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 px-4 py-2 rounded shadow-lg z-50 ${type === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`;
        notification.textContent = message;
        document.body.appendChild(notification);
        
        // Remove notification after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
    
    // Function to update preference summary in dashboard
    function updatePreferenceSummary(preferences) {
        const vehicleSummary = document.getElementById('preferred-vehicle-summary');
        if (vehicleSummary) {
            vehicleSummary.textContent = preferences.preferred_vehicle || 'Any';
        }
        
        const womenOnlySummary = document.getElementById('women-only-summary');
        if (womenOnlySummary && preferences.women_only_rides !== undefined) {
            if (preferences.women_only_rides) {
                womenOnlySummary.innerHTML = '<span class="text-pink-600">Enabled</span>';
            } else {
                womenOnlySummary.textContent = 'Disabled';
            }
        }
        
        const quietRideSummary = document.getElementById('quiet-ride-summary');
        if (quietRideSummary && preferences.quiet_ride !== undefined) {
            quietRideSummary.textContent = preferences.quiet_ride ? 'Enabled' : 'Disabled';
        }
    }
});
    </script>
</body>
</html>