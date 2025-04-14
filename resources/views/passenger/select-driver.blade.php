<!-- passenger/select-driver.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>inTime - Select Driver</title>
    
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
            <a href="{{ route('passenger.book') }}" class="bg-black text-white py-2 px-6 rounded-md font-medium hover:bg-gray-800 transition">
                Back to Booking
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
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Left Column - Driver List -->
            <div class="w-full lg:w-1/2 flex flex-col gap-6">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold">Available Drivers</h2>
                        
                        @if(Auth::user()->gender === 'female')
                        <div class="flex items-center">
                            <label for="women_only_toggle" class="mr-2 text-sm font-medium">Women-Only Drivers</label>
                            <div class="relative inline-block w-10 mr-2 align-middle select-none">
                                <input type="checkbox" id="women_only_toggle" name="women_only_toggle" class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer"
                                       {{ Auth::user()->women_only_rides ? 'checked' : '' }}>
                                <label for="women_only_toggle" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"></label>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    <div class="flex mb-4">
                        <div class="text-xs px-2 py-1 rounded bg-blue-100 text-blue-800 mr-2 flex items-center">
                            <span class="inline-block w-3 h-3 rounded-full bg-green-500 mr-1"></span> Online
                        </div>
                        <div class="text-xs px-2 py-1 rounded bg-pink-100 text-pink-800 mr-2 flex items-center">
                            <span class="inline-block w-3 h-3 rounded-full bg-pink-500 mr-1"></span> Women-Only
                        </div>
                    </div>
                    
                    <div id="drivers-list" class="space-y-4">
                        <!-- Driver list will be populated here -->
                        @if(empty($drivers))
                            <div class="text-center py-8">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <p class="text-gray-500 font-medium">No drivers available in your area</p>
                                <p class="text-sm text-gray-400">Please try again later or check your ride preferences</p>
                            </div>
                        @else
                            @foreach($drivers as $driver)
                                <div class="driver-card border rounded-md p-4 hover:border-blue-300 hover:bg-blue-50 transition-colors cursor-pointer">
                                    <div class="flex items-start">
                                        <div class="h-16 w-16 rounded-full bg-gray-200 overflow-hidden mr-4">
                                            @if($driver['profile_picture'])
                                                <img src="{{ asset('storage/' . $driver['profile_picture']) }}" alt="Driver" class="h-full w-full object-cover">
                                            @else
                                                <img src="/api/placeholder/64/64" alt="Driver" class="h-full w-full object-cover">
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex justify-between">
                                                <h3 class="font-medium text-lg">{{ $driver['name'] }}</h3>
                                                <div class="flex items-center">
                                                    @if($driver['women_only_driver'])
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-pink-100 text-pink-800 mr-2">
                                                            Women Only
                                                        </span>
                                                    @endif
                                                    <span class="text-sm">{{ $driver['rating'] }} 
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                        </svg>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex justify-between items-center mt-1">
                                                <div class="text-gray-600 text-sm">
                                                    <span class="inline-flex items-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                                        </svg>
                                                        {{ ucfirst($driver['gender']) }}
                                                    </span>
                                                </div>
                                                <div class="text-sm">
                                                    <span>{{ $driver['completed_rides'] }} rides</span>
                                                </div>
                                            </div>
                                            <div class="mt-2 text-gray-600">
                                                <p class="text-sm">
                                                    <span class="font-medium">{{ $driver['vehicle']['make'] }} {{ $driver['vehicle']['model'] }}</span> · 
                                                    {{ $driver['vehicle']['color'] }} · 
                                                    {{ $driver['vehicle']['plate_number'] }}
                                                </p>
                                            </div>
                                            <div class="flex justify-between items-center mt-2">
                                                <div>
                                                    <span class="text-xs px-2 py-1 rounded bg-blue-100 text-blue-800">
                                                        {{ ucfirst($driver['vehicle']['type']) }}
                                                    </span>
                                                </div>
                                                <div class="text-sm">
                                                    <span class="font-medium">{{ $driver['distance_km'] }} km away</span> · 
                                                    <span>{{ $driver['eta_minutes'] }} min</span>
                                                </div>
                                            </div>
                                            
                                            <div class="mt-3">
                                                <form action="{{ route('passenger.book.with.driver') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="driver_id" value="{{ $driver['id'] }}">
                                                    <input type="hidden" name="ride_id" value="{{ $rideId }}">
                                                    <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md font-medium hover:bg-blue-700 transition">
                                                        Select Driver
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Right Column - Map & Ride Info -->
            <div class="w-full lg:w-1/2 flex flex-col gap-6">
                <!-- Map Container -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="h-96" id="map"></div>
                </div>
                
                <!-- Ride Details -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold mb-4">Ride Details</h2>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <div class="text-gray-600">Pickup Location</div>
                            <div class="font-medium">{{ $rideInfo['pickup_location'] }}</div>
                        </div>
                        
                        <div class="flex justify-between">
                            <div class="text-gray-600">Destination</div>
                            <div class="font-medium">{{ $rideInfo['dropoff_location'] }}</div>
                        </div>
                        
                        <div class="flex justify-between">
                            <div class="text-gray-600">Distance</div>
                            <div class="font-medium">{{ number_format($rideInfo['distance_km'], 1) }} km</div>
                        </div>
                        
                        <div class="flex justify-between">
                            <div class="text-gray-600">Vehicle Type</div>
                            <div class="font-medium">{{ ucfirst($rideInfo['vehicle_type']) }}</div>
                        </div>
                        
                        <div class="flex justify-between">
                            <div class="text-gray-600">Estimated Fare</div>
                            <div class="font-medium">
                                MAD {{ number_format($rideInfo['price'], 2) }}
                                @if($rideInfo['surge_multiplier'] > 1)
                                    <span class="text-xs text-red-600 ml-1">(Surge x{{ $rideInfo['surge_multiplier'] }})</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-4 border-gray-200">
                    
                    <div>
                        <form action="{{ route('passenger.request.ride') }}" method="POST">
                            @csrf
                            <input type="hidden" name="ride_id" value="{{ $rideId }}">
                            <input type="hidden" name="vehicle_type" value="{{ $rideInfo['vehicle_type'] }}">
                            <button type="submit" class="w-full bg-black text-white py-3 px-4 rounded-md font-medium hover:bg-gray-800 transition">
                                Request Any Available Driver
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- JavaScript for map functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize map
            const map = L.map('map').setView([{{ $rideInfo['pickup_latitude'] }}, {{ $rideInfo['pickup_longitude'] }}], 14);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(map);
            
            // Add pickup marker
            const pickupMarker = L.marker([{{ $rideInfo['pickup_latitude'] }}, {{ $rideInfo['pickup_longitude'] }}])
                .addTo(map)
                .bindPopup('Pickup Location')
                .openPopup();
                
            // Add dropoff marker
            const dropoffMarker = L.marker([{{ $rideInfo['dropoff_latitude'] }}, {{ $rideInfo['dropoff_longitude'] }}])
                .addTo(map)
                .bindPopup('Destination');
                
            // Add drivers to the map
            @foreach($drivers as $driver)
                // Create custom icon for drivers
                const driverIcon = L.divIcon({
                    className: 'driver-marker',
                    html: `<div class="driver-marker-inner ${@json($driver['women_only_driver']) ? 'women-only' : ''}"></div>`,
                    iconSize: [24, 24],
                    iconAnchor: [12, 12]
                });
                
                L.marker([{{ $driver['location']['latitude'] }}, {{ $driver['location']['longitude'] }}], {
                    icon: driverIcon
                }).addTo(map)
                .bindPopup(`<strong>{{ $driver['name'] }}</strong><br>{{ $driver['vehicle']['make'] }} {{ $driver['vehicle']['model'] }}<br>{{ $driver['distance_km'] }} km away`);
            @endforeach
            
            // Draw line between pickup and dropoff
            const polyline = L.polyline([
                [{{ $rideInfo['pickup_latitude'] }}, {{ $rideInfo['pickup_longitude'] }}],
                [{{ $rideInfo['dropoff_latitude'] }}, {{ $rideInfo['dropoff_longitude'] }}]
            ], {
                color: '#4F46E5',
                weight: 5,
                opacity: 0.7
            }).addTo(map);
            
            // Fit map to show all markers
            const bounds = L.latLngBounds([
                [{{ $rideInfo['pickup_latitude'] }}, {{ $rideInfo['pickup_longitude'] }}],
                [{{ $rideInfo['dropoff_latitude'] }}, {{ $rideInfo['dropoff_longitude'] }}]
            ]);
            
            // Add driver markers to bounds
            @foreach($drivers as $driver)
                bounds.extend([{{ $driver['location']['latitude'] }}, {{ $driver['location']['longitude'] }}]);
            @endforeach
            
            map.fitBounds(bounds, { padding: [50, 50] });
            
            // Toggle women-only drivers
            const womenOnlyToggle = document.getElementById('women_only_toggle');
            if (womenOnlyToggle) {
                womenOnlyToggle.addEventListener('change', function() {
                    // Send AJAX request to update preference
                    fetch('{{ route("passenger.toggle.women.only") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            women_only_rides: this.checked
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Reload the page to refresh driver list
                            window.location.reload();
                        }
                    });
                });
            }
            
            // Add custom CSS for driver markers
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
                    border: 3px solid white;
                    border-radius: 50%;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
                }
                
                .driver-marker-inner.women-only {
                    background-color: #EC4899;
                }
                
                .toggle-checkbox:checked {
                    right: 0;
                    border-color: #EC4899;
                }
                
                .toggle-checkbox:checked + .toggle-label {
                    background-color: #EC4899;
                }
                
                .toggle-label {
                    transition: background-color 0.2s ease;
                }
            `;
            document.head.appendChild(style);
        });
    </script>
</body>
</html>