<!-- resources/views/passenger/availableDrivers.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>inTime - Available Drivers</title>
    
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
                <a href="{{ route('passenger.dashboard') }}" class="font-medium">Dashboard</a>
                <a href="{{ route('passenger.book') }}" class="font-medium text-black border-b-2 border-black">Book a Ride</a>
                <a href="{{ route('passenger.history') }}" class="font-medium">My Rides</a>
                <a href="{{ route('profile.edit') }}" class="font-medium">My Profile</a>
            </nav>
        </div>
        
        <!-- User Profile -->
        <div class="flex items-center space-x-4">
            <div class="h-10 w-10 rounded-full bg-gray-300 overflow-hidden">
                @if(Auth::user()->profile_picture)
                    <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Profile" class="h-full w-full object-cover">
                @else
                    <img src="/api/placeholder/40/40" alt="Profile" class="h-full w-full object-cover">
                @endif
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Available Drivers</h1>
                <a href="{{ route('passenger.book') }}" class="text-black hover:underline flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Back to Search
                </a>
            </div>
            
            <!-- Ride Summary -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-lg font-bold mb-4">Ride Summary</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <p class="text-sm text-gray-500">Pickup</p>
                        <p class="font-medium">{{ session('ride_search.pickup_location') }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500">Dropoff</p>
                        <p class="font-medium">{{ session('ride_search.dropoff_location') }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500">When</p>
                        <p class="font-medium">{{ \Carbon\Carbon::parse(session('ride_search.reservation_date'))->format('M d, Y g:i A') }}</p>
                    </div>
                </div>
                
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                        <p class="text-sm text-gray-500">Estimated Distance</p>
                            <p class="font-medium">{{ number_format($distance, 1) }} miles</p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-500">Estimated Duration</p>
                            <p class="font-medium">{{ $duration }} minutes</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Available Drivers List -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-bold mb-4">Choose a Driver</h2>
                    
                    @if(count($availableDrivers) === 0)
                        <div class="bg-gray-50 rounded-md p-6 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900">No drivers available</h3>
                            <p class="mt-2 text-sm text-gray-500">We couldn't find any available drivers in your area at this time. Please try again later or adjust your search criteria.</p>
                            <a href="{{ route('passenger.book') }}" class="mt-4 inline-block bg-black text-white py-2 px-4 rounded-md font-medium hover:bg-gray-800 transition">
                                Modify Search
                            </a>
                        </div>
                    @else
                        <div class="space-y-4 max-h-[500px] overflow-y-auto pr-2">
                            @foreach($availableDrivers as $driver)
                                <div class="border rounded-md p-4 hover:border-black transition cursor-pointer driver-option"
                                     data-driver-id="{{ $driver->id }}"
                                     data-driver-name="{{ $driver->user->name }}"
                                     data-driver-rating="{{ $driver->rating }}"
                                     data-vehicle-make="{{ $driver->vehicle->make }}"
                                     data-vehicle-model="{{ $driver->vehicle->model }}"
                                     data-vehicle-color="{{ $driver->vehicle->color }}"
                                     data-vehicle-type="{{ $driver->vehicle->type }}"
                                     data-estimated-price="{{ $driver->estimated_price }}"
                                     data-driver-lat="{{ $driver->latitude ?? 0 }}"
                                     data-driver-lng="{{ $driver->longitude ?? 0 }}"
                                     data-driver-distance="{{ number_format($driver->distance, 1) }}">
                                    <div class="flex justify-between items-start">
                                        <div class="flex items-start">
                                            <div class="h-12 w-12 rounded-full bg-gray-300 overflow-hidden mr-3">
                                                @if($driver->user->profile_picture)
                                                    <img src="{{ asset('storage/' . $driver->user->profile_picture) }}" alt="Driver" class="h-full w-full object-cover">
                                                @else
                                                    <img src="/api/placeholder/48/48" alt="Driver" class="h-full w-full object-cover">
                                                @endif
                                            </div>
                                            <div>
                                                <h3 class="font-medium">{{ $driver->user->name }}</h3>
                                                <div class="flex items-center text-sm text-gray-500">
                                                    <span class="mr-1">{{ number_format($driver->rating ?? 0, 1) }}</span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                    <span class="ml-2">{{ $driver->completed_rides ?? 0 }} rides</span>
                                                </div>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ $driver->vehicle->make }} {{ $driver->vehicle->model }} - {{ $driver->vehicle->color }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-lg font-bold">${{ number_format($driver->estimated_price, 2) }}</p>
                                            <p class="text-xs text-gray-500">{{ number_format($driver->distance, 1) }} miles away</p>
                                        </div>
                                    </div>
                                    <div class="mt-4 pt-4 border-t border-gray-100">
                                        <p class="text-sm">
                                            <span class="font-medium">{{ ucfirst($driver->vehicle->type) }}:</span>
                                            @if($driver->vehicle->type === 'share')
                                                Affordable, everyday ride
                                            @elseif($driver->vehicle->type === 'comfort')
                                                Extra legroom, newer vehicles
                                            @elseif($driver->vehicle->type === 'Black')
                                                Luxury vehicle, professional driver
                                            @elseif($driver->vehicle->type === 'WAV')
                                                Wheelchair accessible vehicle
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                
                <!-- Map and Driver Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Map -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="h-80" id="map"></div>
                    </div>
                    
                    <!-- Selected Driver Details and Booking Form -->
                    <div id="driver-details" class="bg-white rounded-lg shadow-md p-6 hidden">
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <h2 class="text-lg font-bold" id="selected-driver-name"></h2>
                                <div class="flex items-center text-sm text-gray-500">
                                    <span class="mr-1" id="selected-driver-rating"></span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold" id="selected-driver-price"></p>
                                <p class="text-sm text-gray-500" id="selected-driver-distance"></p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div class="bg-gray-50 rounded-md p-4">
                                <p class="text-sm text-gray-500">Vehicle</p>
                                <p class="font-medium" id="selected-driver-vehicle"></p>
                            </div>
                            
                            <div class="bg-gray-50 rounded-md p-4">
                                <p class="text-sm text-gray-500">Type</p>
                                <p class="font-medium" id="selected-driver-type"></p>
                            </div>
                        </div>
                        
                        <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4">
                            <div class="space-y-2 flex-1">
                                <div class="flex items-start">
                                    <div class="mt-1 mr-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <circle cx="12" cy="12" r="8" stroke-width="2" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium">Pickup Location</p>
                                        <p class="text-sm text-gray-600">{{ session('ride_search.pickup_location') }}</p>
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
                                        <p class="text-sm text-gray-600">{{ session('ride_search.dropoff_location') }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="md:w-1/3">
                                <form action="{{ route('passenger.book.with.driver') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="driver_id" id="book-driver-id">
                                    <input type="hidden" name="estimated_price" id="book-estimated-price">
                                    
                                    <button type="submit" class="w-full bg-black text-white py-3 px-4 rounded-md font-medium hover:bg-gray-800 transition">
                                        Book This Ride
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Default State -->
                    <div id="default-message" class="bg-white rounded-lg shadow-md p-6 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7l4-4m0 0l4 4m-4-4v18" />
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900">Select a driver</h3>
                        <p class="mt-2 text-sm text-gray-500">Click on a driver from the list to view more details and book your ride.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <!-- JavaScript for the map and driver selection -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize the map
            const map = L.map('map').setView([
                {{ session('ride_search.pickup_latitude') }}, 
                {{ session('ride_search.pickup_longitude') }}
            ], 12);
            
            // Add OpenStreetMap tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(map);
            
                       // Add pickup and dropoff markers
                       const pickupIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });
            
            const dropoffIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });
            
            const driverIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });
            
            const pickupCoords = [
                {{ session('ride_search.pickup_latitude') }}, 
                {{ session('ride_search.pickup_longitude') }}
            ];
            
            const dropoffCoords = [
                {{ session('ride_search.dropoff_latitude') }}, 
                {{ session('ride_search.dropoff_longitude') }}
            ];
            
            const pickupMarker = L.marker(pickupCoords, {icon: pickupIcon}).addTo(map);
            pickupMarker.bindPopup("Pickup: {{ session('ride_search.pickup_location') }}");
            
            const dropoffMarker = L.marker(dropoffCoords, {icon: dropoffIcon}).addTo(map);
            dropoffMarker.bindPopup("Dropoff: {{ session('ride_search.dropoff_location') }}");
            
            // Add routing between pickup and dropoff
            const routingControl = L.Routing.control({
                waypoints: [
                    L.latLng(pickupCoords[0], pickupCoords[1]),
                    L.latLng(dropoffCoords[0], dropoffCoords[1])
                ],
                routeWhileDragging: false,
                showAlternatives: true,
                fitSelectedRoutes: true,
                lineOptions: {
                    styles: [
                        {color: 'black', opacity: 0.15, weight: 9},
                        {color: 'white', opacity: 0.8, weight: 6},
                        {color: '#0066CC', opacity: 1, weight: 4}
                    ]
                },
                createMarker: function() { return null; } // Don't create default markers
            }).addTo(map);
            
            // Fit map bounds to show pickup and dropoff
            const bounds = L.latLngBounds(pickupCoords, dropoffCoords);
            map.fitBounds(bounds, { padding: [50, 50] });
            
            // Driver markers
            const driverMarkers = [];
            
            // Variables for driver selection UI
            const driverOptions = document.querySelectorAll('.driver-option');
            const driverDetails = document.getElementById('driver-details');
            const defaultMessage = document.getElementById('default-message');
            
            // Selected driver information elements
            const selectedDriverName = document.getElementById('selected-driver-name');
            const selectedDriverRating = document.getElementById('selected-driver-rating');
            const selectedDriverPrice = document.getElementById('selected-driver-price');
            const selectedDriverDistance = document.getElementById('selected-driver-distance');
            const selectedDriverVehicle = document.getElementById('selected-driver-vehicle');
            const selectedDriverType = document.getElementById('selected-driver-type');
            
            // Form hidden inputs
            const bookDriverId = document.getElementById('book-driver-id');
            const bookEstimatedPrice = document.getElementById('book-estimated-price');
            
            // Function to display driver info
            function showDriverInfo(driverId, driverName, driverRating, vehicleMake, vehicleModel, vehicleColor, 
                               vehicleType, estimatedPrice, driverLat, driverLng, driverDistance) {
                // Update info panels
                selectedDriverName.textContent = driverName;
                selectedDriverRating.textContent = driverRating;
                selectedDriverPrice.textContent = '
