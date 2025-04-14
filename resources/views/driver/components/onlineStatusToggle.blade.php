<!-- driver/components/onlineStatusToggle.blade.php -->
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // CSRF Token for AJAX requests
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // UI elements
        const toggleStatusBtn = document.getElementById('toggle-status');
        const toggleCircle = document.getElementById('toggle-circle');
        const toggleText = document.getElementById('toggle-text');
        const locationSharingContainer = document.getElementById('location-sharing-container');
        const statusIndicator = document.getElementById('status-indicator');
        const statusText = document.getElementById('status-text');
        const shareLocationBtn = document.getElementById('share-location');
        const locationStatus = document.getElementById('location-status');
        const locationInfo = document.getElementById('location-info');
        
        // Variables
        let watchId = null;
        let isTrackingLocation = false;
        
        // Toggle driver status
        if (toggleStatusBtn) {
            toggleStatusBtn.addEventListener('click', function() {
                const isCurrentlyOnline = toggleStatusBtn.classList.contains('bg-green-500');
                
                // Update UI first for a snappy response
                if (isCurrentlyOnline) {
                    // Going offline
                    toggleStatusBtn.classList.remove('bg-green-500');
                    toggleStatusBtn.classList.add('bg-gray-300');
                    toggleCircle.classList.remove('translate-x-5');
                    toggleCircle.classList.add('translate-x-1');
                    toggleText.textContent = 'Go Online';
                    locationSharingContainer.classList.add('hidden');
                    if (statusIndicator) {
                        statusIndicator.classList.remove('bg-green-500');
                        statusIndicator.classList.add('bg-red-500');
                        statusText.textContent = 'Offline';
                    }
                } else {
                    // Going online
                    toggleStatusBtn.classList.remove('bg-gray-300');
                    toggleStatusBtn.classList.add('bg-green-500');
                    toggleCircle.classList.remove('translate-x-1');
                    toggleCircle.classList.add('translate-x-5');
                    toggleText.textContent = 'Go Offline';
                    locationSharingContainer.classList.remove('hidden');
                    if (statusIndicator) {
                        statusIndicator.classList.remove('bg-red-500');
                        statusIndicator.classList.add('bg-green-500');
                        statusText.textContent = 'Online';
                    }
                }
                
                // Make AJAX request to update status
                fetch('{{ route("driver.update.status") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        is_online: !isCurrentlyOnline
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        if (data.request_location) {
                            // If we're going online, prompt to share location
                            requestLocationPermission();
                        }
                    } else {
                        console.error('Error updating status:', data.message);
                        // Revert UI if there was an error
                        revertStatusUI(isCurrentlyOnline);
                    }
                })
                .catch(error => {
                    console.error('Error updating status:', error);
                    // Revert UI if there was an error
                    revertStatusUI(isCurrentlyOnline);
                });
            });
        }
        
        // Revert UI helper function
        function revertStatusUI(wasOnline) {
            if (wasOnline) {
                toggleStatusBtn.classList.add('bg-green-500');
                toggleStatusBtn.classList.remove('bg-gray-300');
                toggleCircle.classList.add('translate-x-5');
                toggleCircle.classList.remove('translate-x-1');
                toggleText.textContent = 'Go Offline';
                locationSharingContainer.classList.remove('hidden');
                if (statusIndicator) {
                    statusIndicator.classList.add('bg-green-500');
                    statusIndicator.classList.remove('bg-red-500');
                    statusText.textContent = 'Online';
                }
            } else {
                toggleStatusBtn.classList.add('bg-gray-300');
                toggleStatusBtn.classList.remove('bg-green-500');
                toggleCircle.classList.add('translate-x-1');
                toggleCircle.classList.remove('translate-x-5');
                toggleText.textContent = 'Go Online';
                locationSharingContainer.classList.add('hidden');
                if (statusIndicator) {
                    statusIndicator.classList.add('bg-red-500');
                    statusIndicator.classList.remove('bg-green-500');
                    statusText.textContent = 'Offline';
                }
            }
        }
        
        // Location sharing
        if (shareLocationBtn) {
            shareLocationBtn.addEventListener('click', function() {
                if (!isTrackingLocation) {
                    requestLocationPermission();
                } else {
                    stopLocationTracking();
                }
            });
        }
        
        // Request location permission
        function requestLocationPermission() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    // Success callback
                    function(position) {
                        // Start tracking
                        startLocationTracking();
                        
                        // Send initial location to server
                        updateLocationOnServer(position.coords.latitude, position.coords.longitude);
                    },
                    // Error callback
                    function(error) {
                        console.error('Error obtaining location:', error);
                        alert('Location sharing is required to go online. Please enable location services.');
                        
                        // Revert to offline if location permission denied
                        if (toggleStatusBtn.classList.contains('bg-green-500')) {
                            toggleStatusBtn.click();
                        }
                    },
                    // Options
                    {
                        enableHighAccuracy: true,
                        timeout: 5000,
                        maximumAge: 0
                    }
                );
            } else {
                alert('Geolocation is not supported by this browser.');
            }
        }
        
        // Start tracking location
        function startLocationTracking() {
            locationStatus.textContent = 'Location Shared';
            shareLocationBtn.classList.remove('bg-blue-600');
            shareLocationBtn.classList.add('bg-green-600');
            locationInfo.classList.remove('hidden');
            isTrackingLocation = true;
            
            // Set up continuous location tracking
            watchId = navigator.geolocation.watchPosition(
                function(position) {
                    updateLocationOnServer(position.coords.latitude, position.coords.longitude);
                },
                function(error) {
                    console.error('Error tracking location:', error);
                    stopLocationTracking();
                },
                {
                    enableHighAccuracy: true,
                    maximumAge: 30000,       // Accept positions that are up to 30 seconds old
                    timeout: 27000           // Wait up to 27 seconds for a position
                }
            );
        }
        
        // Stop tracking location
        function stopLocationTracking() {
            if (watchId !== null) {
                navigator.geolocation.clearWatch(watchId);
                watchId = null;
            }
            
            locationStatus.textContent = 'Share Location';
            shareLocationBtn.classList.remove('bg-green-600');
            shareLocationBtn.classList.add('bg-blue-600');
            locationInfo.classList.add('hidden');
            isTrackingLocation = false;
        }
        
        // Update location on server
        function updateLocationOnServer(latitude, longitude) {
            fetch('{{ route("driver.update.location") }}', {
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
                if (!data.status || data.status !== 'success') {
                    console.error('Error updating location:', data.message);
                }
            })
            .catch(error => {
                console.error('Network error when updating location:', error);
            });
        }
        
        // Auto-start location tracking if online
        if (toggleStatusBtn && toggleStatusBtn.classList.contains('bg-green-500')) {
            requestLocationPermission();
        }
    });
</script>