<!-- driver/awaitingRides.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>inTime - Awaiting Rides</title>
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- OpenStreetMap with Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" 
          crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" 
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" 
            crossorigin=""></script>
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
                <a href="{{ route('driver.awaiting.rides') }}" class="font-medium border-b-2 border-blue-600 text-blue-600">Awaiting Rides</a>
                <a href="{{ route('driver.active.rides') }}" class="font-medium hover:text-blue-600 transition">Active Rides</a>
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
                <span class="w-3 h-3 rounded-full {{ Auth::user()->is_online ? 'bg-green-500' : 'bg-red-500' }} mr-2"></span>
                <span class="text-sm font-medium">{{ Auth::user()->is_online ? 'Online' : 'Offline' }}</span>
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
                        <a href="{{ route('driver.awaiting.rides') }}" class="font-medium px-3 py-2 rounded-md bg-blue-50 text-blue-600">Awaiting Rides</a>
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
        
        <div class="max-w-4xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Awaiting Rides</h1>
                <a href="{{ route('driver.dashboard') }}" class="text-blue-600 hover:underline flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.707-10.293a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L9.414 11H13a1 1 0 100-2H9.414l1.293-1.293z" clip-rule="evenodd" />
                    </svg>
                    Back to Dashboard
                </a>
            </div>
            
            @if(count($pendingRides) === 0)
                <div class="bg-white rounded-lg shadow-md p-8 text-center">
                    <div class="flex justify-center mb-6">
                        <div class="bg-gray-100 p-4 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    
                    <h2 class="text-xl font-bold mb-4">No Awaiting Rides</h2>
                    
                    <p class="text-gray-600 mb-6">
                        You don't have any rides awaiting your response at the moment. 
                        Make sure you're online to receive new ride requests.
                    </p>
                    
                    <div class="bg-gray-50 p-6 rounded-lg mb-6">
                        <h3 class="font-medium mb-2">Tips for getting more ride requests:</h3>
                        <ul class="text-gray-600 text-left space-y-2">
                            <li class="flex items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Stay online during peak hours (morning and evening rush hours)
                            </li>
                            <li class="flex items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Position yourself in high-demand areas like downtown, airports, or event venues
                            </li>
                            <li class="flex items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Maintain a high rating by providing excellent service
                            </li>
                        </ul>
                    </div>
                    
                    <a href="{{ route('driver.dashboard') }}" class="bg-blue-600 text-white py-2 px-6 rounded-md font-medium hover:bg-blue-700 transition">
                        Return to Dashboard
                    </a>
                </div>
            @else
                <div class="space-y-6">
                    @foreach($pendingRides as $ride)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h2 class="text-xl font-bold">Ride Request from {{ $ride->passenger->user->name }}</h2>
                                        <p class="text-gray-500">{{ $ride->reservation_date->format('M d, Y g:i A') }}</p>
                                    </div>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                        Pending
                                    </span>
                                </div>
                                
                                <div class="flex flex-col md:flex-row gap-6">
                                    <div class="w-full md:w-1/2">
                                        <div id="map-{{ $ride->id }}" class="h-60 rounded-md mb-4 border border-gray-200"></div>
                                    </div>
                                    
                                    <div class="w-full md:w-1/2 space-y-4">
                                        <div class="flex items-start">
                                            <div class="mt-1 mr-3 text-blue-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <circle cx="12" cy="12" r="8" stroke-width="2" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium">Pickup Location</p>
                                                <p class="text-sm text-gray-600">{{ $ride->pickup_location }}</p>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-start">
                                            <div class="mt-1 mr-3 text-red-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium">Dropoff Location</p>
                                                <p class="text-sm text-gray-600">{{ $ride->dropoff_location }}</p>
                                            </div>
                                        </div>
                                        
                                        <!-- Fare and details -->
                                        <div class="grid grid-cols-2 gap-4 mt-3 bg-gray-50 p-4 rounded-md">
                                            <div>
                                                <p class="text-sm font-medium">Estimated Fare</p>
                                                <p class="text-sm text-gray-600">MAD {{ number_format($ride->price ?? $ride->ride_cost ?? 0, 2) }}</p>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium">Distance</p>
                                                <p class="text-sm text-gray-600">
                                                    @if(isset($ride->distance_in_km))
                                                        {{ number_format($ride->distance_in_km, 1) }} km
                                                    @else
                                                        Calculating...
                                                    @endif
                                                </p>
                                            </div>
                                            
                                            <div>
                                                <p class="text-sm font-medium">Vehicle Type</p>
                                                <p class="text-sm text-gray-600">
                                                    {{ ucfirst($ride->vehicle_type ?? 'Standard') }}
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium">ETA</p>
                                                <p class="text-sm text-gray-600">
                                                    @php
                                                        $pickupLat = $ride->pickup_latitude;
                                                        $pickupLon = $ride->pickup_longitude;
                                                        $dropoffLat = $ride->dropoff_latitude;
                                                        $dropoffLon = $ride->dropoff_longitude;
                                                        
                                                        if ($pickupLat && $pickupLon && $dropoffLat && $dropoffLon) {
                                                            // Calculate distance using Haversine formula
                                                            $earthRadius = 6371; // in kilometers
                                                            $dLat = deg2rad($dropoffLat - $pickupLat);
                                                            $dLon = deg2rad($dropoffLon - $pickupLon);
                                                            $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($pickupLat)) * cos(deg2rad($dropoffLat)) * sin($dLon/2) * sin($dLon/2);
                                                            $c = 2 * atan2(sqrt($a), sqrt(1-$a));
                                                            $distance = $earthRadius * $c;
                                                            
                                                            // Estimate time (assume 30 km/h average speed)
                                                            $timeInHours = $distance / 30;
                                                            $timeInMinutes = ceil($timeInHours * 60);
                                                            
                                                            echo $timeInMinutes . ' min';
                                                        } else {
                                                            echo 'Calculating...';
                                                        }
                                                    @endphp
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <!-- Action buttons -->
                                        <div class="flex flex-wrap gap-3 pt-2">
                                            <form action="{{ route('driver.ride.respond', $ride->id) }}" method="POST" class="flex-1">
                                                @csrf
                                                <input type="hidden" name="action" value="accept">
                                                <button type="submit" class="w-full bg-blue-600 text-white py-2 px-6 rounded-md font-medium hover:bg-blue-700 transition">
                                                    Accept Ride
                                                </button>
                                            </form>
                                            
                                            <form action="{{ route('driver.ride.respond', $ride->id) }}" method="POST" class="flex-1">
                                                @csrf
                                                <input type="hidden" name="action" value="reject">
                                                <button type="submit" class="w-full border border-gray-300 text-gray-700 py-2 px-6 rounded-md font-medium hover:bg-gray-50 transition">
                                                    Decline
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Passenger details -->
                            <div class="border-t border-gray-200 bg-gray-50 px-6 py-4">
                                <h3 class="font-medium mb-2">Passenger Details</h3>
                                <div class="flex items-center">
                                    <div class="h-12 w-12 rounded-full bg-gray-300 overflow-hidden mr-3">
                                        @if($ride->passenger->user->profile_picture)
                                            <img src="{{ asset('storage/' . $ride->passenger->user->profile_picture) }}" alt="Profile" class="h-full w-full object-cover">
                                        @else
                                            <img src="/api/placeholder/48/48" alt="Profile" class="h-full w-full object-cover">
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-medium">{{ $ride->passenger->user->name }}</p>
                                        <div class="flex items-center text-sm text-gray-500">
                                            <span class="mr-1">{{ number_format($ride->passenger->rating ?? 0, 1) }}</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                            <span class="ml-2">{{ $ride->passenger->total_rides ?? 0 }} rides</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </main>

    <!-- Response Modal -->
    <div id="response-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            
            <div class="bg-white rounded-lg px-4 pt-5 pb-4 text-center overflow-hidden shadow-xl transform transition-all sm:max-w-sm sm:w-full sm:p-6">
                <div id="success-content" class="hidden">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                        <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Success</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500" id="success-message"></p>
                        </div>
                    </div>
                </div>
                
                <div id="error-content" class="hidden">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Error</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500" id="error-message"></p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-5 sm:mt-6">
                    <button type="button" class="inline-flex justify-center w-full rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:text-sm" id="close-modal">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for maps and functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile menu elements
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            const closeMobileMenuButton = document.getElementById('close-mobile-menu');
            
            // Profile dropdown elements
            const profileButton = document.getElementById('profile-button');
            const profileDropdown = document.getElementById('profile-dropdown');
            
            // Response modal elements
            const responseModal = document.getElementById('response-modal');
            const successContent = document.getElementById('success-content');
            const errorContent = document.getElementById('error-content');
            const successMessage = document.getElementById('success-message');
            const errorMessage = document.getElementById('error-message');
            const closeModalButton = document.getElementById('close-modal');
            
            // Initialize maps for each ride
            @foreach($pendingRides as $ride)
                // Create map if pickup and dropoff coordinates exist
                @if($ride->pickup_latitude && $ride->pickup_longitude && $ride->dropoff_latitude && $ride->dropoff_longitude)
                    const map{{ $ride->id }} = L.map('map-{{ $ride->id }}').setView([{{ $ride->pickup_latitude }}, {{ $ride->pickup_longitude }}], 13);
                    
                    // Add OpenStreetMap tile layer
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                        maxZoom: 19
                    }).addTo(map{{ $ride->id }});
                    
                    // Define pickup and dropoff coordinates
                    const pickupCoords = [{{ $ride->pickup_latitude }}, {{ $ride->pickup_longitude }}];
                    const dropoffCoords = [{{ $ride->dropoff_latitude }}, {{ $ride->dropoff_longitude }}];
                    
                    // Add pickup marker with custom icon
                    const pickupIcon = L.divIcon({
                        className: 'pickup-marker',
                        html: '<div class="pickup-marker-inner"></div>',
                        iconSize: [20, 20],
                        iconAnchor: [10, 10]
                    });
                    
                    const pickupMarker = L.marker(pickupCoords, {
                        icon: pickupIcon
                    }).addTo(map{{ $ride->id }});
                    
                    pickupMarker.bindPopup("<strong>Pickup</strong><br>{{ $ride->pickup_location }}");
                    
                    // Add dropoff marker with custom icon
                    const dropoffIcon = L.divIcon({
                        className: 'dropoff-marker',
                        html: '<div class="dropoff-marker-inner"></div>',
                        iconSize: [20, 20],
                        iconAnchor: [10, 10]
                    });
                    
                    const dropoffMarker = L.marker(dropoffCoords, {
                        icon: dropoffIcon
                    }).addTo(map{{ $ride->id }});
                    
                    dropoffMarker.bindPopup("<strong>Dropoff</strong><br>{{ $ride->dropoff_location }}");
                    
                    // Draw a route line between pickup and dropoff
                    const routeLine = L.polyline([pickupCoords, dropoffCoords], {
                        color: '#3B82F6',
                        weight: 4,
                        opacity: 0.7,
                        dashArray: '6, 8'
                    }).addTo(map{{ $ride->id }});
                    
                    // Fit map to show both points with padding
                    const bounds = L.latLngBounds(pickupCoords, dropoffCoords);
                    map{{ $ride->id }}.fitBounds(bounds, { padding: [30, 30] });
                    
                    // Add current location marker if available
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            (position) => {
                                const driverCoords = [position.coords.latitude, position.coords.longitude];
                                
                                // Add driver location marker with custom icon
                                const driverIcon = L.divIcon({
                                    className: 'driver-marker',
                                    html: '<div class="driver-marker-inner"></div>',
                                    iconSize: [24, 24],
                                    iconAnchor: [12, 12]
                                });
                                
                                const driverMarker = L.marker(driverCoords, {
                                    icon: driverIcon
                                }).addTo(map{{ $ride->id }});
                                
                                driverMarker.bindPopup("<strong>Your Location</strong>");
                                
                                // Connect driver to pickup with a different style line
                                L.polyline([driverCoords, pickupCoords], {
                                    color: '#10B981',
                                    weight: 3,
                                    opacity: 0.6
                                }).addTo(map{{ $ride->id }});
                                
                                // Extend bounds to include driver
                                bounds.extend(driverCoords);
                                map{{ $ride->id }}.fitBounds(bounds, { padding: [30, 30] });
                            },
                            (error) => {
                                console.log('Error getting current location:', error);
                            }
                        );
                    }
                @else
                    // Create a basic map centered at a default location
                    const map{{ $ride->id }} = L.map('map-{{ $ride->id }}').setView([31.6295, -7.9811], 13); // Default to Marrakech
                    
                    // Add OpenStreetMap tile layer
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                        maxZoom: 19
                    }).addTo(map{{ $ride->id }});
                    
                    // Add a marker for the approximate pickup location
                    L.marker([31.6295, -7.9811], {
                        icon: L.divIcon({
                            className: 'pickup-marker',
                            html: '<div class="pickup-marker-inner"></div>',
                            iconSize: [20, 20],
                            iconAnchor: [10, 10]
                        })
                    }).addTo(map{{ $ride->id }})
                    .bindPopup("<strong>Approximate Location</strong><br>Coordinates not available");
                @endif
            @endforeach
            
            // Add CSS for custom markers
            const style = document.createElement('style');
            style.textContent = `
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
            
            // Function to show response modal
            function showResponseModal(success, message) {
                if (success) {
                    successContent.classList.remove('hidden');
                    errorContent.classList.add('hidden');
                    successMessage.textContent = message;
                } else {
                    successContent.classList.add('hidden');
                    errorContent.classList.remove('hidden');
                    errorMessage.textContent = message;
                }
                
                responseModal.classList.remove('hidden');
            }
            
            // Event listener for mobile menu toggle
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
            
            // Event listener for profile dropdown toggle
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
            
            // Event listener for modal close button
            if (closeModalButton) {
                closeModalButton.addEventListener('click', function() {
                    responseModal.classList.add('hidden');
                });
            }
            
            // Handle form submissions with AJAX
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    const url = this.action;
                    const method = this.method;
                    
                    fetch(url, {
                        method: method,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showResponseModal(true, data.message || 'Request processed successfully.');
                            
                            // Redirect or refresh the page after a delay
                            setTimeout(() => {
                                if (data.redirect) {
                                    window.location.href = data.redirect;
                                } else {
                                    window.location.reload();
                                }
                            }, 1500);
                        } else {
                            showResponseModal(false, data.error || 'An error occurred. Please try again.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showResponseModal(false, 'An error occurred while processing your request.');
                    });
                });
            });
            
            // Resize maps when window resizes
            window.addEventListener('resize', function() {
                @foreach($pendingRides as $ride)
                    map{{ $ride->id }}.invalidateSize();
                @endforeach
            });
        });
    </script>
</body>
</html>