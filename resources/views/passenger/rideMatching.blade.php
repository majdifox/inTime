@extends('passenger.layouts.passenger')

@section('title', 'inTime - Finding Your Driver')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" 
      crossorigin=""/>
@endsection

@section('head-scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" 
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" 
        crossorigin=""></script>
@endsection

@section('content')

<div class="flex flex-col lg:flex-row gap-6">
    <!-- Left Column - Matching Status -->
    <div class="w-full lg:w-1/2 flex flex-col gap-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-center mb-6">
                <h2 class="text-xl font-bold mb-2">Sending request to your driver</h2>
                <p class="text-gray-600" id="matching-status">Waiting for your driver to confirm the ride. This won't take long.</p>
            </div>
            
            <div class="flex justify-center mb-6">
                <!-- Pulse animation to indicate searching -->
                <div class="animate-pulse relative w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center">
                    <div class="absolute w-24 h-24 bg-blue-200 rounded-full animate-ping opacity-75"></div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-blue-500 z-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12h.01M7 12h.01M11 12h.01M15 12h.01" />
                    </svg>
                </div>
            </div>
            
            <div class="mb-6">
                <div class="h-4 bg-gray-200 rounded-full w-full mb-3">
                    <div id="matching-progress" class="h-4 bg-blue-600 rounded-full" style="width: 10%"></div>
                </div>
                <div class="flex justify-between text-xs text-gray-500">
                    <span>Matching</span>
                    <span id="matching-time">0s</span>
                    <span>Driver Found</span>
                </div>
            </div>
            
            <!-- Stats display -->
            <div class="grid grid-cols-2 gap-4 text-center">
                <div class="border rounded-md p-3">
                    <p class="text-sm text-gray-500 mb-1">Requests Sent</p>
                    <p class="text-lg font-bold" id="requests-sent">0</p>
                </div>
                
                <div class="border rounded-md p-3">
                    <p class="text-sm text-gray-500 mb-1">Pending Requests</p>
                    <p class="text-lg font-bold" id="pending-requests">0</p>
                    
                </div>
                
                
            </div>
            <br>
            <br>
            <div class="flex justify-center space-x-4 mb-6">
    <form id="cancel-ride-form" action="{{ route('passenger.cancel.ride', $ride->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this ride request?');">
        @csrf
        <button type="submit" class="bg-red-500 text-white py-2 px-6 rounded-md font-medium hover:bg-red-600 transition">
            Cancel
        </button>
    </form>
</div>

            
            <!-- Driver card (hidden initially) -->
            <div id="driver-found-card" class="hidden border-t border-gray-200 mt-6 pt-6">
                <h3 class="font-bold text-lg mb-4 text-center text-green-600">Driver Found!</h3>
                
                <div class="flex items-start">
                    <div class="h-16 w-16 rounded-full bg-gray-200 overflow-hidden mr-4" id="driver-photo-container">
                        <img id="driver-photo" src="/api/placeholder/64/64" alt="Driver" class="h-full w-full object-cover">
                    </div>
                    <div>
                        <div class="flex justify-between">
                            <h4 class="font-medium text-lg" id="driver-name">-</h4>
                            <div class="flex items-center">
                                <span id="women-only-badge" class="hidden inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-pink-100 text-pink-800 mr-2">
                                    Women Only
                                </span>
                                <span class="text-sm" id="driver-rating">- 
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                        <div class="flex justify-between items-center mt-1">
                            <div class="text-gray-600 text-sm" id="driver-gender">
                                <span class="inline-flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                    </svg>
                                    -
                                </span>
                            </div>
                            <div class="text-sm" id="driver-rides-count">
                                <span>- rides</span>
                            </div>
                        </div>
                        <div class="mt-2 text-gray-600">
                            <p class="text-sm" id="driver-vehicle">
                                <span class="font-medium">-</span>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-between items-center">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm font-medium" id="driver-eta">Arriving in - minutes</span>
                    </div>
                    <a href="#" id="view-ride-button" class="bg-blue-600 text-white py-2 px-4 rounded-md text-sm font-medium hover:bg-blue-700 transition">
                        View Ride
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Ride Details Card -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold mb-4">Ride Details</h2>
            
            <div class="space-y-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0 mt-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <circle cx="12" cy="12" r="8" stroke-width="2" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">Pickup Location</p>
                        <p class="text-sm text-gray-600">{{ $ride->pickup_location }}</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="flex-shrink-0 mt-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">Destination</p>
                        <p class="text-sm text-gray-600">{{ $ride->dropoff_location }}</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="flex-shrink-0 mt-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">Fare</p>
                        <p class="text-sm text-gray-600">
                            MAD {{ number_format($ride->price, 2) }}
                            @if($ride->surge_multiplier > 1)
                                <span class="text-xs text-red-600 ml-1">(Surge x{{ $ride->surge_multiplier }})</span>
                            @endif
                        </p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="flex-shrink-0 mt-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">Vehicle Type</p>
                        <p class="text-sm text-gray-600">{{ ucfirst($ride->vehicle_type) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Right Column - Map -->
    <div class="w-full lg:w-1/2 flex flex-col gap-6">
        <!-- Map Container -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="h-96" id="map"></div>
        </div>
        
        <!-- Ride Status -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold mb-4">Ride Status</h2>
            
            <div class="relative">
                <!-- Timeline -->
                <div class="border-l-2 border-blue-500 ml-4 py-2">
                    <!-- Request Initiated -->
                    <div class="relative mb-8">
                        <div class="absolute -left-4 mt-1">
                            <div class="bg-blue-500 h-6 w-6 rounded-full flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-8">
                            <h3 class="font-medium">Ride Request Initiated</h3>
                            <p class="text-sm text-gray-500">{{ $ride->created_at->format('g:i A') }}</p>
                            
                        </div>
                        
                    </div>
                    
                    <!-- Finding Driver -->
                    <div class="relative mb-8" id="finding-driver-status">
                                <div class="absolute -left-4 mt-1">
                                    <div class="bg-blue-500 h-6 w-6 rounded-full flex items-center justify-center animate-pulse">
                                        <div class="bg-white h-2 w-2 rounded-full"></div>
                                    </div>
                                </div>
                                <div class="ml-8">
                                    <h3 class="font-medium">Finding Driver</h3>
                                    <p class="text-sm text-gray-500">In progress...</p>
                                </div>
                            </div>
                            
                            <!-- Driver Found -->
                            <div class="relative mb-8" id="driver-found-status">
                                <div class="absolute -left-4 mt-1">
                                    <div class="bg-gray-300 h-6 w-6 rounded-full flex items-center justify-center">
                                        <div class="h-2 w-2 rounded-full"></div>
                                    </div>
                                </div>
                                <div class="ml-8">
                                    <h3 class="font-medium text-gray-400">Driver Found</h3>
                                    <p class="text-sm text-gray-400">Pending...</p>
                                </div>
                            </div>
                            
                            <!-- Driver En Route -->
                            <div class="relative mb-8" id="driver-en-route-status">
                                <div class="absolute -left-4 mt-1">
                                    <div class="bg-gray-300 h-6 w-6 rounded-full flex items-center justify-center">
                                        <div class="h-2 w-2 rounded-full"></div>
                                    </div>
                                </div>
                                <div class="ml-8">
                                    <h3 class="font-medium text-gray-400">Driver En Route</h3>
                                    <p class="text-sm text-gray-400">Pending...</p>
                                </div>
                            </div>
                            
                            <!-- Arrival -->
                            <div class="relative" id="arrival-status">
                                <div class="absolute -left-4 mt-1">
                                    <div class="bg-gray-300 h-6 w-6 rounded-full flex items-center justify-center">
                                        <div class="h-2 w-2 rounded-full"></div>
                                    </div>
                                </div>
                                <div class="ml-8">
                                    <h3 class="font-medium text-gray-400">Arrival at Pickup</h3>
                                    <p class="text-sm text-gray-400">Pending...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


    <!-- JavaScript for functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize map
            const map = L.map('map').setView([{{ $ride->pickup_latitude }}, {{ $ride->pickup_longitude }}], 14);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(map);
            
            // Add pickup marker
            const pickupMarker = L.marker([{{ $ride->pickup_latitude }}, {{ $ride->pickup_longitude }}])
                .addTo(map)
                .bindPopup('Pickup Location')
                .openPopup();
                
            // Add dropoff marker
            const dropoffMarker = L.marker([{{ $ride->dropoff_latitude }}, {{ $ride->dropoff_longitude }}])
                .addTo(map)
                .bindPopup('Destination');
                
            // Draw line between pickup and dropoff
            const polyline = L.polyline([
                [{{ $ride->pickup_latitude }}, {{ $ride->pickup_longitude }}],
                [{{ $ride->dropoff_latitude }}, {{ $ride->dropoff_longitude }}]
            ], {
                color: '#4F46E5',
                weight: 5,
                opacity: 0.7
            }).addTo(map);
            
            // Fit map to show both markers
            const bounds = L.latLngBounds([
                [{{ $ride->pickup_latitude }}, {{ $ride->pickup_longitude }}],
                [{{ $ride->dropoff_latitude }}, {{ $ride->dropoff_longitude }}]
            ]);
            map.fitBounds(bounds, { padding: [50, 50] });
            
            // Variables for driver marker
            let driverMarker = null;
            
            // Variables for checking status
            const matchingStartTime = new Date();
            let intervalId = null;
            let progress = 10; // Initial progress bar value
            let driverFound = false;
            
            // Elements for status updates
            const matchingStatus = document.getElementById('matching-status');
            const matchingProgress = document.getElementById('matching-progress');
            const matchingTime = document.getElementById('matching-time');
            const requestsSent = document.getElementById('requests-sent');
            const pendingRequests = document.getElementById('pending-requests');
            
            // Driver found elements
            const driverFoundCard = document.getElementById('driver-found-card');
            const driverPhotoContainer = document.getElementById('driver-photo-container');
            const driverPhoto = document.getElementById('driver-photo');
            const driverName = document.getElementById('driver-name');
            const driverRating = document.getElementById('driver-rating');
            const driverGender = document.getElementById('driver-gender');
            const driverRidesCount = document.getElementById('driver-rides-count');
            const driverVehicle = document.getElementById('driver-vehicle');
            const driverEta = document.getElementById('driver-eta');
            const womenOnlyBadge = document.getElementById('women-only-badge');
            const viewRideButton = document.getElementById('view-ride-button');
            
            // Timeline status elements
            const findingDriverStatus = document.getElementById('finding-driver-status');
            const driverFoundStatus = document.getElementById('driver-found-status');
            const driverEnRouteStatus = document.getElementById('driver-en-route-status');
            const arrivalStatus = document.getElementById('arrival-status');
            
            // Function to check matching status
            function checkMatchingStatus() {
                fetch('{{ route("passenger.matching.status", ["ride" => $ride->id]) }}')
                    .then(response => response.json())
                    .then(data => {
                        // Update status message
                        matchingStatus.textContent = data.message || 'Waiting for your driver to confirm the ride. This won’t take long.';
                        
                        // Update matching stats
                        requestsSent.textContent = data.requests_sent || 0;
                        pendingRequests.textContent = data.pending_requests || 0;
                        
                        // Calculate elapsed time
                        const elapsedSeconds = Math.floor((new Date() - matchingStartTime) / 1000);
                        matchingTime.textContent = elapsedSeconds + 's';
                        
                        // Update progress bar (increment slowly while matching)
                        if (data.status === 'matching' && progress < 60) {
                            progress += 1;
                            matchingProgress.style.width = progress + '%';
                        }
                        
                        // Handle different status responses
                        if (data.status === 'matched') {
                            // Driver found, update UI
                            driverFound = true;
                            matchingProgress.style.width = '100%';
                            
                            // Update timeline
                            findingDriverStatus.querySelector('.bg-blue-500').classList.remove('animate-pulse');
                            findingDriverStatus.querySelector('.bg-blue-500').innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>';
                            
                            driverFoundStatus.querySelector('.bg-gray-300').classList.add('bg-blue-500');
                            driverFoundStatus.querySelector('.bg-gray-300').classList.remove('bg-gray-300');
                            driverFoundStatus.querySelector('.bg-blue-500').classList.add('animate-pulse');
                            driverFoundStatus.querySelector('.bg-blue-500 .h-2').classList.add('bg-white');
                            driverFoundStatus.querySelector('h3').classList.remove('text-gray-400');
                            driverFoundStatus.querySelector('p').classList.remove('text-gray-400');
                            driverFoundStatus.querySelector('p').textContent = 'Driver accepted your ride!';
                            
                            driverEnRouteStatus.querySelector('.bg-gray-300').classList.add('bg-blue-500');
                            driverEnRouteStatus.querySelector('.bg-gray-300').classList.remove('bg-gray-300');
                            driverEnRouteStatus.querySelector('.bg-blue-500').classList.add('animate-pulse');
                            driverEnRouteStatus.querySelector('.bg-blue-500 .h-2').classList.add('bg-white');
                            driverEnRouteStatus.querySelector('h3').classList.remove('text-gray-400');
                            driverEnRouteStatus.querySelector('p').classList.remove('text-gray-400');
                            driverEnRouteStatus.querySelector('p').textContent = 'Driver is heading to your location';
                            
                            // Show driver info
                            driverFoundCard.classList.remove('hidden');
                            
                            // Update driver information
                            if (data.driver.profile_picture) {
                                driverPhoto.src = `/storage/${data.driver.profile_picture}`;
                            }
                            driverName.textContent = data.driver.name;
                            driverRating.textContent = data.driver.rating + ' ';
                            driverGender.innerHTML = `
                                <span class="inline-flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                    </svg>
                                    ${data.driver.gender}
                                </span>
                            `;
                            driverRidesCount.textContent = data.driver.completed_rides + ' rides';
                            driverVehicle.innerHTML = `
                                <span class="font-medium">${data.driver.vehicle.make} ${data.driver.vehicle.model}</span> · 
                                ${data.driver.vehicle.color} · 
                                ${data.driver.vehicle.plate_number}
                            `;
                            driverEta.textContent = `Arriving in ${data.driver.eta_minutes} minutes`;
                            
                            // Show women-only badge if applicable
                            if (data.driver.women_only_driver) {
                                womenOnlyBadge.classList.remove('hidden');
                            }
                            
                            // Add driver marker to map
                            if (data.driver.current_location) {
                                const lat = data.driver.current_location.latitude;
                                const lng = data.driver.current_location.longitude;
                                
                                if (driverMarker) {
                                    driverMarker.setLatLng([lat, lng]);
                                } else {
                                    // Create custom driver marker
                                    const driverIcon = L.divIcon({
                                        className: 'driver-marker',
                                        html: `<div class="driver-marker-inner ${data.driver.women_only_driver ? 'women-only' : ''}"></div>`,
                                        iconSize: [24, 24],
                                        iconAnchor: [12, 12]
                                    });
                                    
                                    driverMarker = L.marker([lat, lng], {
                                        icon: driverIcon
                                    }).addTo(map)
                                    .bindPopup(`${data.driver.name}<br>${data.driver.vehicle.make} ${data.driver.vehicle.model}`);
                                    
                                    // Update map bounds to include driver
                                    bounds.extend([lat, lng]);
                                    map.fitBounds(bounds, { padding: [50, 50] });
                                }
                            }
                            
                            // Set view ride button link
                            viewRideButton.href = data.redirect;
                            
                            // Clear interval once driver is found
                            clearInterval(intervalId);
                            
                            // Auto redirect after a short delay
                            setTimeout(() => {
                                window.location.href = data.redirect;
                            }, 3000);
                        } else if (data.status === 'failed') {
                            // Matching failed
                            clearInterval(intervalId);
                            matchingStatus.textContent = 'No drivers available at this time';
                            matchingStatus.classList.add('text-red-600');
                            
                            // Redirect to dashboard
                            setTimeout(() => {
                                window.location.href = data.redirect || "{{ route('passenger.dashboard') }}";
                            }, 3000);
                        }
                    })
                    .catch(error => {
                        console.error('Error checking matching status:', error);
                    });
            }
            
            // Start checking matching status periodically
            intervalId = setInterval(checkMatchingStatus, 2000);
            
            // Initial status check
            checkMatchingStatus();
            
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
            `;
            document.head.appendChild(style);
        });
    </script>
@endsection