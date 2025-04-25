<!-- driver/activeRides.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>inTime - Active Rides</title>
    
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
    <header class="px-4 py-4 flex items-center justify-between bg-white shadow-sm sticky top-0 z-50">
        <!-- Logo and navigation -->
        <div class="flex items-center space-x-8">
            <!-- Logo -->
            <a href="{{ route('driver.dashboard') }}" class="text-2xl font-bold">inTime</a>
            
            <!-- Navigation Links -->
            <nav class="hidden md:flex space-x-6">
                <a href="{{ route('driver.awaiting.rides') }}" class="font-medium hover:text-blue-600 transition">Awaiting Rides</a>
                <a href="{{ route('driver.active.rides') }}" class="font-medium text-blue-600 transition">Active Rides</a>
                <a href="{{ route('driver.history') }}" class="font-medium hover:text-blue-600 transition">History</a>
                <a href="{{ route('driver.earnings') }}" class="font-medium hover:text-blue-600 transition">Earnings</a>
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
                        <img src="/api/placeholder/40/40" alt="Profile" class="h-full w-full object-cover">
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
                        <a href="{{ route('driver.active.rides') }}" class="font-medium px-3 py-2 rounded-md bg-blue-50 text-blue-600">Active Rides</a>
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
        
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Left Column - Map -->
            <div class="w-full lg:w-1/2 flex flex-col gap-6">
                <!-- Map Container -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="h-96" id="map"></div>
                </div>
                
                <!-- Location Sharing Toggle -->
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
            </div>
            
            <!-- Right Column - Rides -->
            <div class="w-full lg:w-1/2 flex flex-col gap-6">
                <!-- En Route Rides -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold mb-4">En Route to Pickup</h2>
                    
                    @if(count($enRouteRides) === 0)
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
            <a href="{{ route('passenger.public.profile', $ride->passenger->id) }}" class="text-blue-600 hover:text-blue-800 text-sm ml-2">
                View Profile
            </a>
        </div>
    </div>
</div>
                                            <p class="text-sm text-gray-500">Reserved: {{ $ride->reservation_date->format('M d, Y g:i A') }}</p>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
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
                                        
                                        <button type="button" class="navigation-btn border border-gray-300 text-gray-700 py-2 px-4 rounded-md text-sm font-medium hover:bg-gray-50 transition flex items-center justify-center flex-1"
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
                
                <!-- In Progress Rides -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold mb-4">In Progress Rides</h2>
                    
                    @if(count($inProgressRides) === 0)
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
            <a href="{{ route('passenger.public.profile', $ride->passenger->id) }}" class="text-blue-600 hover:text-blue-800 text-sm ml-2">
                View Profile
            </a>
        </div>
    </div>
