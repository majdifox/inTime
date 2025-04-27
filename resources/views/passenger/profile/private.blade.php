<!-- resources/views/passenger/profile/private.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>inTime - My Profile</title>
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
                <a href="{{ route('passenger.book') }}" class="font-medium">Book Ride</a>
                <a href="{{ route('passenger.history') }}" class="font-medium">Ride History</a>
            </nav>
        </div>
        
       
        
        <div class="h-10 w-10 rounded-full bg-gray-300 overflow-hidden">
            @if(Auth::user()->profile_picture)
                <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Profile" class="h-full w-full object-cover">
            @else
                <div class="h-full w-full flex items-center justify-center bg-gray-400 text-white">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
            @endif
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <div class="max-w-5xl mx-auto">
            <!-- User Info Section -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                <div class="relative bg-gray-800 text-white p-6">
                    <div class="flex items-center">
                        <div class="h-24 w-24 rounded-full bg-gray-300 overflow-hidden mr-6">
                            @if(Auth::user()->profile_picture)
                                <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Profile" class="h-full w-full object-cover">
                            @else
                                <div class="h-full w-full flex items-center justify-center bg-gray-400 text-white text-2xl">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold">{{ Auth::user()->name }}</h1>
                            <p class="text-gray-300">{{ Auth::user()->email }}</p>
                            <p class="text-gray-300">{{ Auth::user()->phone }}</p>
                            
                            <div class="flex items-center mt-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>Joined {{ Auth::user()->created_at->format('F Y') }}</span>
                            </div>
                        </div>
                        
                        <div class="ml-auto">
                        <a href="{{ route('passenger.profile.private') }}" class="inline-flex items-center px-4 py-2 border border-white rounded-md text-white hover:bg-white hover:text-gray-800 transition">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
    </svg>
    Edit Profile
