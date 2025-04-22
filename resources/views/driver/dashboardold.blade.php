<!-- driver/dashboard.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>inTime - Driver Dashboard</title>
    
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
    <script src="{{ asset('views/driver/js/debug-timeout.js') }}"></script>

    <style>
        /* Custom styles for the dashboard */
        .driver-marker-inner {
            width: 24px;
            height: 24px;
            background-color: #3B82F6;
            border: 3px solid #EFF6FF;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        .pickup-marker-inner {
            width: 20px;
            height: 20px;
            background-color: #10B981;
            border: 2px solid white;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        .dropoff-marker-inner {
            width: 20px;
            height: 20px;
            background-color: #EF4444;
            border: 2px solid white;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        .map-container {
            position: relative;
            z-index: 10;
        }
        .rides-container {
            position: relative;
            z-index: 20;
        }
        .dashboard-tabs {
            position: relative;
            z-index: 30;
            background-color: white;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header/Navigation -->
    <header class="px-4 py-4 flex items-center justify-between bg-white shadow-sm sticky top-0 z-50">
        <!-- Logo and navigation -->
        <div class="flex items-center space-x-8">
            <!-- Logo -->
            <a href="{{ route('driver.dashboard') }}" class="text-2xl font-bold">inTime</a>
            
            <!-- Navigation Links -->
            <nav class="hidden md:flex space-x-6">
                <a href="{{ route('driver.awaiting.rides') }}" class="font-medium hover:text-blue-600 transition">Awaiting Rides</a>
                <a href="{{ route('driver.active.rides') }}" class="font-medium hover:text-blue-600 transition">Active Rides</a>
                <a href="{{ route('driver.history') }}" class="font-medium hover:text-blue-600 transition">History</a>
                <a href="{{ route('driver.earnings') }}" class="font-medium hover:text-blue-600 transition">Earnings</a>
                <a href="{{ route('driver.profile.private') }}" class="font-medium hover:text-blue-600 transition">My Profile</a>
            </nav>
        </div>
        
        <!-- Mobile Menu Button -->
        <button type="button" class="md:hidden p-2 rounded-md text-gray-700 hover:bg-gray-100" id="mobile-menu-button">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        
        <!-- User Profile -->
        <div class="flex items-center space-x-4">
            <!-- Online/Offline Status -->
            <div class="flex items-center bg-gray-100 rounded-full px-3 py-1">
                <span id="status-indicator" class="w-3 h-3 rounded-full {{ Auth::user()->is_online ? 'bg-green-500' : 'bg-red-500' }} mr-2"></span>
                <span id="status-text" class="text-sm font-medium">{{ Auth::user()->is_online ? 'Online' : 'Offline' }}</span>
            </div>
            
            <div class="relative">
                <button type="button" class="h-10 w-10 rounded-full bg-gray-300 overflow-hidden" id="profile-button">
                    @if(Auth::user()->profile_picture)
                        <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Profile" class="h-full w-full object-cover">
                    @else
                        <svg class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    @endif
                </button>
                
                <!-- Profile Dropdown -->
                <div class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50" id="profile-dropdown">
                    <a href="{{ route('driver.profile.private') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile Settings</a>
                    <a href="{{ route('driver.reviews') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My Reviews</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                            {{ __('Log Out') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Mobile Navigation Menu (Hidden by default) -->
    <div class="fixed inset-0 flex z-40 md:hidden transform translate-x-full transition-transform duration-300 ease-in-out" id="mobile-menu">
        <div class="relative flex-1 flex flex-col max-w-xs w-full bg-white">
            <div class="px-4 pt-5 pb-4">
                <div class="flex items-center justify-between">
                    <div class="text-2xl font-bold">inTime</div>
                    <button type="button" class="rounded-md text-gray-400 hover:text-gray-500 focus:outline-none" id="close-mobile-menu">
                        <span class="sr-only">Close menu</span>
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <div class="mt-6">
                    <nav class="grid gap-y-4">
                        <a href="{{ route('driver.dashboard') }}" class="font-medium px-3 py-2 rounded-md hover:bg-gray-100">Dashboard</a>
                        <a href="{{ route('driver.awaiting.rides') }}" class="font-medium px-3 py-2 rounded-md hover:bg-gray-100">Awaiting Rides</a>
                        <a href="{{ route('driver.active.rides') }}" class="font-medium px-3 py-2 rounded-md hover:bg-gray-100">Active Rides</a>
                        <a href="{{ route('driver.history') }}" class="font-medium px-3 py-2 rounded-md hover:bg-gray-100">History</a>
                        <a href="{{ route('driver.earnings') }}" class="font-medium px-3 py-2 rounded-md hover:bg-gray-100">Earnings</a>
                        <a href="{{ route('driver.profile.private') }}" class="font-medium px-3 py-2 rounded-md hover:bg-gray-100">Profile Settings</a>
                        <a href="{{ route('driver.reviews') }}" class="font-medium px-3 py-2 rounded-md hover:bg-gray-100">My Reviews</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left font-medium px-3 py-2 rounded-md text-red-600 hover:bg-gray-100">
                                {{ __('Log Out') }}
                            </button>
                        </form>
                    </nav>
                </div>
            </div>
        </div>
    </div>

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
        
        @if(session('warning'))
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6 rounded shadow" role="alert">
                <p>{{ session('warning') }}</p>
            </div>
        @endif
        
        <!-- Dashboard Tabs -->
        <div class="dashboard-tabs mb-6 bg-white rounded-lg shadow-md overflow-hidden">
            <div class="flex border-b">
                <button class="tab-button flex-1 py-3 px-4 font-medium border-b-2 border-blue-500 text-blue-500" data-tab="overview">Overview</button>
                <button class="tab-button flex-1 py-3 px-4 font-medium border-b-2 border-transparent hover:text-blue-500" data-tab="en-route">En Route ({{ count($enRouteRides ?? []) }})</button>
                <button class="tab-button flex-1 py-3 px-4 font-medium border-b-2 border-transparent hover:text-blue-500" data-tab="in-progress">In Progress ({{ count($inProgressRides ?? []) }})</button>
            </div>
        </div>
        
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Map and Control Column - Always visible -->
            <div class="w-full lg:w-1/2 flex flex-col gap-6">
                <!-- Map Container -->
                <div class="map-container bg-white rounded-lg shadow-md overflow-hidden">
                <div class="h-80 w-full" id="map"></div>


                </div>
                
                <!-- Online/Offline Toggle and Location Sharing -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex flex-col space-y-4">
                        <div>
                            <h2 class="text-xl font-bold">Driver Status</h2>
                            <p class="text-gray-600">Toggle your availability to receive ride requests</p>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <!-- Online/Offline Toggle Button -->
                            <div class="flex items-center">
                                <button id="toggle-status" class="relative inline-flex h-6 w-11 items-center rounded-full {{ Auth::user()->is_online ? 'bg-green-500' : 'bg-gray-300' }} transition-colors duration-300">
                                    <span id="toggle-circle" class="inline-block h-5 w-5 transform rounded-full bg-white shadow-md transition-transform duration-300 {{ Auth::user()->is_online ? 'translate-x-5' : 'translate-x-1' }}"></span>
                                </button>
                                <span id="toggle-text" class="ml-2 font-medium">{{ Auth::user()->is_online ? 'Go Offline' : 'Go Online' }}</span>
                            </div>
                            
                            <!-- Location Sharing Button -->
                            <div id="location-sharing-container" class="{{ Auth::user()->is_online ? '' : 'hidden' }}">
                                <button id="share-location" class="bg-blue-600 text-white py-2 px-4 rounded-md font-medium hover:bg-blue-700 transition {{ Auth::user()->is_online ? '' : 'opacity-50 cursor-not-allowed' }}" {{ Auth::user()->is_online ? '' : 'disabled' }}>
                                    <span id="location-status">Share Location</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Location tracking status info -->
                    <div id="location-info" class="hidden mt-4 bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    Your location is being shared. Passengers can now see your position on the map.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Driver Visibility Status -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold mb-4">Driver Visibility Status</h2>
                    
                    <div id="visibility-status" class="p-3 mb-4 rounded-md bg-yellow-50 text-yellow-700">
                        Loading visibility status...
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <span class="font-medium block">Online Status</span>
                            <span id="status-online" class="px-2 py-1 rounded-md text-xs font-medium bg-gray-200">Loading...</span>
                        </div>
                        <div>
                            <span class="font-medium block">Location Sharing</span>
                            <span id="status-location" class="px-2 py-1 rounded-md text-xs font-medium bg-gray-200">Loading...</span>
                        </div>
                        <div>
                            <span class="font-medium block">Account Status</span>
                            <span id="status-account" class="px-2 py-1 rounded-md text-xs font-medium bg-gray-200">Loading...</span>
                        </div>
                        <div>
                            <span class="font-medium block">Vehicle Status</span>
                            <span id="status-vehicle" class="px-2 py-1 rounded-md text-xs font-medium bg-gray-200">Loading...</span>
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center text-sm">
                        <div>
                            <span class="text-gray-500">Last location update:</span>
                            <span id="location-timestamp" class="font-medium">Never</span>
                        </div>
                        <button id="debug-status" class="text-blue-600 px-3 py-1 rounded-md hover:bg-blue-50">Debug Status</button>
                    </div>
                    
                    <div id="location-error" class="mt-3 hidden p-3 rounded-md bg-red-50 text-red-700"></div>
                    <div id="debug-info" class="mt-4 hidden"></div>
                </div>
            </div>
            
            <!-- Tab Content Area -->
            <div class="w-full lg:w-1/2 rides-container">
                <!-- "Overview" Tab Content (default view) -->
                <div id="overview-tab" class="tab-content active space-y-6">
                    <!-- Incoming Ride Requests -->
                    @if(count($rideRequests ?? []) > 0)
                        <div class="bg-white rounded-lg shadow-md p-6 animate-pulse">
                            <h2 class="text-xl font-bold mb-4 flex items-center text-blue-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                New Ride Request
                            </h2>
                            
                            @foreach($rideRequests as $request)
                                <div class="border-l-4 border-blue-500 pl-4 py-4 mb-4 bg-blue-50 rounded-r-md">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <h3 class="font-medium">{{ $request->ride->passenger->user->name }}
                                                @if($request->ride->passenger->user->gender === 'female' && $request->ride->passenger->user->women_only_rides)
                                                    <span class="inline-flex items-center ml-1 px-1.5 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800">
                                                        Women Only
                                                    </span>
                                                @endif
                                            </h3>
                                            <p class="text-sm text-gray-500">Requested {{ \Carbon\Carbon::parse($request->requested_at)->diffForHumans() }}</p>
                                        </div>
                                        
                                        <!-- Timer -->
                                        <div class="text-sm text-red-600 font-semibold countdown" 
                                             data-requested="{{ $request->requested_at }}" 
                                             data-request-id="{{ $request->id }}">
                                            45s
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-start mb-3">
                                        <div class="mr-3 text-gray-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium">{{ $request->ride->pickup_location }}</p>
                                            <p class="text-xs text-gray-500">
                                                @if(isset($request->ride->distance_in_km))
                                                    {{ number_format($request->ride->distance_in_km, 1) }} km away
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center text-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="font-medium">
                                                MAD {{ number_format($request->ride->price ?? $request->ride->ride_cost ?? 0, 2) }}
                                            </span>
                                        </div>
                                        
                                        <div class="flex items-center text-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span>
                                                {{ $request->ride->vehicle_type ? ucfirst($request->ride->vehicle_type) : (isset($driver->vehicle) ? ucfirst($driver->vehicle->type) : 'N/A') }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="flex space-x-3">
                                        <button type="button" class="accept-request bg-green-500 text-white py-2 px-6 rounded-md font-medium hover:bg-green-600 transition w-1/2"
                                                data-request-id="{{ $request->id }}">
                                            Accept
                                        </button>
                                        
                                        <button type="button" class="reject-request border border-gray-300 text-gray-700 py-2 px-6 rounded-md font-medium hover:bg-gray-50 transition w-1/2"
                                                data-request-id="{{ $request->id }}">
                                            Decline
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    
                    <!-- Active/Current Ride -->
                    @if(count($activeRides ?? []) > 0)
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h2 class="text-xl font-bold mb-4">Current Ride</h2>
                            
                            @foreach($activeRides as $ride)
                                <div class="border-l-4 border-green-500 pl-4 py-2">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <h3 class="font-medium">
                                                Ride with {{ $ride->passenger->user->name }}
                                                @if($ride->passenger->user->gender === 'female' && $ride->passenger->user->women_only_rides)
                                                    <span class="inline-flex items-center ml-1 px-1.5 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800">
                                                        Women Only
                                                    </span>
                                                @endif
                                            </h3>
                                            <p class="text-sm text-gray-500">{{ $ride->pickup_time ? 'Started: ' . $ride->pickup_time->format('g:i A') : 'Not started yet' }}</p>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ $ride->pickup_time ? 'In Progress' : 'En Route' }}
                                        </span>
                                    </div>
                                    
                                    <div class="space-y-3 mb-4">
                                        <div class="flex items-start">
                                            <div class="mt-1 mr-3">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m-8 6H4m0 0l4 4m-4-4l4-4" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium">Vehicle Type</p>
                                                <p class="text-sm text-gray-600">{{ ucfirst($ride->vehicle_type) }}</p>
                                            </div>
                                        </div>
                                        
                                        @if($ride->distance_in_km)
                                        <div class="flex items-start">
                                            <div class="mt-1 mr-3">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium">Distance</p>
                                                <p class="text-sm text-gray-600">{{ number_format($ride->distance_in_km, 1) }} km</p>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    
                                    <div class="flex space-x-3">
                                        @if(!$ride->pickup_time)
                                            <button type="button" class="start-ride bg-green-500 text-white py-2 px-4 rounded-md font-medium hover:bg-green-600 transition flex-1"
                                                    data-ride-id="{{ $ride->id }}">
                                                Start Ride
                                            </button>
                                        @elseif(!$ride->dropoff_time)
                                            <button type="button" class="complete-ride bg-blue-500 text-white py-2 px-4 rounded-md font-medium hover:bg-blue-600 transition flex-1"
                                                    data-ride-id="{{ $ride->id }}">
                                                Complete Ride
                                            </button>
                                        @endif
                                        
                                        <button type="button" class="show-route border border-gray-300 text-gray-700 py-2 px-4 rounded-md font-medium hover:bg-gray-50 transition"
                                                data-pickup-lat="{{ $ride->pickup_latitude }}"
                                                data-pickup-lng="{{ $ride->pickup_longitude }}"
                                                data-dropoff-lat="{{ $ride->dropoff_latitude }}"
                                                data-dropoff-lng="{{ $ride->dropoff_longitude }}">
                                            Show Route
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    
                    <!-- Driver Stats -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold mb-4">Your Stats</h2>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Completed Rides</span>
                                <span class="font-medium">{{ $stats['completed_rides'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Rating</span>
                                <div class="flex items-center">
                                    <span class="font-medium mr-1">{{ number_format($stats['rating'], 1) }}</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Total Income</span>
                                <span class="font-medium">MAD {{ number_format($stats['total_income'], 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Response Time</span>
                                <span class="font-medium">{{ $driver->driver_response_time ? number_format($driver->driver_response_time, 1) . 's' : 'N/A' }}</span>
                            </div>
                        </div>

                        <!-- Women-Only Driver Toggle (only for female drivers) -->
                        @if(Auth::user()->gender === 'female')
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <h3 class="font-medium mb-2">Women-Only Driver Settings</h3>
                            
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <button id="toggle-women-only" class="relative inline-flex h-6 w-11 items-center rounded-full {{ $driver->women_only_driver ? 'bg-pink-500' : 'bg-gray-300' }} transition-colors duration-300">
                                        <span id="women-only-circle" class="inline-block h-5 w-5 transform rounded-full bg-white shadow-md transition-transform duration-300 {{ $driver->women_only_driver ? 'translate-x-5' : 'translate-x-1' }}"></span>
                                    </button>
                                    <span id="women-only-text" class="ml-2 font-medium">{{ $driver->women_only_driver ? 'Women-Only Mode On' : 'Women-Only Mode Off' }}</span>
                                </div>
                                
                                <div class="px-2 py-1 rounded bg-pink-100 text-pink-800 text-xs">
                                    <span>Female Only</span>
                                </div>
                            </div>
                            
                            <p class="text-sm text-gray-500 mb-2">When enabled, you'll only receive ride requests from female passengers.</p>
                            
                            @if($driver->women_only_driver && $driver->vehicle && $driver->vehicle->type !== 'women')
                                <div class="mt-2 bg-yellow-100 text-yellow-700 p-3 rounded-md text-sm">
                                    <span class="font-medium">Note:</span> For consistent matching, consider updating your vehicle type to "Women" in vehicle settings.
                                </div>
                            @endif
                        </div>
                        @endif
                    </div>
                    
                    <!-- Vehicle Info -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold mb-4">Vehicle</h2>
                        <div class="flex items-center space-x-4 mb-4">
                            <div class="h-16 w-16 bg-gray-200 rounded-md overflow-hidden">
                                @if($driver->vehicle && $driver->vehicle->vehicle_photo)
                                    <img src="{{ asset('storage/' . $driver->vehicle->vehicle_photo) }}" alt="Vehicle" class="h-full w-full object-cover">
                                @else
                                    <div class="h-full w-full flex items-center justify-center text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m-8 6H4m0 0l4 4m-4-4l4-4" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div>
                                @if($driver->vehicle)
                                    <p class="font-medium">{{ $driver->vehicle->make }} {{ $driver->vehicle->model }}</p>
                                    <p class="text-gray-600">{{ $driver->vehicle->year }} · {{ $driver->vehicle->color }}</p>
                                    <p class="text-sm text-gray-500">{{ $driver->vehicle->plate_number }}</p>
                                @else
                                    <p class="text-gray-600">No vehicle information available</p>
                                    <a href="{{ route('driver.profile.private') }}#vehicle-section" class="text-blue-600 hover:text-blue-800 text-sm hover:underline">
                                        Add Vehicle
                                    </a>
                                @endif
                            </div>
                        </div>
                        
                        @if($driver->vehicle && $driver->vehicle->type)
                            <div class="flex justify-between items-center">
                                <div class="bg-gray-100 px-3 py-2 rounded-md 
                                    @if($driver->vehicle->type === 'women')
                                        border-l-4 border-l-pink-500
                                    @endif">
                                    <span class="text-sm font-medium">
                                        @if($driver->vehicle->type === 'women')
                                            <span class="text-pink-700">Women</span>
                                        @else
                                            {{ ucfirst($driver->vehicle->type) }}
                                        @endif
                                        Vehicle
                                    </span>
                                </div>
                            </div>
                            
                            @if($driver->vehicle->type === 'women' && !($driver->women_only_driver && Auth::user()->gender === 'female'))
                                <div class="mt-3 bg-red-100 text-red-700 p-3 rounded-md text-sm">
                                    <span class="font-medium">Note:</span> Women-only vehicle type requires a female driver with women-only preference enabled.
                                </div>
                            @endif
                        @endif

                        @if($driver->vehicle && $driver->vehicle->features && $driver->vehicle->features->count() > 0)
                        <div class="mt-4 border-t border-gray-100 pt-4">
                            <h3 class="text-sm font-medium text-gray-700 mb-2">Vehicle Features</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($driver->vehicle->features as $feature)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ ucfirst(str_replace('_', ' ', $feature->feature)) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- "En Route" Tab Content -->
                <div id="en-route-tab" class="tab-content space-y-6">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold mb-4">En Route to Pickup</h2>
                        
                        @if(empty($enRouteRides) || count($enRouteRides) === 0)
                            <div class="bg-gray-50 rounded-md p-6 text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-gray-500 font-medium">You don't have any rides en route</p>
                                <p class="text-sm text-gray-400 mt-1">When you accept a ride, it will appear here</p>
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach($enRouteRides as $ride)
                                    <div class="border rounded-md p-4 hover:border-blue-200 transition-colors duration-200 border-l-4 border-l-blue-500">
                                        <div class="flex justify-between items-start mb-3">
                                            <div>
                                                <div class="flex items-center space-x-3">
                                                    <div class="h-10 w-10 rounded-full bg-gray-200 overflow-hidden">
                                                        @if($ride->passenger->user->profile_picture)
                                                            <img src="{{ asset('storage/' . $ride->passenger->user->profile_picture) }}" alt="Passenger" class="h-full w-full object-cover">
                                                        @else
                                                            <div class="h-full w-full flex items-center justify-center text-gray-500 bg-gray-300">
                                                                {{ substr($ride->passenger->user->name, 0, 1) }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <p class="font-medium">{{ $ride->passenger->user->name }}</p>
                                                        <div class="flex items-center">
                                                            @if($ride->passenger->rating)
                                                                <div class="flex items-center text-sm">
                                                                    <span class="mr-1">{{ number_format($ride->passenger->rating, 1) }}</span>
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                                    </svg>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <p class="text-sm text-gray-500 mt-1">Reserved: {{ $ride->reservation_date->format('M d, Y g:i A') }}</p>
                                            </div>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                En Route
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
                                                    <p class="text-sm text-gray-600">{{ $ride->pickup_location }}</p>
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
                                                    <p class="text-sm text-gray-600">{{ $ride->dropoff_location }}</p>
                                                </div>
                                            </div>
                                            
                                            <div class="flex justify-between items-center mt-2">
                                                <div class="text-sm">
                                                    <span class="font-medium">MAD {{ number_format($ride->price ?? $ride->ride_cost ?? 0, 2) }}</span>
                                                    @if($ride->vehicle_type)
                                                        <span class="text-xs text-gray-500 ml-2">{{ ucfirst($ride->vehicle_type) }}</span>
                                                    @endif
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    @if(isset($ride->distance_in_km))
                                                        {{ number_format($ride->distance_in_km, 1) }} km
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="flex flex-wrap gap-3">
                                            <button type="button" class="start-ride-btn bg-green-500 text-white py-2 px-4 rounded-md text-sm font-medium hover:bg-green-600 transition flex-1"
                                                    data-ride-id="{{ $ride->id }}">
                                                Start Ride
                                            </button>
                                            
                                            <a href="tel:{{ $ride->passenger->user->phone }}" class="border border-gray-300 text-gray-700 py-2 px-4 rounded-md text-sm font-medium hover:bg-gray-50 transition flex items-center justify-center flex-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                                </svg>
                                                Call Passenger
                                            </a>
                                            
                                            <button type="button" class="navigation-btn border border-gray-300 text-gray-700 py-2 px-4 rounded-md text-sm font-medium hover:bg-gray-50 transition flex items-center justify-center"
                                                    data-pickup-lat="{{ $ride->pickup_latitude }}"
                                                    data-pickup-lng="{{ $ride->pickup_longitude }}"
                                                    data-dropoff-lat="{{ $ride->dropoff_latitude }}"
                                                    data-dropoff-lng="{{ $ride->dropoff_longitude }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M12 1.586l-4 4v12.828l4-4V1.586zM3.707 3.293A1 1 0 002 4v10a1 1 0 00.293.707L6 18.414V5.586L3.707 3.293zM17.707 5.293L14 1.586v12.828l2.293 2.293A1 1 0 0018 16V6a1 1 0 00-.293-.707z" clip-rule="evenodd" />
                                                </svg>
                                                Navigation
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- "In Progress" Tab Content -->
                <div id="in-progress-tab" class="tab-content space-y-6">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold mb-4">In Progress Rides</h2>
                        
                        @if(empty($inProgressRides) || count($inProgressRides) === 0)
                            <div class="bg-gray-50 rounded-md p-6 text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-gray-500 font-medium">You don't have any rides in progress</p>
                                <p class="text-sm text-gray-400 mt-1">When you start a ride, it will appear here</p>
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach($inProgressRides as $ride)
                                    <div class="border rounded-md p-4 hover:border-green-200 transition-colors duration-200 border-l-4 border-l-green-500">
                                        <div class="flex justify-between items-start mb-3">
                                            <div>
                                                <h3 class="font-medium">Ride with {{ $ride->passenger->user->name }}</h3>
                                                <p class="text-sm text-gray-500">Started: {{ $ride->pickup_time->format('M d, Y g:i A') }}</p>
                                            </div>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                In Progress
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
                                                    <p class="text-sm text-gray-600">{{ $ride->pickup_location }}</p>
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
                                                    <p class="text-sm text-gray-600">{{ $ride->dropoff_location }}</p>
                                                </div>
                                            </div>
                                            
                                            <div class="flex justify-between items-center mt-2">
                                                <div class="text-sm">
                                                    <span class="font-medium">MAD {{ number_format($ride->price ?? $ride->ride_cost ?? 0, 2) }}</span>
                                                    @if($ride->vehicle_type)
                                                        <span class="text-xs text-gray-500 ml-2">{{ ucfirst($ride->vehicle_type) }}</span>
                                                    @endif
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    @if(isset($ride->distance_in_km))
                                                        {{ number_format($ride->distance_in_km, 1) }} km
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="flex flex-wrap gap-3">
                                            <button type="button" class="complete-ride-btn bg-blue-600 text-white py-2 px-4 rounded-md text-sm font-medium hover:bg-blue-700 transition flex-1"
                                                    data-ride-id="{{ $ride->id }}">
                                                Complete Ride
                                            </button>
                                            
                                            <a href="tel:{{ $ride->passenger->user->phone }}" class="border border-gray-300 text-gray-700 py-2 px-4 rounded-md text-sm font-medium hover:bg-gray-50 transition flex items-center justify-center flex-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                                </svg>
                                                Call Passenger
                                            </a>
                                            
                                            <button type="button" class="navigation-btn border border-gray-300 text-gray-700 py-2 px-4 rounded-md text-sm font-medium hover:bg-gray-50 transition flex items-center justify-center"
                                                    data-pickup-lat="{{ $ride->pickup_latitude }}"
                                                    data-pickup-lng="{{ $ride->pickup_longitude }}"
                                                    data-dropoff-lat="{{ $ride->dropoff_latitude }}"
                                                    data-dropoff-lng="{{ $ride->dropoff_longitude }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M12 1.586l-4 4v12.828l4-4V1.586zM3.707 3.293A1 1 0 002 4v10a1 1 0 00.293.707L6 18.414V5.586L3.707 3.293zM17.707 5.293L14 1.586v12.828l2.293 2.293A1 1 0 0018 16V6a1 1 0 00-.293-.707z" clip-rule="evenodd" />
                                                </svg>
                                                Navigation
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Start Ride Modal -->
    <div id="start-ride-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            
            <div class="relative inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div>
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-5">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Start Ride</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Please confirm you have picked up the passenger and are ready to start the ride.
                            </p>
                        </div>
                    </div>
                </div>
                
                <form id="start-ride-form" class="mt-5">
                    <input type="hidden" id="start-ride-id-input" name="ride_id" value="">
                    <input type="hidden" id="start-latitude-input" name="latitude" value="">
                    <input type="hidden" id="start-longitude-input" name="longitude" value="">
                    
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-500 text-base font-medium text-white hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:col-start-2 sm:text-sm">
                            Start Ride
                        </button>
                        <button type="button" id="cancel-start-ride" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Complete Ride Modal -->
    <div id="complete-ride-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
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
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Complete Ride</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Please confirm you have reached the destination and want to complete this ride.
                            </p>
                        </div>
                    </div>
                </div>
                
                <form id="complete-ride-form" class="mt-5">
                    <input type="hidden" id="ride-id-input" name="ride_id" value="">
                    <input type="hidden" id="latitude-input" name="latitude" value="">
                    <input type="hidden" id="longitude-input" name="longitude" value="">
                    
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:col-start-2 sm:text-sm">
                            Complete Ride
                        </button>
                        <button type="button" id="cancel-complete-ride" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Response Modal -->
    <div id="ride-response-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            
            <div class="bg-white rounded-lg px-4 pt-5 pb-4 text-center overflow-hidden shadow-xl transform transition-all sm:max-w-sm sm:w-full sm:p-6">
                <div id="response-success" class="hidden">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                        <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-5">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Request Processed</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500" id="success-message"></p>
                        </div>
                    </div>
                </div>
                
                <div id="response-error" class="hidden">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-5">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Error</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500" id="error-message"></p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-5 sm:mt-6">
                    <button type="button" class="inline-flex justify-center w-full rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:text-sm" id="response-modal-close">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // CSRF Token for AJAX requests
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Initialize UI components
            initTabs();
            initUIComponents();
            
            // Initialize map and location tracking
            initMap();
            
            // Initialize debug status information
            initDebugStatus();
            
            // Set up heartbeat and auto-offline
            initHeartbeatAndOffline();
            
            // Initialize ride request handling
            initRideRequestHandling();
            
            // Initialize countdown timers
            initRequestTimers();

            // Initialize the modal handlers
            initModals();
            
            // Function to initialize tabs
            function initTabs() {
                const tabButtons = document.querySelectorAll('.tab-button');
                const tabContents = document.querySelectorAll('.tab-content');
                
                tabButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        // Remove active class from all buttons and tabs
                        tabButtons.forEach(btn => {
                            btn.classList.remove('border-blue-500', 'text-blue-500');
                            btn.classList.add('border-transparent', 'hover:text-blue-500');
                        });
                        
                        tabContents.forEach(content => {
                            content.classList.remove('active');
                        });
                        
                        // Add active class to clicked button and corresponding tab
                        this.classList.add('border-blue-500', 'text-blue-500');
                        this.classList.remove('border-transparent', 'hover:text-blue-500');
                        
                        const tabId = this.getAttribute('data-tab');
                        document.getElementById(tabId + '-tab').classList.add('active');
                    });
                });
            }
            
            // Function to initialize UI components and event listeners
            function initUIComponents() {
                // Mobile menu toggle
                const mobileMenu = document.getElementById('mobile-menu');
                const mobileMenuButton = document.getElementById('mobile-menu-button');
                const closeMobileMenu = document.getElementById('close-mobile-menu');
                
                if (mobileMenuButton) {
                    mobileMenuButton.addEventListener('click', function() {
                        mobileMenu.classList.remove('translate-x-full');
                    });
                }
                
                if (closeMobileMenu) {
                    closeMobileMenu.addEventListener('click', function() {
                        mobileMenu.classList.add('translate-x-full');
                    });
                }
                
                // Profile dropdown toggle
                const profileButton = document.getElementById('profile-button');
                const profileDropdown = document.getElementById('profile-dropdown');
                
                if (profileButton && profileDropdown) {
                    profileButton.addEventListener('click', function() {
                        profileDropdown.classList.toggle('hidden');
                    });
                    
                    // Close profile dropdown when clicking outside
                    document.addEventListener('click', function(event) {
                        if (!profileButton.contains(event.target) && !profileDropdown.contains(event.target)) {
                            profileDropdown.classList.add('hidden');
                        }
                    });
                }
                
                // Women-only toggle (for female drivers only)
                const toggleWomenOnly = document.getElementById('toggle-women-only');
                const womenOnlyCircle = document.getElementById('women-only-circle');
                const womenOnlyText = document.getElementById('women-only-text');
                
                if (toggleWomenOnly) {
                    toggleWomenOnly.addEventListener('click', function() {
                        const isEnabled = toggleWomenOnly.classList.contains('bg-pink-500');
                        
                        // Update UI first for responsiveness
                        if (isEnabled) {
                            // Turning off women-only mode
                            toggleWomenOnly.classList.remove('bg-pink-500');
                            toggleWomenOnly.classList.add('bg-gray-300');
                            womenOnlyCircle.classList.remove('translate-x-5');
                            womenOnlyCircle.classList.add('translate-x-1');
                            womenOnlyText.textContent = 'Women-Only Mode Off';
                        } else {
                            // Turning on women-only mode
                            toggleWomenOnly.classList.remove('bg-gray-300');
                            toggleWomenOnly.classList.add('bg-pink-500');
                            womenOnlyCircle.classList.remove('translate-x-1');
                            womenOnlyCircle.classList.add('translate-x-5');
                            womenOnlyText.textContent = 'Women-Only Mode On';
                        }
                        
                        // Make AJAX request to update women-only mode
                        fetch('/driver/toggle-women-only', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (!data.success) {
                                // Revert UI if there was an error
                                if (isEnabled) {
                                    toggleWomenOnly.classList.add('bg-pink-500');
                                    toggleWomenOnly.classList.remove('bg-gray-300');
                                    womenOnlyCircle.classList.add('translate-x-5');
                                    womenOnlyCircle.classList.remove('translate-x-1');
                                    womenOnlyText.textContent = 'Women-Only Mode On';
                                } else {
                                    toggleWomenOnly.classList.add('bg-gray-300');
                                    toggleWomenOnly.classList.remove('bg-pink-500');
                                    womenOnlyCircle.classList.add('translate-x-1');
                                    womenOnlyCircle.classList.remove('translate-x-5');
                                    womenOnlyText.textContent = 'Women-Only Mode Off';
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error updating women-only mode:', error);
                            // Revert UI on error
                            if (isEnabled) {
                                toggleWomenOnly.classList.add('bg-pink-500');
                                toggleWomenOnly.classList.remove('bg-gray-300');
                                womenOnlyCircle.classList.add('translate-x-5');
                                womenOnlyCircle.classList.remove('translate-x-1');
                                womenOnlyText.textContent = 'Women-Only Mode On';
                            } else {
                                toggleWomenOnly.classList.add('bg-gray-300');
                                toggleWomenOnly.classList.remove('bg-pink-500');
                                womenOnlyCircle.classList.add('translate-x-1');
                                womenOnlyCircle.classList.remove('translate-x-5');
                                womenOnlyText.textContent = 'Women-Only Mode Off';
                            }
                        });
                    });
                }
                
                // Online/Offline Toggle
                const toggleStatus = document.getElementById('toggle-status');
                const toggleCircle = document.getElementById('toggle-circle');
                const toggleText = document.getElementById('toggle-text');
                const locationSharingContainer = document.getElementById('location-sharing-container');
                const statusIndicator = document.getElementById('status-indicator');
                const statusText = document.getElementById('status-text');
                
                if (toggleStatus) {
                    toggleStatus.addEventListener('click', function() {
                        const isOnline = toggleStatus.classList.contains('bg-green-500');
                        
                        // Update UI first for a snappy response
                        if (isOnline) {
                            // Going offline
                            toggleStatus.classList.remove('bg-green-500');
                            toggleStatus.classList.add('bg-gray-300');
                            toggleCircle.classList.remove('translate-x-5');
                            toggleCircle.classList.add('translate-x-1');
                            toggleText.textContent = 'Go Online';
                            locationSharingContainer.classList.add('hidden');
                            statusIndicator.classList.remove('bg-green-500');
                            statusIndicator.classList.add('bg-red-500');
                            statusText.textContent = 'Offline';
                            
                            // Stop location tracking
                            if (typeof stopLocationTracking === 'function') {
                                stopLocationTracking();
                            }
                        } else {
                            // Going online
                            toggleStatus.classList.remove('bg-gray-300');
                            toggleStatus.classList.add('bg-green-500');
                            toggleCircle.classList.remove('translate-x-1');
                            toggleCircle.classList.add('translate-x-5');
                            toggleText.textContent = 'Go Offline';
                            locationSharingContainer.classList.remove('hidden');
                            statusIndicator.classList.remove('bg-red-500');
                            statusIndicator.classList.add('bg-green-500');
                            statusText.textContent = 'Online';
                            
                            // Start location tracking
                            if (typeof startLocationTracking === 'function') {
                                startLocationTracking();
                            }
                        }
                        
                        // Make AJAX request to update status
                        fetch('/driver/status', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({
                                is_online: !isOnline
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            // Update debug status after status change
                            updateDriverStatus();
                        })
                        .catch(error => {
                            console.error('Error updating status:', error);
                            // Revert UI if there was an error
                            if (isOnline) {
                                toggleStatus.classList.add('bg-green-500');
                                toggleStatus.classList.remove('bg-gray-300');
                                toggleCircle.classList.add('translate-x-5');
                                toggleCircle.classList.remove('translate-x-1');
                                toggleText.textContent = 'Go Offline';
                                locationSharingContainer.classList.remove('hidden');
                                statusIndicator.classList.add('bg-green-500');
                                statusIndicator.classList.remove('bg-red-500');
                                statusText.textContent = 'Online';
                            } else {
                                toggleStatus.classList.add('bg-gray-300');
                                toggleStatus.classList.remove('bg-green-500');
                                toggleCircle.classList.add('translate-x-1');
                                toggleCircle.classList.remove('translate-x-5');
                                toggleText.textContent = 'Go Online';
                                locationSharingContainer.classList.add('hidden');
                                statusIndicator.classList.add('bg-red-500');
                                statusIndicator.classList.remove('bg-green-500');
                                statusText.textContent = 'Offline';
                            }
                        });
                    });
                }
                
                // Share location button
                const shareLocationButton = document.getElementById('share-location');
                const locationInfo = document.getElementById('location-info');
                
                if (shareLocationButton) {
                    shareLocationButton.addEventListener('click', function() {
                        if (typeof startLocationTracking === 'function') {
                            startLocationTracking();
                        }
                    });
                }
            }
            
            // Variables for tracking
            let map = null;
            let driverMarker = null;
            let routingControl = null;
            let watchId = null;
            let locationIntervalId = null;
            let isTrackingLocation = false;
            let driverLocation = null;
            
            // Function to initialize map
            function initMap() {
                const mapContainer = document.getElementById('map');
                
                if (mapContainer) {
                    // Initialize the map
                    map = L.map('map').setView([31.6294, -7.9810], 13); // Default to Marrakech
                    
                    // Add OpenStreetMap tiles
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                        maxZoom: 19
                    }).addTo(map);
                    
                    // Try to center map on driver's current location
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(function(position) {
                            map.setView([position.coords.latitude, position.coords.longitude], 15);
                            
                            // Add marker for current position
                            driverMarker = L.marker([position.coords.latitude, position.coords.longitude], {
                                icon: L.divIcon({
                                    className: 'driver-marker',
                                    html: '<div class="driver-marker-inner"></div>',
                                    iconSize: [24, 24],
                                    iconAnchor: [12, 12]
                                })
                            })
                            .addTo(map)
                            .bindPopup('Your current location')
                            .openPopup();
                            
                            // Save location
                            driverLocation = {
                                lat: position.coords.latitude,
                                lng: position.coords.longitude
                            };
                            
                            // Make marker accessible globally
                            window.driverMarker = driverMarker;
                            window.map = map;
                        });
                    }
                    
                    // Handle show route button clicks
                    document.querySelectorAll('.show-route, .navigation-btn').forEach(button => {
                        button.addEventListener('click', function() {
                            const pickupLat = parseFloat(this.dataset.pickupLat);
                            const pickupLng = parseFloat(this.dataset.pickupLng);
                            const dropoffLat = parseFloat(this.dataset.dropoffLat);
                            const dropoffLng = parseFloat(this.dataset.dropoffLng);
                            
                            if (map && pickupLat && pickupLng && dropoffLat && dropoffLng) {
                                addRouteToMap([pickupLat, pickupLng], [dropoffLat, dropoffLng]);
                                
                                // Show the map if we're not in the overview tab
                                const overviewTabButton = document.querySelector('.tab-button[data-tab="overview"]');
                                if (overviewTabButton) {
                                    overviewTabButton.click();
                                }
                                
                                // Scroll to map
                                mapContainer.scrollIntoView({ behavior: 'smooth' });
                            } else {
                                console.error('Invalid coordinates or map not initialized');
                            }
                        });
                    });
                }
            }
            
            // Function to add route to map
            function addRouteToMap(pickupCoords, dropoffCoords) {
                // Clear existing route
                if (routingControl) {
                    map.removeControl(routingControl);
                }
                
                // Add pickup marker
                const pickupMarker = L.marker(pickupCoords, {
                    icon: L.divIcon({
                        className: 'pickup-marker',
                        html: '<div class="pickup-marker-inner"></div>',
                        iconSize: [20, 20],
                        iconAnchor: [10, 10]
                    })
                }).addTo(map);
                
                // Add dropoff marker
                const dropoffMarker = L.marker(dropoffCoords, {
                    icon: L.divIcon({
                        className: 'dropoff-marker',
                        html: '<div class="dropoff-marker-inner"></div>',
                        iconSize: [20, 20],
                        iconAnchor: [10, 10]
                    })
                }).addTo(map);
                
                // Create routing control
                routingControl = L.Routing.control({
                    waypoints: [
                        L.latLng(pickupCoords[0], pickupCoords[1]),
                        L.latLng(dropoffCoords[0], dropoffCoords[1])
                    ],
                    routeWhileDragging: false,
                    showAlternatives: false,
                    lineOptions: {
                        styles: [{color: '#3B82F6', weight: 5, opacity: 0.7}]
                    },
                    createMarker: function() { return null; } // Don't create default markers
                }).addTo(map);
                
                // Hide the routing control panel
                routingControl.hide();
                
                // Fit map to show route and markers
                const bounds = L.latLngBounds(pickupCoords, dropoffCoords);
                map.fitBounds(bounds, {padding: [50, 50]});
                
                // Store markers to remove later
                window.currentRouteMarkers = [pickupMarker, dropoffMarker];
            }
            
            // Function to start location tracking
            function startLocationTracking() {
                if (isTrackingLocation) return;
                
                if (navigator.geolocation) {
                    // High accuracy, force hardware sensors when available
                    const options = {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    };
                    
                    // Request position once first to check permission
                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            // Enable location sharing on success
                            const shareLocationBtn = document.getElementById('share-location');
                            const locationInfo = document.getElementById('location-info');
                            
                            if (shareLocationBtn) {
                                shareLocationBtn.textContent = 'Location Shared';
                                shareLocationBtn.classList.add('bg-green-600');
                                shareLocationBtn.classList.remove('bg-blue-600');
                            }
                            
                            if (locationInfo) {
                                locationInfo.classList.remove('hidden');
                            }
                            
                            // Send initial location to server
                            updateDriverLocation(position.coords.latitude, position.coords.longitude);
                            
                            // Update our saved location
                            driverLocation = {
                                lat: position.coords.latitude,
                                lng: position.coords.longitude
                            };
                            
                            // Then start continuous tracking
                            // Watch position (continuous updates)
                            watchId = navigator.geolocation.watchPosition(
                                function(position) {
                                    updateDriverLocation(position.coords.latitude, position.coords.longitude);
                                    
                                    driverLocation = {
                                        lat: position.coords.latitude,
                                        lng: position.coords.longitude
                                    };
                                },
                                function(error) {
                                    console.error('Error tracking location:', error);
                                },
                                options
                            );
                            
                            // Also set an interval as a fallback to ensure regular updates
                            locationIntervalId = setInterval(() => {
                                navigator.geolocation.getCurrentPosition(
                                    function(position) {
                                        updateDriverLocation(position.coords.latitude, position.coords.longitude);
                                        
                                        driverLocation = {
                                            lat: position.coords.latitude,
                                            lng: position.coords.longitude
                                        };
                                    },
                                    function(error) {
                                        console.error('Error getting location:', error);
                                    },
                                    options
                                );
                            }, 30000); // Update every 30 seconds as fallback
                            
                            isTrackingLocation = true;
                        },
                        function(error) {
                            // Handle geolocation error
                            console.error('Error obtaining location:', error);
                            alert('Location sharing is required to go online. Please enable location services.');
                            
                            // Update location error message
                            const errorElement = document.getElementById('location-error');
                            if (errorElement) {
                                let errorMessage;
                                switch(error.code) {
                                    case error.PERMISSION_DENIED:
                                        errorMessage = 'Location permission denied. Please enable location access.';
                                        break;
                                    case error.POSITION_UNAVAILABLE:
                                        errorMessage = 'Location information is unavailable.';
                                        break;
                                    case error.TIMEOUT:
                                        errorMessage = 'The request to get location timed out.';
                                        break;
                                    default:
                                        errorMessage = 'An unknown error occurred while getting location.';
                                        break;
                                }
                                
                                errorElement.textContent = errorMessage;
                                errorElement.classList.remove('hidden');
                            }
                            
                            // Revert to offline if location permission denied
                            if (error.code === error.PERMISSION_DENIED) {
                                const toggleStatus = document.getElementById('toggle-status');
                                if (toggleStatus && toggleStatus.classList.contains('bg-green-500')) {
                                    toggleStatus.click();
                                }
                            }
                        }
                    );
                } else {
                    alert('Geolocation is not supported by this browser.');
                }
            }
            
            // Function to stop location tracking
            function stopLocationTracking() {
                if (!isTrackingLocation) return;
                
                if (watchId !== null) {
                    navigator.geolocation.clearWatch(watchId);
                    watchId = null;
                }
                
                if (locationIntervalId !== null) {
                    clearInterval(locationIntervalId);
                    locationIntervalId = null;
                }
                
                isTrackingLocation = false;
                
                // Update UI
                const shareLocationBtn = document.getElementById('share-location');
                const locationInfo = document.getElementById('location-info');
                
                if (shareLocationBtn) {
                    shareLocationBtn.textContent = 'Share Location';
                    shareLocationBtn.classList.remove('bg-green-600');
                    shareLocationBtn.classList.add('bg-blue-600');
                }
                
                if (locationInfo) {
                    locationInfo.classList.add('hidden');
                }
            }
            
            // Function to update driver location on server
            function updateDriverLocation(latitude, longitude) {
                fetch('/driver/location', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        latitude: latitude,
                        longitude: longitude
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Update location timestamp if element exists
                        const locationTimestamp = document.getElementById('location-timestamp');
                        if (locationTimestamp && data.timestamp) {
                            locationTimestamp.textContent = new Date(data.timestamp).toLocaleTimeString();
                        }
                        
                        // If map exists, update driver marker
                        if (map && driverMarker) {
                            driverMarker.setLatLng([latitude, longitude]);
                        }
                        
                        // Update driver status display
                        updateDriverStatus();
                    }
                })
                .catch(error => {
                    console.error('Network error when updating location:', error);
                });
            }

            // Function to initialize debug status information
            function initDebugStatus() {
                const debugStatusBtn = document.getElementById('debug-status');
                
                if (debugStatusBtn) {
                    debugStatusBtn.addEventListener('click', function() {
                        debugDriverStatus();
                    });
                }
                
                // Initial status update
                updateDriverStatus();
            }
            
            // Debug driver status
            function debugDriverStatus() {
                fetch('/driver/debug/status', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Display debug information
                    const debugInfo = document.getElementById('debug-info');
                    if (debugInfo) {
                        debugInfo.classList.remove('hidden');
                        
                        let html = `<div class="p-4 rounded-md ${data.debug_info.matching_issues.length > 0 ? 'bg-red-50 text-red-700' : 'bg-green-50 text-green-700'}">
                            <h5 class="font-bold">${data.eligibility_summary}</h5>`;
                        
                        if (data.debug_info.matching_issues.length > 0) {
                            html += `<ul class="mt-2 ml-5 list-disc">`;
                            data.debug_info.matching_issues.forEach(issue => {
                                html += `<li>${issue}</li>`;
                            });
                            html += `</ul>`;
                            
                            html += `<h6 class="font-bold mt-3">How to fix:</h6><ul class="ml-5 list-disc">`;
                            data.fix_instructions.forEach(instruction => {
                                html += `<li>${instruction}</li>`;
                            });
                            html += `</ul>`;
                        }
                        
                        html += `</div>`;
                        
                        // Add detailed debug info
                        html += `<div class="mt-3 p-4 bg-gray-50 rounded-md overflow-auto">
                            <h6 class="font-bold mb-2">Detailed Debug Information</h6>
                            <pre class="text-xs">${JSON.stringify(data.debug_info, null, 2)}</pre>
                        </div>`;
                        
                        debugInfo.innerHTML = html;
                    }
                })
                .catch(error => {
                    console.error('Failed to debug driver status:', error);
                });
            }
            
            // Function to update driver status display
            function updateDriverStatus() {
                console.log("Updating driver status...");
                const visibilityStatus = document.getElementById('visibility-status');
                const statusOnline = document.getElementById('status-online');
                const statusLocation = document.getElementById('status-location');
                const statusAccount = document.getElementById('status-account');
                const statusVehicle = document.getElementById('status-vehicle');
                
                if (!visibilityStatus) return; // Status elements not found
                
                fetch('/driver/debug/status', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Update overall status
                    if (data.debug_info.matching_issues.length > 0) {
                        visibilityStatus.className = 'p-3 mb-4 rounded-md bg-red-50 text-red-700';
                        visibilityStatus.textContent = 'You are NOT visible to passengers';
                    } else {
                        visibilityStatus.className = 'p-3 mb-4 rounded-md bg-green-50 text-green-700';
                        visibilityStatus.textContent = 'You are visible to passengers';
                    }
                    
                    // Update individual statuses
                    if (statusOnline) {
                        statusOnline.textContent = data.debug_info.user.is_online ? 'Online' : 'Offline';
                        statusOnline.className = 'px-2 py-1 rounded-md text-xs font-medium ' + 
                            (data.debug_info.user.is_online ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800');
                    }
                    
                    if (statusLocation && data.debug_info.location) {
                        const isLocationActive = data.debug_info.location.is_recent;
                        statusLocation.textContent = isLocationActive ? 'Active' : 'Inactive';
                        statusLocation.className = 'px-2 py-1 rounded-md text-xs font-medium ' + 
                            (isLocationActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800');
                        
                        // Update location timestamp
                        const locationTimestamp = document.getElementById('location-timestamp');
                        if (locationTimestamp && data.debug_info.location.last_updated) {
                            locationTimestamp.textContent = new Date(data.debug_info.location.last_updated).toLocaleTimeString();
                        }
                    }
                    
                    if (statusAccount) {
                        statusAccount.textContent = data.debug_info.user.account_status;
                        statusAccount.className = 'px-2 py-1 rounded-md text-xs font-medium ' + 
                            (data.debug_info.user.account_status === 'activated' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800');
                    }
                    
                    if (statusVehicle && data.debug_info.vehicle) {
                        statusVehicle.textContent = data.debug_info.vehicle.is_active ? 'Active' : 'Inactive';
                        statusVehicle.className = 'px-2 py-1 rounded-md text-xs font-medium ' + 
                            (data.debug_info.vehicle.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800');
                    }
                })
                .catch(error => {
                    console.error('Failed to update driver status:', error);
                });
            }
            
            // Function to initialize heartbeat and auto-offline
            function initHeartbeatAndOffline() {
                // Setup heartbeat
                let heartbeatInterval = setInterval(sendHeartbeat, 30000); // every 30 seconds
                
                // Add event listener for page unload (close tab, navigate away, etc)
                window.addEventListener('beforeunload', function() {
                    setDriverOffline(true);
                    clearInterval(heartbeatInterval);
                });
                
                // Check initial online status
                fetch('/driver/status/check', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.is_online) {
                        // If already online, start location tracking
                        startLocationTracking();
                    }
                })
                .catch(error => {
                    console.error('Failed to check online status:', error);
                });
            }
            
            // Function to send heartbeat to server
            function sendHeartbeat() {
                // Only send heartbeat if driver is online
                const isOnline = document.getElementById('toggle-status') && 
                                document.getElementById('toggle-status').classList.contains('bg-green-500');
                
                if (isOnline) {
                    fetch('/driver/heartbeat', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    }).catch(error => console.error('Heartbeat error:', error));
                }
            }
            
            // Function to set driver offline
            function setDriverOffline(useBeacon = false) {
                // If using beacon (for page unload), use navigator.sendBeacon
                if (useBeacon) {
                    const formData = new FormData();
                    formData.append('_token', csrfToken);
                    navigator.sendBeacon('/driver/set-offline', formData);
                    return;
                }
                
                // Otherwise use fetch
                fetch('/driver/set-offline', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(() => {
                    // Stop location tracking
                    stopLocationTracking();
                    
                    // Update driver status display
                    updateDriverStatus();
                })
                .catch(error => {
                    console.error('Failed to set driver offline:', error);
                });
            }
            
            // Function to initialize ride request handling
            function initRideRequestHandling() {
                // Handle ride request responses (accept/reject)
                document.querySelectorAll('.accept-request, .reject-request').forEach(button => {
                    button.addEventListener('click', function() {
                        const requestId = this.dataset.requestId;
                        const action = this.classList.contains('accept-request') ? 'accept' : 'reject';
                        
                        // Disable both buttons to prevent multiple clicks
                        const requestContainer = this.closest('.border-l-4');
                        const buttons = requestContainer.querySelectorAll('button');
                        buttons.forEach(btn => {
                            btn.disabled = true;
                            btn.classList.add('opacity-50', 'cursor-not-allowed');
                        });
                        
                        // Show loading state
                        this.innerHTML = action === 'accept' ? 'Accepting...' : 'Declining...';
                        
                        // Send response to server
                        fetch(`/driver/request/${requestId}/respond`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({
                                response: action
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Request handled successfully
                                requestContainer.style.transition = 'opacity 0.5s';
                                requestContainer.style.opacity = '0.5';
                                
                                // Add a success indicator
                                const successBadge = document.createElement('div');
                                successBadge.className = 'text-sm font-medium text-green-600 mt-2';
                                successBadge.textContent = action === 'accept' ? 'Accepted!' : 'Declined';
                                this.parentNode.appendChild(successBadge);
                                
                                // If accepted, redirect to active rides
                                if (action === 'accept' && data.redirect) {
                                    setTimeout(() => {
                                        window.location.href = data.redirect;
                                    }, 1000);
                                } else {
                                    // Remove the request after a delay if rejected
                                    setTimeout(() => {
                                        requestContainer.remove();
                                        
                                        // If no more requests, hide the parent container
                                        const requestsContainer = requestContainer.closest('.bg-white');
                                        if (requestsContainer && !requestsContainer.querySelector('.border-l-4')) {
                                            requestsContainer.remove();
                                        }
                                    }, 1500);
                                }
                            } else {
                                // Error handling
                                this.innerHTML = action === 'accept' ? 'Accept' : 'Decline';
                                buttons.forEach(btn => {
                                    btn.disabled = false;
                                    btn.classList.remove('opacity-50', 'cursor-not-allowed');
                                });
                                
                                // Show error message
                                const errorMessage = document.createElement('div');
                                errorMessage.className = 'text-sm font-medium text-red-600 mt-2';
                                errorMessage.textContent = data.message || 'An error occurred';
                                this.parentNode.appendChild(errorMessage);
                            }
                        })
                        .catch(error => {
                            console.error('Error responding to request:', error);
                            
                            // Reset buttons
                            this.innerHTML = action === 'accept' ? 'Accept' : 'Decline';
                            buttons.forEach(btn => {
                                btn.disabled = false;
                                btn.classList.remove('opacity-50', 'cursor-not-allowed');
                            });
                            
                            // Show error message
                            const errorMessage = document.createElement('div');
                            errorMessage.className = 'text-sm font-medium text-red-600 mt-2';
                            errorMessage.textContent = 'Network error. Please try again.';
                            this.parentNode.appendChild(errorMessage);
                        });
                    });
                });
                
                // Handle start ride buttons
                document.querySelectorAll('.start-ride, .start-ride-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        const rideId = this.dataset.rideId;
                        
                        // Show start ride modal
                        const startRideModal = document.getElementById('start-ride-modal');
                        const startRideIdInput = document.getElementById('start-ride-id-input');
                        const startLatitudeInput = document.getElementById('start-latitude-input');
                        const startLongitudeInput = document.getElementById('start-longitude-input');
                        
                        if (startRideModal && startRideIdInput) {
                            startRideIdInput.value = rideId;
                            
                            // Get current location if available
                            if (driverLocation) {
                                startLatitudeInput.value = driverLocation.lat;
                                startLongitudeInput.value = driverLocation.lng;
                            } else if (navigator.geolocation) {
                                navigator.geolocation.getCurrentPosition(position => {
                                    startLatitudeInput.value = position.coords.latitude;
                                    startLongitudeInput.value = position.coords.longitude;
                                });
                            }
                            
                            startRideModal.classList.remove('hidden');
                        }
                    });
                });
                
                // Handle complete ride buttons
                document.querySelectorAll('.complete-ride, .complete-ride-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        const rideId = this.dataset.rideId;
                        
                        // Show complete ride modal
                        const completeRideModal = document.getElementById('complete-ride-modal');
                        const rideIdInput = document.getElementById('ride-id-input');
                        const latitudeInput = document.getElementById('latitude-input');
                        const longitudeInput = document.getElementById('longitude-input');
                        
                        if (completeRideModal && rideIdInput) {
                            rideIdInput.value = rideId;
                            
                            // Get current location if available
                            if (driverLocation) {
                                latitudeInput.value = driverLocation.lat;
                                longitudeInput.value = driverLocation.lng;
                            } else if (navigator.geolocation) {
                                navigator.geolocation.getCurrentPosition(position => {
                                    latitudeInput.value = position.coords.latitude;
                                    longitudeInput.value = position.coords.longitude;
                                });
                            }
                            
                            completeRideModal.classList.remove('hidden');
                        }
                    });
                });
            }
            
            // Function to initialize countdown timers for requests
            function initRequestTimers() {
                document.querySelectorAll('.countdown').forEach(timer => {
                    const requestedAt = new Date(timer.dataset.requested);
                    const requestId = timer.dataset.requestId;
                    
                    // Extend timeout from 15 to 45 seconds to give drivers more time to respond
                    const expiresAt = new Date(requestedAt.getTime() + 45 * 1000); 
                    
                    function updateTimer() {
                        const now = new Date();
                        const remainingMs = expiresAt - now;
                        
                        if (remainingMs <= 0) {
                            timer.textContent = 'Expired';
                            timer.classList.remove('text-red-600');
                            timer.classList.add('text-gray-400');
                            
                            // Even when UI shows expired, first check with server if request is truly expired
                            fetch(`/driver/request/${requestId}/status`)
                                .then(response => response.json())
                                .then(data => {
                                    const requestContainer = timer.closest('.border-l-4');
                                    if (requestContainer) {
                                        const buttons = requestContainer.querySelectorAll('button');
                                        
                                        // If request is actually still pending on server, don't disable buttons
                                        if (data.status === 'pending') {
                                            const expiredMsg = document.createElement('div');
                                            expiredMsg.className = 'mt-2 text-sm text-blue-500';
                                            expiredMsg.textContent = 'Request expired on UI but still active on server. You can still respond!';
                                            requestContainer.querySelector('.flex.space-x-3').appendChild(expiredMsg);
                                            return;
                                        }
                                        
                                        // Otherwise, disable buttons as normal
                                        buttons.forEach(btn => {
                                            btn.disabled = true;
                                            btn.classList.add('opacity-50', 'cursor-not-allowed');
                                        });
                                        
                                        // Add expired message
                                        const expiredMsg = document.createElement('div');
                                        expiredMsg.className = 'mt-2 text-sm text-gray-500';
                                        expiredMsg.textContent = 'This request has expired';
                                        requestContainer.querySelector('.flex.space-x-3').appendChild(expiredMsg);
                                    }
                                })
                                .catch(error => {
                                    console.error('Error checking request status:', error);
                                });
                            
                            return; // Stop updating
                        }
                        
                        const remainingSec = Math.ceil(remainingMs / 1000);
                        timer.textContent = remainingSec + 's';
                        
                        // Pulse effect when time is running out
                        if (remainingSec <= 10) {
                            timer.classList.add('animate-pulse');
                        }
                        
                        requestAnimationFrame(updateTimer);
                    }
                    
                    updateTimer();
                });
            }
            
            // Function to initialize modals
            function initModals() {
                // Start Ride Modal
                const startRideModal = document.getElementById('start-ride-modal');
                const startRideForm = document.getElementById('start-ride-form');
                const cancelStartRideBtn = document.getElementById('cancel-start-ride');
                
                if (startRideForm) {
                    startRideForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        
                        const rideId = document.getElementById('start-ride-id-input').value;
                        const latitude = document.getElementById('start-latitude-input').value;
                        const longitude = document.getElementById('start-longitude-input').value;
                        
                        startRide(rideId, latitude, longitude);
                        startRideModal.classList.add('hidden');
                    });
                }
                
                if (cancelStartRideBtn) {
                    cancelStartRideBtn.addEventListener('click', function() {
                        startRideModal.classList.add('hidden');
                    });
                }
                
                // Complete Ride Modal
                const completeRideModal = document.getElementById('complete-ride-modal');
                const completeRideForm = document.getElementById('complete-ride-form');
                const cancelCompleteRideBtn = document.getElementById('cancel-complete-ride');
                
                if (completeRideForm) {
                    completeRideForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        
                        const rideId = document.getElementById('ride-id-input').value;
                        const latitude = document.getElementById('latitude-input').value;
                        const longitude = document.getElementById('longitude-input').value;
                        
                        completeRide(rideId, latitude, longitude);
                        completeRideModal.classList.add('hidden');
                    });
                }
                
                if (cancelCompleteRideBtn) {
                    cancelCompleteRideBtn.addEventListener('click', function() {
                        completeRideModal.classList.add('hidden');
                    });
                }
                
                // Response Modal
                const responseModal = document.getElementById('ride-response-modal');
                const responseModalClose = document.getElementById('response-modal-close');
                
                if (responseModalClose) {
                    responseModalClose.addEventListener('click', function() {
                        responseModal.classList.add('hidden');
                    });
                }
            }
            
            // Function to start a ride
            function startRide(rideId, latitude, longitude) {
                // Get the CSRF token
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                // Disable submit button to prevent multiple submissions
                document.querySelector('#start-ride-form button[type="submit"]').disabled = true;
                
                // Send the request
                fetch(`/driver/ride/${rideId}/start`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({
                        latitude: latitude || 0,
                        longitude: longitude || 0
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        showResponseModal(true, 'Ride started successfully!');
                        
                        // Reload page after a delay
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        // Show error message
                        showResponseModal(false, data.message || 'Failed to start ride. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error starting ride:', error);
                    showResponseModal(false, 'Network error. Please try again.');
                });
            }
            
            // Function to complete ride
            function completeRide(rideId, latitude, longitude) {
                // Show a simple message
                alert("Completing ride. You will be redirected to rate your passenger.");
                
                // Get the CSRF token
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                // Disable submit button to prevent multiple submissions
                document.querySelector('#complete-ride-form button[type="submit"]').disabled = true;
                
                // Send the request
                fetch(`/driver/ride/${rideId}/complete`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({
                        latitude: latitude || 0,
                        longitude: longitude || 0
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.redirect) {
                        // Redirect to the rating page
                        window.location.href = data.redirect;
                    } else {
                        // If we get a success: true but no redirect, or some other error
                        alert("Ride completed! You will now be redirected to rate your passenger.");
                        window.location.href = `/driver/ride/${rideId}/rate`;
                    }
                })
                .catch(() => {
                    // Even if there's an error, try to redirect to the rating page anyway
                    alert("Ride marked as completed. You will now be redirected to rate your passenger.");
                    window.location.href = `/driver/ride/${rideId}/rate`;
                });
            }
            
            // Function to show response modal
            function showResponseModal(success, message) {
                const responseModal = document.getElementById('ride-response-modal');
                const successDiv = document.getElementById('response-success');
                const errorDiv = document.getElementById('response-error');
                const successMessage = document.getElementById('success-message');
                const errorMessage = document.getElementById('error-message');
                
                if (success) {
                    successDiv.classList.remove('hidden');
                    errorDiv.classList.add('hidden');
                    successMessage.textContent = message;
                } else {
                    successDiv.classList.add('hidden');
                    errorDiv.classList.remove('hidden');
                    errorMessage.textContent = message;
                }
                
                responseModal.classList.remove('hidden');
            }
            
            // Make functions available globally
            window.startLocationTracking = startLocationTracking;
            window.stopLocationTracking = stopLocationTracking;
            window.updateDriverLocation = updateDriverLocation;
            window.showResponseModal = showResponseModal;
        });
    </script>
</body>
</html>