</div>
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
                                        
                                        <button type="button" class="navigation-btn border border-gray-300 text-gray-700 py-2 px-4 rounded-md text-sm font-medium hover:bg-gray-50 transition flex items-center justify-center flex-1"
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
            // Initialize map
            const map = L.map('map').setView([0, 0], 15);
            
            // Add OpenStreetMap tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(map);
            
            // Variables
            let watchId = null;
            let isTrackingLocation = false;
            let driverLocation = null;
            let driverMarker = null;
            let pickupMarker = null;
            let dropoffMarker = null;
            let routingControl = null;
            
            // UI elements
            const locationSharingBtn = document.getElementById('share-location');
            const locationStatus = document.getElementById('location-status');
            const locationInfo = document.getElementById('location-info');
            const toggleStatusBtn = document.getElementById('toggle-status');
            const toggleCircle = document.getElementById('toggle-circle');
            const toggleText = document.getElementById('toggle-text');
            const statusIndicator = document.getElementById('status-indicator');
            const statusText = document.getElementById('status-text');
            const locationSharingContainer = document.getElementById('location-sharing-container');
            
            // Modal elements
            const startRideModal = document.getElementById('start-ride-modal');
            const startRideForm = document.getElementById('start-ride-form');
            const startRideIdInput = document.getElementById('start-ride-id-input');
            const startLatitudeInput = document.getElementById('start-latitude-input');
            const startLongitudeInput = document.getElementById('start-longitude-input');
            const cancelStartRideBtn = document.getElementById('cancel-start-ride');
            
            const completeRideModal = document.getElementById('complete-ride-modal');
            const completeRideForm = document.getElementById('complete-ride-form');
            const rideIdInput = document.getElementById('ride-id-input');
            const latitudeInput = document.getElementById('latitude-input');
            const longitudeInput = document.getElementById('longitude-input');
            const cancelCompleteRideBtn = document.getElementById('cancel-complete-ride');
            
            const responseModal = document.getElementById('ride-response-modal');
            const responseSuccessDiv = document.getElementById('response-success');
            const responseErrorDiv = document.getElementById('response-error');
            const successMessage = document.getElementById('success-message');
            const errorMessage = document.getElementById('error-message');
            const responseModalClose = document.getElementById('response-modal-close');
            
            // Mobile menu elements
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            const closeMobileMenuButton = document.getElementById('close-mobile-menu');
            
            // Profile dropdown elements
            const profileButton = document.getElementById('profile-button');
            const profileDropdown = document.getElementById('profile-dropdown');
            
            // Add event listeners
            
            // Toggle driver status
            if (toggleStatusBtn) {
                toggleStatusBtn.addEventListener('click', function() {
                    const isCurrentlyOnline = toggleStatusBtn.classList.contains('bg-green-500');
                    updateDriverStatus(!isCurrentlyOnline);
                });
            }
            
            // Location sharing
            if (locationSharingBtn) {
                locationSharingBtn.addEventListener('click', function() {
                    if (!isTrackingLocation) {
                        startLocationTracking();
                    } else {
                        stopLocationTracking();
                    }
                });
            }
            
            // Start ride buttons
            document.querySelectorAll('.start-ride-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const rideId = this.dataset.rideId;
                    startRideIdInput.value = rideId;
                    
                    // Get current location if available
                    if (driverLocation) {
                        startLatitudeInput.value = driverLocation.lat;
                        startLongitudeInput.value = driverLocation.lng;
                    }
                    
                    startRideModal.classList.remove('hidden');
                });
            });
            
            // Complete ride buttons
            document.querySelectorAll('.complete-ride-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const rideId = this.dataset.rideId;
                    rideIdInput.value = rideId;
                    
                    // Get current location if available
                    if (driverLocation) {
                        latitudeInput.value = driverLocation.lat;
                        longitudeInput.value = driverLocation.lng;
                    }
                    
                    completeRideModal.classList.remove('hidden');
                });
            });
            
            // Navigation buttons
            document.querySelectorAll('.navigation-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const pickupLat = parseFloat(this.dataset.pickupLat);
                    const pickupLng = parseFloat(this.dataset.pickupLng);
                    const dropoffLat = parseFloat(this.dataset.dropoffLat);
                    const dropoffLng = parseFloat(this.dataset.dropoffLng);
                    
                    if (pickupLat && pickupLng && dropoffLat && dropoffLng) {
                        addRouteToMap([pickupLat, pickupLng], [dropoffLat, dropoffLng]);
                    }
                });
            });
            
            // Modal forms and buttons
            if (startRideForm) {
                startRideForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const rideId = startRideIdInput.value;
                    const latitude = startLatitudeInput.value;
                    const longitude = startLongitudeInput.value;
                    startRide(rideId, latitude, longitude);
                    startRideModal.classList.add('hidden');
                });
            }
            
            if (completeRideForm) {
                completeRideForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const rideId = rideIdInput.value;
                    const latitude = latitudeInput.value;
                    const longitude = longitudeInput.value;
                    completeRide(rideId, latitude, longitude);
                    completeRideModal.classList.add('hidden');
                });
            }
            
            if (cancelStartRideBtn) {
                cancelStartRideBtn.addEventListener('click', function() {
                    startRideModal.classList.add('hidden');
                });
            }
            
            if (cancelCompleteRideBtn) {
                cancelCompleteRideBtn.addEventListener('click', function() {
                    completeRideModal.classList.add('hidden');
                });
            }
            
            if (responseModalClose) {
                responseModalClose.addEventListener('click', function() {
                    responseModal.classList.add('hidden');
                });
            }
            
            // Mobile menu
            if (mobileMenuButton) {
                mobileMenuButton.addEventListener('click', function() {
                    mobileMenu.classList.remove('translate-x-full');
                });
            }
            
            if (closeMobileMenuButton) {
                closeMobileMenuButton.addEventListener('click', function() {
                    mobileMenu.classList.add('translate-x-full');
                });
            }
            
            // Profile dropdown
            if (profileButton) {
                profileButton.addEventListener('click', function() {
                    profileDropdown.classList.toggle('hidden');
                });
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function(event) {
                    if (!profileButton.contains(event.target) && !profileDropdown.contains(event.target)) {
                        profileDropdown.classList.add('hidden');
                    }
                });
            }
            
            // Functions
            
            // Update driver status
            function updateDriverStatus(isOnline) {
                fetch('{{ route("driver.update.status") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        is_online: isOnline
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Update UI elements
                        toggleCircle.classList.toggle('translate-x-1', !isOnline);
                        toggleCircle.classList.toggle('translate-x-5', isOnline);
                        toggleStatusBtn.classList.toggle('bg-gray-300', !isOnline);
                        toggleStatusBtn.classList.toggle('bg-green-500', isOnline);
                        toggleText.textContent = isOnline ? 'Go Offline' : 'Go Online';
                        statusIndicator.classList.toggle('bg-red-500', !isOnline);
                        statusIndicator.classList.toggle('bg-green-500', isOnline);
                        statusText.textContent = isOnline ? 'Online' : 'Offline';
                        
                        // Show/hide location sharing button
                        if (isOnline) {
                            locationSharingContainer.classList.remove('hidden');
                            
                            if (data.request_location) {
                                // Auto-start location sharing when going online
                                startLocationTracking();
                            }
                        } else {
                            locationSharingContainer.classList.add('hidden');
                            stopLocationTracking();
                        }
                    }
                })
                .catch(error => {
                    console.error('Error updating status:', error);
                    showResponseModal(false, 'Failed to update your online status. Please try again.');
                });
            }
            
            // Start tracking location
            function startLocationTracking() {
                if (navigator.geolocation) {
                    locationStatus.textContent = 'Tracking...';
                    locationSharingBtn.classList.add('bg-red-500');
                    locationSharingBtn.classList.remove('bg-blue-600');
                    locationInfo.classList.remove('hidden');
                    
                    watchId = navigator.geolocation.watchPosition(
                        // Success callback
                        position => {
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;
                            
                            // Save current location for later use
                            driverLocation = {lat, lng};
                            
                            // Update location on the server
                            updateLocationOnServer(lat, lng);
                            
                            // Update map with driver location
                            updateDriverLocationOnMap(lat, lng);
                            
                            isTrackingLocation = true;
                            locationStatus.textContent = 'Location Shared';
                        },
                        // Error callback
                        error => {
                            console.error('Geolocation error:', error);
                            stopLocationTracking();
                            showResponseModal(false, 'Unable to access your location. Please check your device settings and try again.');
                        },
                        // Options
                        {
                            enableHighAccuracy: true,
                            maximumAge: 0,
                            timeout: 5000
                        }
                    );
                } else {
                    showResponseModal(false, 'Geolocation is not supported by your browser.');
                }
            }
            
            // Stop tracking location
            function stopLocationTracking() {
                if (watchId !== null) {
                    navigator.geolocation.clearWatch(watchId);
                    watchId = null;
                }
                
                isTrackingLocation = false;
                locationStatus.textContent = 'Share Location';
                locationSharingBtn.classList.remove('bg-red-500');
                locationSharingBtn.classList.add('bg-blue-600');
                locationInfo.classList.add('hidden');
            }
            
            // Update location on server
            function updateLocationOnServer(latitude, longitude) {
                fetch('{{ route("driver.update.location") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        latitude: latitude,
                        longitude: longitude
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status !== 'success') {
                        console.error('Error updating location on server:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Failed to update location:', error);
                });
            }
            
            // Update driver location on map
            function updateDriverLocationOnMap(latitude, longitude) {
                const driverLatLng = [latitude, longitude];
                
                // If marker doesn't exist yet, create it
                if (!driverMarker) {
                    // Create a custom icon for the driver
                    const driverIcon = L.divIcon({
                        className: 'driver-marker',
                        html: '<div class="driver-marker-inner"></div>',
                        iconSize: [24, 24],
                        iconAnchor: [12, 12]
                    });
                    
                    driverMarker = L.marker(driverLatLng, {
                        icon: driverIcon,
                        zIndexOffset: 1000
                    }).addTo(map);
                    
                    // Center map on driver location
                    map.setView(driverLatLng, 15);
                } else {
                    // Update existing marker position
                    driverMarker.setLatLng(driverLatLng);
                }
            }
            
            // Add route to map
            function addRouteToMap(pickupCoords, dropoffCoords) {
                // Remove existing markers and routes
                if (pickupMarker) map.removeLayer(pickupMarker);
                if (dropoffMarker) map.removeLayer(dropoffMarker);
                if (routingControl) map.removeControl(routingControl);
                
                // Add pickup marker
                pickupMarker = L.marker(pickupCoords, {
                    icon: L.divIcon({
                        className: 'pickup-marker',
                        html: '<div class="pickup-marker-inner"></div>',
                        iconSize: [20, 20],
                        iconAnchor: [10, 10]
                    })
                }).addTo(map);
                
                // Add dropoff marker
                dropoffMarker = L.marker(dropoffCoords, {
                    icon: L.divIcon({
                        className: 'dropoff-marker',
                        html: '<div class="dropoff-marker-inner"></div>',
                        iconSize: [20, 20],
                        iconAnchor: [10, 10]
                    })
                }).addTo(map);
                
                // Add routing
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
            }
            
            // Show response modal
            function showResponseModal(success, message) {
                if (success) {
                    responseSuccessDiv.classList.remove('hidden');
                    responseErrorDiv.classList.add('hidden');
                    successMessage.innerHTML = message;
                } else {
                    responseSuccessDiv.classList.add('hidden');
                    responseErrorDiv.classList.remove('hidden');
                    errorMessage.textContent = message;
                }
                
                responseModal.classList.remove('hidden');
            }
            
            // Start ride
            function startRide(rideId, latitude, longitude) {
                fetch(`{{ url('driver/ride') }}/${rideId}/start`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        latitude: latitude,
                        longitude: longitude
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showResponseModal(true, 'Ride started successfully.');
                        
                        // Refresh the page after a delay
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        showResponseModal(false, data.message || 'Failed to start ride.');
                    }
                })
                .catch(error => {
                    console.error('Error starting ride:', error);
                    showResponseModal(false, 'An error occurred while processing your request.');
                });
            }
            
            function completeRide(rideId, latitude, longitude) {
    // Get the CSRF token
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Create the request payload
    const payload = {
        latitude: latitude || 0,
        longitude: longitude || 0
    };
    
    // Show loading state
    showLoadingOverlay('Completing ride...');
    
    // Send the request
    fetch(`/driver/ride/${rideId}/complete`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token
        },
        body: JSON.stringify(payload)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Server returned error status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        hideLoadingOverlay();
        
        if (data.success) {
            // Show success message
            showSuccessMessage('Ride completed successfully!');
            
            // For cash payments, redirect driver to cash confirmation page
            if (data.payment_method === 'cash') {
                setTimeout(() => {
                    window.location.href = `/driver/ride/${rideId}/confirm-cash-payment`;
                }, 1500);
            } else {
                // Card payments will be handled by the passenger
                // Show a message that passenger will complete payment
                showSuccessMessage('The passenger will now complete payment. You will be notified when payment is complete.');
                
                // Redirect back to dashboard after a delay
                setTimeout(() => {
                    window.location.href = '/driver/dashboard';
                }, 3000);
            }
        } else {
            showErrorMessage(data.message || 'Failed to complete ride');
        }
    })
    .catch(error => {
        hideLoadingOverlay();
        console.error('Error completing ride:', error);
        showErrorMessage('An error occurred. Please try again or contact support.');
    });
}