</a>
                        </div>
                    </div>
                </div>
                
                <!-- Stats Cards -->
                <div class="p-6">
                    <h2 class="text-xl font-bold mb-4">Ride Statistics</h2>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-center p-4 bg-gray-50 rounded-lg shadow-sm">
                            <p class="text-gray-500 text-sm">Total Rides</p>
                            <p class="text-2xl font-bold">{{ $rideStats['total'] }}</p>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg shadow-sm">
                            <p class="text-gray-500 text-sm">Completed</p>
                            <p class="text-2xl font-bold">{{ $rideStats['completed'] }}</p>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg shadow-sm">
                            <p class="text-gray-500 text-sm">Cancelled</p>
                            <p class="text-2xl font-bold">{{ $rideStats['cancelled'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

                <!-- Tabs for different sections -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="border-b border-gray-200">
                        <nav class="flex -mb-px">
                            <button class="tab-btn active-tab px-6 py-4 text-center border-b-2 border-black font-medium text-gray-900 whitespace-nowrap" data-tab="preferences">
                                Ride Preferences
                            </button>
                            <button class="tab-btn px-6 py-4 text-center border-b-2 border-transparent font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap" data-tab="locations">
                                Saved Locations
                            </button>
                            <button class="tab-btn px-6 py-4 text-center border-b-2 border-transparent font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap" data-tab="recent-rides">
                                Recent Rides
                            </button>
                        </nav>
                    </div>

                    <!-- Ride Preferences Tab -->
                    <div id="preferences-tab" class="tab-content p-6">
                        <form action="{{ route('passenger.profile.update.preferences') }}" method="POST" class="space-y-6">
                            @csrf
                            
                            <!-- Vehicle Features -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-3">Vehicle Features</h3>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                    <label class="relative flex items-start">
                                        <input type="checkbox" name="vehicle_features[]" value="ac" class="h-4 w-4 rounded border-gray-300 text-black focus:ring-black"
                                            {{ in_array('ac', $ridePreferences['vehicle_features'] ?? []) ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm text-gray-700">Air Conditioning</span>
                                    </label>
                                    
                                    <label class="relative flex items-start">
                                        <input type="checkbox" name="vehicle_features[]" value="wifi" class="h-4 w-4 rounded border-gray-300 text-black focus:ring-black"
                                            {{ in_array('wifi', $ridePreferences['vehicle_features'] ?? []) ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm text-gray-700">WiFi</span>
                                    </label>
                                    
                                    <label class="relative flex items-start">
                                        <input type="checkbox" name="vehicle_features[]" value="child_seat" class="h-4 w-4 rounded border-gray-300 text-black focus:ring-black"
                                            {{ in_array('child_seat', $ridePreferences['vehicle_features'] ?? []) ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm text-gray-700">Child Seat</span>
                                    </label>
                                    
                                    <label class="relative flex items-start">
                                        <input type="checkbox" name="vehicle_features[]" value="usb_charger" class="h-4 w-4 rounded border-gray-300 text-black focus:ring-black"
                                            {{ in_array('usb_charger', $ridePreferences['vehicle_features'] ?? []) ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm text-gray-700">USB Charger</span>
                                    </label>
                                    
                                    <label class="relative flex items-start">
                                        <input type="checkbox" name="vehicle_features[]" value="pet_friendly" class="h-4 w-4 rounded border-gray-300 text-black focus:ring-black"
                                            {{ in_array('pet_friendly', $ridePreferences['vehicle_features'] ?? []) ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm text-gray-700">Pet Friendly</span>
                                    </label>
                                    
                                    <label class="relative flex items-start">
                                        <input type="checkbox" name="vehicle_features[]" value="luggage_carrier" class="h-4 w-4 rounded border-gray-300 text-black focus:ring-black"
                                            {{ in_array('luggage_carrier', $ridePreferences['vehicle_features'] ?? []) ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm text-gray-700">Luggage Carrier</span>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="border-t pt-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-3">Ride Preferences</h3>
                                <div class="space-y-4">
                                    <!-- Preferred Vehicle Type -->
                                    <div>
                                        <label for="preferred_vehicle_type" class="block text-sm font-medium text-gray-700 mb-1">Preferred Vehicle Type</label>
                                        <select id="preferred_vehicle_type" name="preferred_vehicle_type" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black sm:text-sm">
                                            <option value="">No Preference</option>
                                            <option value="basic" {{ ($ridePreferences['preferred_vehicle_type'] ?? '') == 'basic' ? 'selected' : '' }}>Basic</option>
                                            <option value="comfort" {{ ($ridePreferences['preferred_vehicle_type'] ?? '') == 'comfort' ? 'selected' : '' }}>Comfort</option>
                                            <option value="wav" {{ ($ridePreferences['preferred_vehicle_type'] ?? '') == 'wav' ? 'selected' : '' }}>Wheelchair Accessible</option>
                                            <option value="black" {{ ($ridePreferences['preferred_vehicle_type'] ?? '') == 'black' ? 'selected' : '' }}>Black</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Women Only Rides -->
                                    @if(Auth::user()->gender === 'female')
                                        <div class="flex items-start">
                                            <div class="flex items-center h-5">
                                                <input id="women_only_rides" name="women_only_rides" type="checkbox" value="1" class="h-4 w-4 rounded border-gray-300 text-black focus:ring-black"
                                                    {{ Auth::user()->women_only_rides ? 'checked' : '' }}>
                                            </div>
                                            <div class="ml-3 text-sm">
                                                <label for="women_only_rides" class="font-medium text-gray-700">Women-Only Rides</label>
                                                <p class="text-gray-500">Choose only female drivers for your rides.</p>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <!-- Auto Match -->
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="auto_match" name="auto_match" type="checkbox" value="1" class="h-4 w-4 rounded border-gray-300 text-black focus:ring-black"
                                                {{ ($ridePreferences['auto_match'] ?? false) ? 'checked' : '' }}>
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="auto_match" class="font-medium text-gray-700">Auto Match</label>
                                            <p class="text-gray-500">Automatically match with the nearest available driver.</p>
                                        </div>
                                    </div>
                                    
                                    <!-- Preferred Payment Method -->
                                    <div>
                                        <label for="preferred_payment_method" class="block text-sm font-medium text-gray-700 mb-1">Preferred Payment Method</label>
                                        <select id="preferred_payment_method" name="preferred_payment_method" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black sm:text-sm">
                                            <option value="">No Preference</option>
                                            <option value="cash" {{ ($ridePreferences['preferred_payment_method'] ?? '') == 'cash' ? 'selected' : '' }}>Cash</option>
                                            <option value="credit_card" {{ ($ridePreferences['preferred_payment_method'] ?? '') == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                            <option value="in_app" {{ ($ridePreferences['preferred_payment_method'] ?? '') == 'in_app' ? 'selected' : '' }}>In-App Payment</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="pt-5">
                                <button type="submit" class="bg-black text-white py-2 px-6 rounded-md font-medium hover:bg-gray-800 transition">
                                    Save Preferences
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Saved Locations Tab -->
                    <div id="locations-tab" class="tab-content p-6 hidden">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-xl font-bold">Saved Locations</h2>
                            <button id="add-location-btn" class="bg-black text-white py-2 px-4 rounded-md font-medium hover:bg-gray-800 transition flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Add Location
                            </button>
                        </div>
                        
                        @if(count($savedLocations) === 0)
                            <div class="bg-gray-50 rounded-lg p-6 text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <p class="text-gray-600">No saved locations yet</p>
                                <p class="text-gray-500 text-sm mt-2">Add your frequent destinations for faster booking</p>
                            </div>
                        @else
                            <div class="grid md:grid-cols-2 gap-4">
                                @foreach($savedLocations as $location)
                                    <div class="bg-gray-50 rounded-lg p-4 flex justify-between items-start">
                                        <div>
                                            <div class="flex items-center">
                                                @if($location['type'] === 'home')
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                                    </svg>
                                                @elseif($location['type'] === 'work')
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                    </svg>
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    </svg>
                                                @endif
                                                <span class="font-medium">{{ $location['name'] }}</span>
                                            </div>
                                            <p class="text-sm text-gray-600 mt-1">{{ $location['address'] }}</p>
                                        </div>
                                        <form action="{{ route('passenger.profile.remove.location') }}" method="POST" class="delete-location-form">
                                            @csrf
                                            <input type="hidden" name="location_id" value="{{ $location['id'] }}">
                                            <button type="submit" class="text-red-600 hover:text-red-800">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    
                    <!-- Recent Rides Tab -->
                    <div id="recent-rides-tab" class="tab-content p-6 hidden">
                        <h2 class="text-xl font-bold mb-6">Recent Rides</h2>
                        
                        @if(count($recentRides) === 0)
                            <div class="bg-gray-50 rounded-lg p-6 text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m-4 10H4m0 0l4 4m-4-4l4-4" />
                                </svg>
                                <p class="text-gray-600">No rides yet</p>
                                <div class="mt-4">
                                    <a href="{{ route('passenger.book') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-black hover:bg-gray-800 focus:outline-none">
                                        Book Your First Ride
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach($recentRides as $ride)
                                    <div class="bg-white border rounded-lg shadow-sm p-4">
                                        <div class="flex justify-between items-start mb-3">
                                            <div>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                    @if($ride->ride_status == 'completed') 
                                                        bg-green-100 text-green-800
                                                    @elseif($ride->reservation_status == 'cancelled') 
                                                        bg-red-100 text-red-800
                                                    @else 
                                                        bg-blue-100 text-blue-800
                                                    @endif
                                                ">
                                                    {{ $ride->getStatusText() }}
                                                </span>
                                                <h3 class="text-lg font-semibold mt-1">{{ $ride->dropoff_time ? $ride->dropoff_time->format('M d, Y - g:i A') : $ride->reservation_date->format('M d, Y - g:i A') }}</h3>
                                            </div>
                                            <div class="text-right">
                                                <p class="font-bold">MAD {{ number_format($ride->price, 2) }}</p>
                                                <p class="text-sm text-gray-500">{{ number_format($ride->distance_in_km, 1) }} km</p>
                                            </div>
                                        </div>
                                        
                                        <div class="space-y-2 mb-4">
                                            <div class="flex items-start">
                                                <div class="mt-1 mr-3">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <circle cx="12" cy="12" r="8" stroke-width="2" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-sm text-gray-700">{{ $ride->pickup_location }}</p>
                                                </div>
                                            </div>
                                            
                                            <div class="flex items-start">
                                                <div class="mt-1 mr-3">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-sm text-gray-700">{{ $ride->dropoff_location }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        @if($ride->driver)
                                            <div class="flex items-center justify-between border-t pt-3">
                                                <div class="flex items-center">
                                                    <div class="h-8 w-8 rounded-full bg-gray-300 overflow-hidden mr-2">
                                                        @if($ride->driver->user->profile_picture)
                                                            <img src="{{ asset('storage/' . $ride->driver->user->profile_picture) }}" alt="Driver" class="h-full w-full object-cover">
                                                        @else
                                                            <div class="h-full w-full flex items-center justify-center bg-gray-400 text-white">
                                                                {{ strtoupper(substr($ride->driver->user->name, 0, 1)) }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-medium">{{ $ride->driver->user->name }}</p>
                                                        <p class="text-xs text-gray-500">
                                                            @if($ride->vehicle_type)
                                                                <span class="capitalize">{{ $ride->vehicle_type }}</span> •
                                                            @endif
                                                            @if($ride->driver->vehicle)
                                                                {{ $ride->driver->vehicle->color }} {{ $ride->driver->vehicle->make }} {{ $ride->driver->vehicle->model }}
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                                <a href="{{ route('driver.public.profile', $ride->driver->id) }}" class="text-blue-600 hover:text-blue-800 text-sm">View Profile</a>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                                
                                <div class="text-center pt-2">
                                    <a href="{{ route('passenger.history') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        View All Rides
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Add Location Modal -->
    <div id="add-location-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            
            <div class="relative inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div>
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-5">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Add New Location</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Save locations you visit frequently for faster booking.
                            </p>
                        </div>
                    </div>
                </div>
                
                <form action="{{ route('passenger.profile.add.location') }}" method="POST" class="mt-5">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" name="name" id="name" placeholder="Home, Office, Gym, etc." required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black sm:text-sm">
                    </div>
                    
                    <div class="mb-4">
                        <label for="type" class="block text-sm font-medium text-gray-700">Location Type</label>
                        <select name="type" id="type" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black sm:text-sm">
                            <option value="home">Home</option>
                            <option value="work">Work</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                        <input type="text" name="address" id="address" placeholder="Full address" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black sm:text-sm">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="latitude" class="block text-sm font-medium text-gray-700">Latitude</label>
                            <input type="text" name="latitude" id="latitude" placeholder="e.g. 34.0522" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black sm:text-sm">
                        </div>
                        <div>
                            <label for="longitude" class="block text-sm font-medium text-gray-700">Longitude</label>
                            <input type="text" name="longitude" id="longitude" placeholder="e.g. -118.2437" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black sm:text-sm">
                        </div>
                    </div>
                    
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-black text-base font-medium text-white hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black sm:col-start-2 sm:text-sm">
                            Save Location
                        </button>
                        <button type="button" id="cancel-location-btn" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab switching
            const tabButtons = document.querySelectorAll('.tab-btn');
            const tabContents = document.querySelectorAll('.tab-content');
            
            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const tabName = this.getAttribute('data-tab');
                    
                    // Hide all tabs and remove active class
                    tabContents.forEach(tab => tab.classList.add('hidden'));
                    tabButtons.forEach(btn => {
                        btn.classList.remove('active-tab');
                        btn.classList.remove('border-black');
                        btn.classList.add('border-transparent');
                        btn.classList.remove('text-gray-900');
                        btn.classList.add('text-gray-500');
                    });
                    
                    // Show selected tab and add active class
                    document.getElementById(`${tabName}-tab`).classList.remove('hidden');
                    this.classList.add('active-tab');
                    this.classList.remove('border-transparent');
                    this.classList.add('border-black');
                    this.classList.remove('text-gray-500');
                    this.classList.add('text-gray-900');
                });
            });
            
          // Location modal functionality
          const addLocationBtn = document.getElementById('add-location-btn');
            const addLocationModal = document.getElementById('add-location-modal');
            const cancelLocationBtn = document.getElementById('cancel-location-btn');
            
            if (addLocationBtn && addLocationModal && cancelLocationBtn) {
                addLocationBtn.addEventListener('click', function() {
                    addLocationModal.classList.remove('hidden');
                });
                
                cancelLocationBtn.addEventListener('click', function() {
                    addLocationModal.classList.add('hidden');
                });
                
                // Close modal when clicking outside
                window.addEventListener('click', function(event) {
                    if (event.target === addLocationModal) {
                        addLocationModal.classList.add('hidden');
                    }
                });
            }
            
            // Confirm location deletion
            const deleteLocationForms = document.querySelectorAll('.delete-location-form');
            deleteLocationForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    if (confirm('Are you sure you want to remove this location?')) {
                        this.submit();
                    }
                });
            });
        });
    </script>
</body>
</html>