<!-- passenger/activeRide.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>inTime - Active Ride</title>
    
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
            @if(!$activeRide->pickup_time)
            <form id="cancel-ride-form" action="{{ route('passenger.cancel.ride', $activeRide->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this ride?');">
                @csrf
                <button type="submit" class="bg-red-500 text-white py-2 px-6 rounded-md font-medium hover:bg-red-600 transition">
                    Cancel Ride
                </button>
            </form>
            @endif
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
        <!-- Left Column - Ride Info and Driver Details -->
        <div class="w-full lg:w-1/3 flex flex-col gap-6">
            <!-- Ride Status Card -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-start mb-4">
                    <h2 class="text-xl font-bold">Your Ride</h2>
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
                
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mt-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <circle cx="12" cy="12" r="8" stroke-width="2" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium">Pickup Location</p>
                            <p class="text-sm text-gray-600">{{ $activeRide->pickup_location }}</p>
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
                            <p class="text-sm text-gray-600">{{ $activeRide->dropoff_location }}</p>
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
                                MAD {{ number_format($activeRide->price, 2) }}
                                @if($activeRide->surge_multiplier > 1)
                                    <span class="text-xs text-red-600 ml-1">(Surge x{{ $activeRide->surge_multiplier }})</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mt-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium">Scheduled Time</p>
                            <p class="text-sm text-gray-600">{{ $activeRide->reservation_date->format('M d, Y g:i A') }}</p>
                        </div>
                    </div>
                    
                    @if($activeRide->pickup_time)
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mt-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium">Pickup Time</p>
                            <p class="text-sm text-gray-600">{{ $activeRide->pickup_time->format('g:i A') }}</p>
                        </div>
                    </div>
                    @else
                    <div class="flex items-start" id="eta-container">
                        <div class="flex-shrink-0 mt-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium">Estimated Arrival</p>
                            <p class="text-sm text-gray-600" id="eta-time">Calculating...</p>
                        </div>
                    </div>
                    @endif
                    
                    @if($activeRide->vehicle_type)
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mt-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium">Vehicle Type</p>
                            <p class="text-sm text-gray-600">{{ ucfirst($activeRide->vehicle_type) }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Driver Card -->
            @if($activeRide->driver)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold mb-4">Your Driver</h2>
                
                <div class="flex items-start">
                    <div class="h-16 w-16 rounded-full bg-gray-200 overflow-hidden mr-4">
                        @if($activeRide->driver->user->profile_picture)
                            <img src="{{ asset('storage/' . $activeRide->driver->user->profile_picture) }}" alt="Driver" class="h-full w-full object-cover">
                        @else
                            <div class="h-full w-full flex items-center justify-center bg-gray-400 text-white">
                                {{ strtoupper(substr($activeRide->driver->user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <div class="flex justify-between items-start">
                            <h3 class="font-medium text-lg">{{ $activeRide->driver->user->name }}</h3>
                            <div class="flex items-center">
                                @if($activeRide->driver->women_only_driver)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-pink-100 text-pink-800 mr-2">
                                        Women Only
                                    </span>
                                @endif
                                <span class="text-sm">{{ number_format($activeRide->driver->rating, 1) }} 
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center mt-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-sm text-gray-600">{{ ucfirst($activeRide->driver->user->gender) }}</span>
                            
                            <span class="mx-2 text-gray-300">|</span>
                            
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                            </svg>
                            <span class="text-sm text-gray-600">{{ $activeRide->driver->completed_rides }} rides</span>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 border-t border-gray-200 pt-4">
                    <h3 class="font-medium mb-2">Vehicle Information</h3>
                    <div class="flex items-center space-x-4">
                        <div class="h-12 w-12 bg-gray-200 rounded-md overflow-hidden">
                            @if($activeRide->driver->vehicle && $activeRide->driver->vehicle->vehicle_photo)
                                <img src="{{ asset('storage/' . $activeRide->driver->vehicle->vehicle_photo) }}" alt="Vehicle" class="h-full w-full object-cover">
                            @else
                                <div class="h-full w-full flex items-center justify-center text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m-8 6H4m0 0l4 4m-4-4l4-4" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div>
                            <p class="font-medium">{{ $activeRide->driver->vehicle->make }} {{ $activeRide->driver->vehicle->model }}</p>
                            <p class="text-sm text-gray-600">{{ $activeRide->driver->vehicle->color }} · {{ $activeRide->driver->vehicle->plate_number }}</p>
                            <div class="mt-1">
                                <span class="px-2 py-0.5 rounded text-xs font-medium 
                                    @if($activeRide->driver->vehicle->type === 'women')
                                        bg-pink-100 text-pink-800
                                    @elseif($activeRide->driver->vehicle->type === 'share')
                                        bg-blue-100 text-blue-800
                                    @elseif($activeRide->driver->vehicle->type === 'comfort')
                                        bg-green-100 text-green-800
                                    @elseif($activeRide->driver->vehicle->type === 'wav')
                                        bg-purple-100 text-purple-800
                                    @elseif($activeRide->driver->vehicle->type === 'black')
                                        bg-gray-800 text-white
                                    @endif
                                ">
                                    {{ ucfirst($activeRide->driver->vehicle->type) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <div class="flex space-x-3">
                        <a href="tel:{{ $activeRide->driver->user->phone }}" class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-md font-medium hover:bg-blue-700 transition flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                            </svg>
                            Call Driver
                        </a>
                        <button type="button" id="message-driver-btn" class="flex-1 border border-gray-300 text-gray-700 py-2 px-4 rounded-md font-medium hover:bg-gray-50 transition flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd" />
                            </svg>
                            Message
                        </button>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Ride Actions -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold mb-4">Actions</h2>
                
                <div class="space-y-3">
                    <button type="button" id="change-destination-btn" class="w-full bg-gray-100 text-gray-800 py-2 px-4 rounded-md font-medium hover:bg-gray-200 transition flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                        </svg>
                        Change Destination
                    </button>
                    
                    <button type="button" id="report-issue-btn" class="w-full bg-gray-100 text-gray-800 py-2 px-4 rounded-md font-medium hover:bg-gray-200 transition flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        Report an Issue
                    </button>
                </div>
            </div>
        </div>

        <!-- In passenger/activeRide.blade.php in the driver info section -->
        <a href="{{ route('driver.profile', $activeRide->driver->id) }}" class="text-blue-600 hover:text-blue-800 flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
    </svg>
    Driver Profile
</a>
        
        <!-- Right Column - Map and Trip Progress -->
        <div class="w-full lg:w-2/3 flex flex-col gap-6">
            <!-- Map Container -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="h-96" id="map"></div>
            </div>
            
            <!-- Trip Progress -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold mb-4">Trip Progress</h2>
                
                <div class="relative">
                    <!-- Timeline -->
                    <div class="border-l-2 border-blue-500 ml-4 py-2">
                        <!-- Driver Matched -->
                        <div class="relative mb-8">
                            <div class="absolute -left-4 mt-1">
                                <div class="bg-blue-500 h-6 w-6 rounded-full flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-8">
                                <h3 class="font-medium">Driver Matched</h3>
                                <p class="text-sm text-gray-500">{{ $activeRide->updated_at->format('g:i A') }}</p>
                            </div>
                        </div>
                        
                        <!-- Driver En Route -->
                        <div class="relative mb-8" id="driver-en-route-status">
                            <div class="absolute -left-4 mt-1">
                                <div class="h-6 w-6 rounded-full flex items-center justify-center
                                    @if($activeRide->pickup_time)
                                        bg-blue-500
                                    @else
                                        bg-blue-500 animate-pulse
                                    @endif
                                ">
                                    @if($activeRide->pickup_time)
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    @else
                                        <div class="h-2 w-2 bg-white rounded-full"></div>
                                    @endif
                                </div>
                            </div>
                            <div class="ml-8">
                                <h3 class="font-medium">Driver En Route</h3>
                                <p class="text-sm text-gray-500">
                                    @if($activeRide->pickup_time)
                                        Completed
                                    @else
                                        In progress...
                                    @endif
                                </p>
                            </div>
                        </div>
                        
                        <!-- Pickup -->
                        <div class="relative mb-8" id="pickup-status">
                            <div class="absolute -left-4 mt-1">
                                <div class="h-6 w-6 rounded-full flex items-center justify-center
                                    @if($activeRide->pickup_time)
                                        @if($activeRide->dropoff_time)
                                            bg-blue-500
                                        @else
                                            bg-blue-500 animate-pulse
                                        @endif
                                    @else
                                        bg-gray-300
                                    @endif
                                ">
                                    @if($activeRide->pickup_time && $activeRide->dropoff_time)
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    @elseif($activeRide->pickup_time)
                                        <div class="h-2 w-2 bg-white rounded-full"></div>
                                    @endif
                                </div>
                            </div>
                            <div class="ml-8">
                                <h3 class="font-medium @if(!$activeRide->pickup_time) text-gray-400 @endif">Pickup</h3>
                                <p class="text-sm @if(!$activeRide->pickup_time) text-gray-400 @else text-gray-500 @endif">
                                    @if($activeRide->pickup_time)
                                        {{ $activeRide->pickup_time->format('g:i A') }}
                                    @else
                                        Pending...
                                    @endif
                                </p>
                            </div>
                        </div>
                        
                        <!-- Drop-off -->
                        <div class="relative" id="dropoff-status">
                            <div class="absolute -left-4 mt-1">
                                <div class="h-6 w-6 rounded-full flex items-center justify-center
                                    @if($activeRide->dropoff_time)
                                        bg-blue-500
                                    @else
                                        bg-gray-300
                                    @endif
                                ">
                                    @if($activeRide->dropoff_time)
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    @endif
                                </div>
                            </div>
                            <div class="ml-8">
                                <h3 class="font-medium @if(!$activeRide->dropoff_time) text-gray-400 @endif">Drop-off</h3>
                                <p class="text-sm @if(!$activeRide->dropoff_time) text-gray-400 @else text-gray-500 @endif">
                                    @if($activeRide->dropoff_time)
                                        {{ $activeRide->dropoff_time->format('g:i A') }}
                                    @else
                                        Pending...
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Change Destination Modal -->
<div id="change-destination-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
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
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Change Destination</h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">
                            Enter your new destination below. Your fare may be adjusted based on the new route.
                        </p>
                    </div>
                </div>
            </div>
            
            <form id="change-destination-form" class="mt-5">
                @csrf
                <div>
                    <label for="new_destination" class="block text-sm font-medium text-gray-700 mb-1">New Destination</label>
                    <input type="text" id="new_destination" name="new_dropoff_location" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-black focus:border-black sm:text-sm" placeholder="Enter new destination">
                    <input type="hidden" id="new_latitude" name="new_dropoff_latitude">
                    <input type="hidden" id="new_longitude" name="new_dropoff_longitude">
                </div>
                
                <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:col-start-2 sm:text-sm">
                        Update Destination
                    </button>
                    <button type="button" id="cancel-destination-change" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Report Issue Modal -->
<div id="report-issue-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        
        <div class="relative inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div>
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="mt-3 text-center sm:mt-5">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Report an Issue</h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">
                            Please describe the issue you're experiencing with your ride.
                        </p>
                    </div>
                </div>
            </div>
            
            <form id="report-issue-form" class="mt-5">
                <div>
                    <label for="issue_type" class="block text-sm font-medium text-gray-700 mb-1">Issue Type</label>
                    <select id="issue_type" name="issue_type" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-black focus:border-black sm:text-sm">
                        <option value="driver_behavior">Driver Behavior</option>
                        <option value="vehicle_condition">Vehicle Condition</option>
                        <option value="route_issue">Route Issue</option>
                        <option value="payment_problem">Payment Problem</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                
                <div class="mt-4">
                    <label for="issue_description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="issue_description" name="issue_description" rows="4" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-black focus:border-black sm:text-sm"></textarea>
                </div>
                
                <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-yellow-600 text-base font-medium text-white hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 sm:col-start-2 sm:text-sm">
                        Submit Report
                    </button>
                    <button type="button" id="cancel-report" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Message Modal -->
<div id="message-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        
        <div class="relative inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div>
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                </div>
                <div class="mt-3 text-center sm:mt-5">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Message Driver</h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">
                            Send a quick message to your driver.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <div class="space-y-2">
                    <button type="button" class="quick-message w-full inline-flex justify-between items-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                        <span>I'll be right out</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    
                    <button type="button" class="quick-message w-full inline-flex justify-between items-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                        <span>I'm at the pickup point</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    
                    <button type="button" class="quick-message w-full inline-flex justify-between items-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                        <span>I'll be a few minutes late</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
                
                <div class="mt-4">
                    <label for="custom_message" class="block text-sm font-medium text-gray-700 mb-1">Custom Message</label>
                    <textarea id="custom_message" name="custom_message" rows="3" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-black focus:border-black sm:text-sm"></textarea>
                </div>
                
                <div class="mt-5 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                    <button type="button" id="send-message" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:col-start-2 sm:text-sm">
                        Send Message
                    </button>
                    <button type="button" id="cancel-message" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for functionality -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize map
        const map = L.map('map').setView([{{ $activeRide->pickup_latitude }}, {{ $activeRide->pickup_longitude }}], 14);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(map);
        
        // Add pickup marker
        const pickupMarker = L.marker([{{ $activeRide->pickup_latitude }}, {{ $activeRide->pickup_longitude }}])
            .addTo(map)
            .bindPopup('Pickup Location')
            .openPopup();
            
        // Add dropoff marker
        const dropoffMarker = L.marker([{{ $activeRide->dropoff_latitude }}, {{ $activeRide->dropoff_longitude }}])
            .addTo(map)
            .bindPopup('Destination');
            
        // Draw route line
        const routeLine = L.polyline([
            [{{ $activeRide->pickup_latitude }}, {{ $activeRide->pickup_longitude }}],
            [{{ $activeRide->dropoff_latitude }}, {{ $activeRide->dropoff_longitude }}]
        ], {
            color: '#4F46E5',
            weight: 5,
            opacity: 0.7
        }).addTo(map);
        
        // Fit map to show both markers
        const bounds = L.latLngBounds([
            [{{ $activeRide->pickup_latitude }}, {{ $activeRide->pickup_longitude }}],
            [{{ $activeRide->dropoff_latitude }}, {{ $activeRide->dropoff_longitude }}]
        ]);
        map.fitBounds(bounds, { padding: [50, 50] });
        
        // Variables for driver marker
        let driverMarker = null;
        let driverLocation = null;
        
        // Function to update driver location periodically
        function updateDriverLocation() {
            @if($activeRide->driver && !$activeRide->dropoff_time)
            fetch(`/api/driver-location/{{ $activeRide->driver->id }}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.location) {
                        driverLocation = data.location;
                        
                        const lat = data.location.latitude;
                        const lng = data.location.longitude;
                        
                        // Update or create driver marker
                        if (driverMarker) {
                            driverMarker.setLatLng([lat, lng]);
                        } else {
                            // Create custom driver icon
                            const driverIcon = L.divIcon({
                                className: 'driver-marker',
                                html: `<div class="driver-marker-inner {{ $activeRide->driver->women_only_driver ? 'women-only' : '' }}"></div>`,
                                iconSize: [24, 24],
                                iconAnchor: [12, 12]
                            });
                            
                            driverMarker = L.marker([lat, lng], {
                                icon: driverIcon
                            }).addTo(map);
                            
                            // Extend bounds to include driver
                            bounds.extend([lat, lng]);
                            map.fitBounds(bounds, { padding: [50, 50] });
                        }
                        
                        // Update ETA if driver hasn't picked up passenger yet
                        @if(!$activeRide->pickup_time)
                        // Calculate ETA using distance and average speed
                        const distance = calculateDistance(
                            lat, 
                            lng, 
                            {{ $activeRide->pickup_latitude }}, 
                            {{ $activeRide->pickup_longitude }}
                        );
                        
                        // Assuming 30 km/h average speed
                        const etaMinutes = Math.max(1, Math.ceil((distance / 30) * 60));
                        document.getElementById('eta-time').textContent = `${etaMinutes} minutes`;
                        @endif
                    }
                })
                .catch(error => {
                    console.error('Error updating driver location:', error);
                });
            @endif
        }
        
        // Calculate distance between two points using Haversine formula
        function calculateDistance(lat1, lon1, lat2, lon2) {
            // Earth's radius in km
            const R = 6371;
            
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            
            const a = 
                Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * 
                Math.sin(dLon/2) * Math.sin(dLon/2);
                
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            
            return R * c;
        }
        
        // Start updating driver location every 5 seconds if ride is active
        @if($activeRide->driver && !$activeRide->dropoff_time)
        setInterval(updateDriverLocation, 5000);
        updateDriverLocation(); // Initial update
        @endif
        
        // Modal elements
        const changeDestinationModal = document.getElementById('change-destination-modal');
        const changeDestinationBtn = document.getElementById('change-destination-btn');
        const cancelDestinationChangeBtn = document.getElementById('cancel-destination-change');
        
        const reportIssueModal = document.getElementById('report-issue-modal');
        const reportIssueBtn = document.getElementById('report-issue-btn');
        const cancelReportBtn = document.getElementById('cancel-report');
        
        const messageModal = document.getElementById('message-modal');
        const messageDriverBtn = document.getElementById('message-driver-btn');
        const cancelMessageBtn = document.getElementById('cancel-message');
        
        // Open/close change destination modal
        if (changeDestinationBtn) {
            changeDestinationBtn.addEventListener('click', function() {
                changeDestinationModal.classList.remove('hidden');
            });
        }
        
        if (cancelDestinationChangeBtn) {
            cancelDestinationChangeBtn.addEventListener('click', function() {
                changeDestinationModal.classList.add('hidden');
            });
        }
        
        // Open/close report issue modal
        if (reportIssueBtn) {
            reportIssueBtn.addEventListener('click', function() {
                reportIssueModal.classList.remove('hidden');
            });
        }
        
        if (cancelReportBtn) {
            cancelReportBtn.addEventListener('click', function() {
                reportIssueModal.classList.add('hidden');
            });
        }
        
        // Open/close message modal
        if (messageDriverBtn) {
            messageDriverBtn.addEventListener('click', function() {
                messageModal.classList.remove('hidden');
            });
        }
        
        if (cancelMessageBtn) {
            cancelMessageBtn.addEventListener('click', function() {
                messageModal.classList.add('hidden');
            });
        }
        
        // Handle quick message selection
        document.querySelectorAll('.quick-message').forEach(btn => {
            btn.addEventListener('click', function() {
                const message = this.querySelector('span').textContent;
                document.getElementById('custom_message').value = message;
            });
        });
        
        // Handle send message button
        const sendMessageBtn = document.getElementById('send-message');
        if (sendMessageBtn) {
            sendMessageBtn.addEventListener('click', function() {
                const message = document.getElementById('custom_message').value;
                if (message.trim() === '') {
                    alert('Please enter a message');
                    return;
                }
                
                // In a real app, you would send this message to the driver
                // For now, just show a confirmation and close the modal
                alert('Message sent to driver');
                messageModal.classList.add('hidden');
            });
        }
        
       ///here xxxxxxxxxx
        
        // Report issue form submission
        const reportIssueForm = document.getElementById('report-issue-form');
        if (reportIssueForm) {
            reportIssueForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const issueType = document.getElementById('issue_type').value;
                const issueDescription = document.getElementById('issue_description').value;
                
                if (issueDescription.trim() === '') {
                    alert('Please describe the issue');
                    return;
                }
                
                // In a real app, you would send this report to the server
                // For now, just show a confirmation and close the modal
                alert('Your report has been submitted. Our team will review it shortly.');
                reportIssueModal.classList.add('hidden');
            });
        }
        
        // Simple geocoding for the new destination
        const newDestinationInput = document.getElementById('new_destination');
        if (newDestinationInput) {
            newDestinationInput.addEventListener('blur', function() {
                const address = this.value;
                if (address.trim() === '') return;
                
                // In a real app, you would use a geocoding service like Google Maps or Mapbox
                // For this example, use Nominatim (OSM's geocoder)
                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.length > 0) {
                            const result = data[0];
                            document.getElementById('new_latitude').value = result.lat;
                            document.getElementById('new_longitude').value = result.lon;
                        } else {
                            alert('Could not find coordinates for this address');
                        }
                    })
                    .catch(error => {
                        console.error('Error geocoding address:', error);
                    });
            });
        }
        
        // Close modals when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target === changeDestinationModal) {
                changeDestinationModal.classList.add('hidden');
            }
            if (event.target === reportIssueModal) {
                reportIssueModal.classList.add('hidden');
            }
            if (event.target === messageModal) {
                messageModal.classList.add('hidden');
            }
        });
        
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
</body>
</html>