// No need for the proceedWithCompleteRide function

function proceedWithCompleteRide(rideId, latitude, longitude) {
    // Show a simple alert to confirm function is being called
    alert("Attempting to complete ride: " + rideId);
    
    // Get the CSRF token
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Log for debugging
    console.log(`Completing ride ${rideId} at coordinates: ${latitude}, ${longitude}`);
    console.log('CSRF Token:', token);
    
    // Create the request payload
    const payload = {
        latitude: latitude,
        longitude: longitude
    };
    
    console.log('Sending payload:', payload);
    
    // Send the request
    fetch(`/driver/ride/${rideId}/complete`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token
        },
        body: JSON.stringify(payload)
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', [...response.headers.entries()]);
        
        // Check if the response is ok (status in the range 200-299)
        if (!response.ok) {
            console.error('Server returned error status:', response.status);
        }
        // Try to get response text first
        return response.text();
    })
    .then(text => {
        console.log('Raw response text length:', text.length);
        console.log('Raw response first 100 chars:', text.substring(0, 100));
        
        // Try to parse as JSON
        let data;
        try {
            data = JSON.parse(text);
            console.log('Successfully parsed JSON');
        } catch (e) {
            console.error('Failed to parse response as JSON:', e);
            alert('Failed to parse server response as JSON');
            showResponseModal(false, 'Server returned an invalid response. Please try again.');
            return;
        }
        
        console.log('Parsed response data:', data);
        
        // ... rest of the function
    })
    .catch(error => {
        console.error('Fetch error:', error);
        alert('Error: ' + error.message);
        showResponseModal(false, 'An error occurred while processing your request: ' + error.message);
    });
}
            
            // Check for active rides with coordinates and add to map
            const activeRideElements = document.querySelectorAll('.navigation-btn');
            if (activeRideElements.length > 0) {
                // Get the first active ride to display on map
                const firstRide = activeRideElements[0];
                const pickupLat = parseFloat(firstRide.dataset.pickupLat);
                const pickupLng = parseFloat(firstRide.dataset.pickupLng);
                const dropoffLat = parseFloat(firstRide.dataset.dropoffLat);
                const dropoffLng = parseFloat(firstRide.dataset.dropoffLng);
                
                if (pickupLat && pickupLng && dropoffLat && dropoffLng) {
                    addRouteToMap([pickupLat, pickupLng], [dropoffLat, dropoffLng]);
                }
            } else {
                // If no active rides, get current location and center map there
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        position => {
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;
                            
                            // Center map on current location
                            map.setView([lat, lng], 15);
                            
                            // Add driver marker
                            updateDriverLocationOnMap(lat, lng);
                            
                            // Save location for later use
                            driverLocation = {lat, lng};
                        },
                        error => {
                            console.log('Unable to retrieve your location');
                        }
                    );
                }
            }
            
            // Add CSS for markers
            const style = document.createElement('style');
            style.textContent = `
                .driver-marker {
                    background: transparent;
                    border: none;
                }
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
            `;
            document.head.appendChild(style);
            
            // Auto-start location tracking if online
            if ({{ Auth::user()->is_online ? 'true' : 'false' }}) {
                startLocationTracking();
            }
            
            // Handle window resize
            window.addEventListener('resize', function() {
                map.invalidateSize();
            });
        });
    </script>
</body>
</html>




