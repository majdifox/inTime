<!-- driver/incomingRequests.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>inTime - Incoming Requests</title>
    
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
                <a href="{{ route('driver.awaiting.rides') }}" class="font-medium hover:text-blue-600 transition">Awaiting Rides</a>
                <a href="{{ route('driver.active.rides') }}" class="font-medium hover:text-blue-600 transition">Active Rides</a>
                <a href="{{ route('driver.incoming.requests') }}" class="font-medium text-blue-600 transition">Incoming Requests</a>
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
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile Settings</a>
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
                        <a href="{{ route('driver.incoming.requests') }}" class="font-medium px-3 py-2 rounded-md bg-blue-50 text-blue-600">Incoming Requests</a>
                        <a href="{{ route('driver.history') }}" class="font-medium px-3 py-2 rounded-md hover:bg-gray-100">History</a>
                        <a href="{{ route('driver.earnings') }}" class="font-medium px-3 py-2 rounded-md hover:bg-gray-100">Earnings</a>
                        <a href="{{ route('profile.edit') }}" class="font-medium px-3 py-2 rounded-md hover:bg-gray-100">Profile Settings</a>
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
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Incoming Ride Requests</h1>
                <div class="flex items-center">
                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded inline-flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Quick Response Needed
                    </span>
                </div>
            </div>
            
            @if(count($requests) === 0)
                <div class="bg-gray-50 rounded-md p-8 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <p class="text-gray-500 font-medium">No incoming ride requests</p>
                    <p class="text-sm text-gray-400 mt-1">Active ride requests will appear here</p>
                </div>
            @else
                <div class="space-y-6">
                    @foreach($requests as $request)
                        <div class="border-l-4 border-blue-500 pl-4 py-4 bg-blue-50 rounded-r-md relative overflow-hidden">
                            <!-- Request timer indicator -->
                            <div class="absolute top-0 left-0 h-1 bg-red-500 countdown-indicator" 
                                 data-requested="{{ $request->requested_at }}"
                                 data-request-id="{{ $request->id }}"
                                 style="width: 100%;"></div>
                            
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
                                    15s
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <div class="flex items-start mb-3">
                                        <div class="mr-3 text-gray-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium">Pickup Location</p>
                                            <p class="text-sm text-gray-600">{{ $request->ride->pickup_location }}</p>
                                            <p class="text-xs text-gray-500">
                                                @if(isset($request->ride->distance_in_km))
                                                    {{ number_format($request->ride->distance_in_km, 1) }} km away
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-start">
                                        <div class="mr-3 text-gray-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium">Dropoff Location</p>
                                            <p class="text-sm text-gray-600">{{ $request->ride->dropoff_location }}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div>
                                    <div class="bg-white p-3 rounded-md shadow-sm">
                                        <div class="flex justify-between items-center mb-2">
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
                                                    {{ $request->ride->vehicle_type ? ucfirst($request->ride->vehicle_type) : 'Standard' }}
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-center justify-between text-xs text-gray-500">
                                            <span>
                                                {{ isset($request->ride->distance_in_km) ? number_format($request->ride->distance_in_km, 1) . ' km' : 'Unknown distance' }}
                                            </span>
                                            <span>
                                                {{ isset($request->ride->distance_in_km) ? 'Est. ' . ceil($request->ride->distance_in_km * 2) . ' min' : 'Unknown ETA' }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-2 flex items-center space-x-2">
                                        <div class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                            <span class="text-xs text-gray-500 ml-1">
                                                {{ number_format($request->ride->passenger->rating ?? 0, 1) }}
                                            </span>
                                        </div>
                                        <span class="text-xs text-gray-500">
                                            {{ $request->ride->passenger->total_rides ?? 0 }} rides
                                        </span>
                                    </div>
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

    <!-- JavaScript for functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile menu toggle
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            const closeMobileMenuButton = document.getElementById('close-mobile-menu');
            
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
            
            // Profile dropdown toggle
            const profileButton = document.getElementById('profile-button');
            const profileDropdown = document.getElementById('profile-dropdown');
            
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
            
            // Response modal
            const responseModal = document.getElementById('response-modal');
            const successContent = document.getElementById('success-content');
            const errorContent = document.getElementById('error-content');
            const successMessage = document.getElementById('success-message');
            const errorMessage = document.getElementById('error-message');
            const closeModalButton = document.getElementById('close-modal');
            
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
            
            // Close modal button
            if (closeModalButton) {
                closeModalButton.addEventListener('click', function() {
                    responseModal.classList.add('hidden');
                });
            }
            
            // Accept/Reject request buttons
            document.querySelectorAll('.accept-request, .reject-request').forEach(button => {
                button.addEventListener('click', function() {
                    const requestId = this.dataset.requestId;
                    const response = this.classList.contains('accept-request') ? 'accept' : 'reject';
                    
                    // Disable buttons to prevent multiple clicks
                    const requestContainer = this.closest('.border-l-4');
                    const buttons = requestContainer.querySelectorAll('button');
                    buttons.forEach(btn => {
                        btn.disabled = true;
                        btn.classList.add('opacity-50', 'cursor-not-allowed');
                    });
                    
                    // Update button text
                    this.textContent = response === 'accept' ? 'Accepting...' : 'Declining...';
                    
                    // Send response to server
                    fetch(`{{ url('driver/request') }}/${requestId}/respond`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            response: response
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showResponseModal(true, response === 'accept' 
                                ? 'Ride request accepted successfully!' 
                                : 'Ride request declined.');
                            
                            // Hide the request
                            requestContainer.style.opacity = '0.5';
                            
                            // Redirect if accepted
                            if (response === 'accept' && data.redirect) {
                                setTimeout(() => {
                                    window.location.href = data.redirect;
                                }, 1500);
                            } else {
                                // Remove the request after a delay
                                setTimeout(() => {
                                    requestContainer.remove();
                                    
                                    // If no more requests, show empty state
                                    if (document.querySelectorAll('.border-l-4').length === 0) {
                                        location.reload();
                                    }
                                }, 2000);
                            }
                        } else {
                            // Re-enable buttons on error
                            buttons.forEach(btn => {
                                btn.disabled = false;
                                btn.classList.remove('opacity-50', 'cursor-not-allowed');
                            });
                            
                            // Reset button text
                            this.textContent = response === 'accept' ? 'Accept' : 'Decline';
                            
                            // Show error message
                            showResponseModal(false, data.error || 'An error occurred processing your request.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        
                        // Re-enable buttons on error
                        buttons.forEach(btn => {
                            btn.disabled = false;
                            btn.classList.remove('opacity-50', 'cursor-not-allowed');
                        });
                        
                        // Reset button text
                        this.textContent = response === 'accept' ? 'Accept' : 'Decline';
                        
                        // Show error message
                        showResponseModal(false, 'A network error occurred. Please try again.');
                    });
                });
            });
            
            // Request countdown timers
            document.querySelectorAll('.countdown').forEach(timer => {
                const requestedAt = new Date(timer.dataset.requested);
                const expiresAt = new Date(requestedAt.getTime() + 15 * 1000); // 15 seconds timeout
                
                function updateTimer() {
                    const now = new Date();
                    const remainingMs = expiresAt - now;
                    
                    if (remainingMs <= 0) {
                        // Time expired
                        timer.textContent = 'Expired';
                        timer.classList.remove('text-red-600');
                        timer.classList.add('text-gray-400');
                        
                        // Update visual indicator
                        const indicator = document.querySelector(`.countdown-indicator[data-request-id="${timer.dataset.requestId}"]`);
                        if (indicator) {
                            indicator.style.width = '0%';
                        }
                        
                        // Disable the buttons
                        const requestContainer = timer.closest('.border-l-4');
                        if (requestContainer) {
                            const buttons = requestContainer.querySelectorAll('button');
                            buttons.forEach(btn => {
                                btn.disabled = true;
                                btn.classList.add('opacity-50', 'cursor-not-allowed');
                            });
                            
                            // Add expired message
                            const actionsContainer = requestContainer.querySelector('.flex.space-x-3');
                            if (actionsContainer && !actionsContainer.querySelector('.expired-msg')) {
                                const expiredMsg = document.createElement('div');
                                expiredMsg.className = 'expired-msg text-sm text-gray-500 mt-2 text-center w-full';
                                expiredMsg.textContent = 'This request has expired';
                                actionsContainer.after(expiredMsg);
                            }
                            
                            // Fade out after a delay
                            setTimeout(() => {
                                requestContainer.style.transition = 'opacity 0.5s';
                                requestContainer.style.opacity = '0.5';
                                
                                // Remove the request after another delay
                                setTimeout(() => {
                                    requestContainer.remove();
                                    
                                    // If no more requests, refresh the page
                                    if (document.querySelectorAll('.border-l-4').length === 0) {
                                        location.reload();
                                    }
                                }, 1500);
                            }, 2000);
                        }
                        
                        return; // Stop updating
                    }
                    
                    // Update remaining time
                    const remainingSec = Math.ceil(remainingMs / 1000);
                    timer.textContent = remainingSec + 's';
                    
                    // Update visual indicator (width percentage)
                    const indicator = document.querySelector(`.countdown-indicator[data-request-id="${timer.dataset.requestId}"]`);
                    if (indicator) {
                        const percentage = (remainingMs / (15 * 1000)) * 100;
                        indicator.style.width = percentage + '%';
                    }
                    
                    // Add pulse effect when time is running out
                    if (remainingSec <= 5) {
                        timer.classList.add('animate-pulse');
                    }
                    
                    // Continue updating
                    requestAnimationFrame(updateTimer);
                }
                
                // Start the timer
                updateTimer();
            });
            
            // Auto-refresh page every 30 seconds to check for new requests
            setTimeout(() => {
                location.reload();
            }, 30000);
        });
    </script>
</body>
</html>