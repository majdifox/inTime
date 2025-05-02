<!-- resources/views/driver/dashboard.blade.php -->
@extends('driver.layouts.driver')

@section('title', 'Driver Dashboard')

@section('content')
    <main class="container mx-auto px-4 py-8 mt-20">
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

        <!-- Check for rides that need rating -->
        @if(session('check_payment_status'))
            <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-6 rounded shadow" role="alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm">{{ session('check_payment_status')['message'] }}</p>
                        <p class="text-sm mt-2">
                            <button type="button" class="check-payment-status font-medium underline" 
                                    data-ride-id="{{ session('check_payment_status')['ride_id'] }}">
                                Check payment status now
                            </button>
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Notification for rides that need rating -->
        @if(isset($ridesToRate) && $ridesToRate->count() > 0)
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow" role="alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm">You have {{ $ridesToRate->count() }} {{ Str::plural('ride', $ridesToRate->count()) }} that need to be rated.</p>
                        <p class="text-sm mt-2">
                            <a href="{{ route('driver.rate.ride', $ridesToRate->first()->id) }}" class="font-medium underline">
                                Rate your passenger now
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        @endif
        
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Left Column - Status, Stats, Vehicle -->
            <div class="w-full lg:w-1/3 flex flex-col gap-6">
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
                            </div>
                            
                            <p class="text-sm text-gray-500 mb-2">When enabled, you'll only receive ride requests from female passengers.</p>
                        </div>
                    @endif
                </div>

                <!-- Incoming Ride Requests -->
                @if(count($rideRequests) > 0)
                    <div class="bg-white rounded-lg shadow-md p-6 ">
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
                                        15s
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

                                <!-- Vehicle Features Requested -->
                                @if(!empty($request->ride->passenger->ride_preferences['vehicle_features']))
                                <div class="flex items-start mt-2">
                                    <div class="mr-3 text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium">Requested Features</p>
                                        <div class="flex flex-wrap gap-1 mt-1">
                                            @foreach($request->ride->passenger->ride_preferences['vehicle_features'] as $feature)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ ucfirst(str_replace('_', ' ', $feature)) }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endif
                                
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
                    @endif

                    @if($driver->vehicle && $driver->vehicle->features->count() > 0)
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
            
            <!-- Right Column - Map and Rides -->
            <div class="w-full lg:w-2/3 flex flex-col gap-6">
                <!-- Map Container -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="h-96" id="map"></div>
                </div>
                
                <!-- Active/Current Ride -->
                @if(count($activeRides) > 0)
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
                                    
                                    <div class="flex items-start">
                                        <div class="mt-1 mr-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium">Fare</p>
                                            <p class="text-sm text-gray-600">MAD {{ number_format($ride->price ?? $ride->ride_cost ?? 0, 2) }}</p>
                                        </div>
                                    </div>
                                    
                                    @if($ride->vehicle_type)
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
                                    @endif
                                    
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
                            
                            <button type="button" class="show-route border border-gray-300 bg-white text-gray-700 py-2 px-4 rounded-md font-medium hover:bg-gray-50 transition"
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
                
        <!-- Upcoming Rides -->
        @if(isset($upcomingRides) && count($upcomingRides) > 0)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold mb-4">Upcoming Rides</h2>
                
                @foreach($upcomingRides as $ride)
                    <div class="border-l-4 border-blue-500 pl-4 py-2 mb-4">
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
                                <p class="text-sm text-gray-500">Scheduled for: {{ Carbon\Carbon::parse($ride->reservation_date)->format('M d, Y g:i A') }}</p>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Upcoming
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
                            
                            <div class="flex items-start">
                                <div class="mt-1 mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium">Fare</p>
                                    <p class="text-sm text-gray-600">MAD {{ number_format($ride->price ?? $ride->ride_cost ?? 0, 2) }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex space-x-3">
                            <button type="button" class="show-route border border-gray-300 bg-white text-gray-700 py-2 px-4 rounded-md font-medium hover:bg-gray-50 transition"
                                    data-pickup-lat="{{ $ride->pickup_latitude }}"
                                    data-pickup-lng="{{ $ride->pickup_longitude }}"
                                    data-dropoff-lat="{{ $ride->dropoff_latitude }}"
                                    data-dropoff-lng="{{ $ride->dropoff_longitude }}">
                                Show Route
                            </button>
                            
                            <button type="button" class="cancel-ride border border-red-300 text-red-600 py-2 px-4 rounded-md font-medium hover:bg-red-50 transition"
                                    data-ride-id="{{ $ride->id }}">
                                Cancel Ride
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
        
        <!-- Pending Ride Requests -->
        @if(isset($pendingRides) && count($pendingRides) > 0)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold mb-4">Pending Ride Requests</h2>
                
                @foreach($pendingRides as $ride)
                    <div class="border-l-4 border-yellow-500 pl-4 py-2 mb-4">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="font-medium">
                                    Request from {{ $ride->passenger->user->name }}
                                    @if($ride->passenger->user->gender === 'female' && $ride->passenger->user->women_only_rides)
                                        <span class="inline-flex items-center ml-1 px-1.5 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800">
                                            Women Only
                                        </span>
                                    @endif
                                </h3>
                                <p class="text-sm text-gray-500">For: {{ Carbon\Carbon::parse($ride->reservation_date)->format('M d, Y g:i A') }}</p>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Pending
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
                            
                            <div class="flex items-start">
                                <div class="mt-1 mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium">Fare</p>
                                    <p class="text-sm text-gray-600">MAD {{ number_format($ride->price ?? $ride->ride_cost ?? 0, 2) }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex space-x-3">
                            <button type="button" class="respond-to-ride bg-green-500 text-white py-2 px-4 rounded-md font-medium hover:bg-green-600 transition flex-1"
                                    data-ride-id="{{ $ride->id }}" data-response="accept">
                                Accept
                            </button>
                            
                            <button type="button" class="respond-to-ride border border-gray-300 text-gray-700 py-2 px-4 rounded-md font-medium hover:bg-gray-50 transition flex-1"
                                    data-ride-id="{{ $ride->id }}" data-response="reject">
                                Decline
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>



 <!-- JavaScript -->
 <script>
document.addEventListener('DOMContentLoaded', function() {
    // CSRF Token for AJAX requests
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Initialize UI components and event listeners
    initUIComponents();
    
    // Initialize debug status information
    initDebugStatus();
    
    // Initialize map and location tracking
    initLocationService();
    
    // Set up heartbeat and auto-offline
    initHeartbeatAndOffline();
    
    // Initialize ride request handling
    initRideRequestHandling();
    
    // Initialize countdown timers
    initRequestTimers();
    
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
                    if (data.success) {
                        if (data.warning) {
                            // Display warning if vehicle type doesn't match
                            const warningElement = document.createElement('div');
                            warningElement.classList.add('mt-1', 'bg-pink-100', 'text-pink-800', 'p-1', 'rounded-md', 'text-sm');
                            warningElement.innerHTML = `<span class="font-medium"></span> ${data.warning}`;
                            
                            // Remove any existing warning
                            const existingWarning = toggleWomenOnly.parentElement.parentElement.querySelector('.bg-yellow-100');
                            if (existingWarning) {
                                existingWarning.remove();
                            }
                            
                            // Add the new warning
                            toggleWomenOnly.parentElement.parentElement.appendChild(warningElement);
                        }
                    } else {
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
                let html = `<div class="alert ${data.debug_info.matching_issues.length > 0 ? 'alert-danger' : 'alert-success'}">
                    <h5>${data.eligibility_summary}</h5>`;
                
                if (data.debug_info.matching_issues.length > 0) {
                    html += `<ul>`;
                    data.debug_info.matching_issues.forEach(issue => {
                        html += `<li>${issue}</li>`;
                    });
                    html += `</ul>`;
                    
                    html += `<h6>How to fix:</h6><ul>`;
                    data.fix_instructions.forEach(instruction => {
                        html += `<li>${instruction}</li>`;
                    });
                    html += `</ul>`;
                }
                
                html += `</div>`;
                
                // Add detailed debug info
                html += `<div class="card mt-3">
                    <div class="card-header">Detailed Debug Information</div>
                    <div class="card-body">
                        <pre>${JSON.stringify(data.debug_info, null, 2)}</pre>
                    </div>
                </div>`;
                
                debugInfo.innerHTML = html;
                debugInfo.classList.remove('d-none');
            }
        })
        .catch(error => {
            console.error('Failed to debug driver status:', error);
        });
    }
    
    // Function to update driver status display
    function updateDriverStatus() {
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
                visibilityStatus.className = 'alert alert-danger';
                visibilityStatus.textContent = 'You are NOT visible to passengers';
            } else {
                visibilityStatus.className = 'alert alert-success';
                visibilityStatus.textContent = 'You are visible to passengers';
            }
            
            // Update individual statuses
            if (statusOnline) {
                statusOnline.textContent = data.debug_info.user.is_online ? 'Online' : 'Offline';
                statusOnline.className = data.debug_info.user.is_online ? 'badge bg-success' : 'badge bg-danger';
            }
            
            if (statusLocation && data.debug_info.location) {
                const isLocationActive = data.debug_info.location.is_recent;
                statusLocation.textContent = isLocationActive ? 'Active' : 'Inactive';
                statusLocation.className = isLocationActive ? 'badge bg-success' : 'badge bg-danger';
                
                // Update location timestamp
                const locationTimestamp = document.getElementById('location-timestamp');
                if (locationTimestamp && data.debug_info.location.last_updated) {
                    locationTimestamp.textContent = new Date(data.debug_info.location.last_updated).toLocaleTimeString();
                }
            }
            
            if (statusAccount) {
                statusAccount.textContent = data.debug_info.user.account_status;
                statusAccount.className = data.debug_info.user.account_status === 'activated' ? 'badge bg-success' : 'badge bg-warning';
            }
            
            if (statusVehicle && data.debug_info.vehicle) {
                statusVehicle.textContent = data.debug_info.vehicle.is_active ? 'Active' : 'Inactive';
                statusVehicle.className = data.debug_info.vehicle.is_active ? 'badge bg-success' : 'badge bg-danger';
            }
        })
        .catch(error => {
            console.error('Failed to update driver status:', error);
        });
    }
    
    // Function to initialize map and location tracking
    function initLocationService() {
        // Initialize map
        let map = null;
        let driverMarker = null;
        let routingControl = null;
        const mapContainer = document.getElementById('map');
        
        if (mapContainer) {
            // Initialize the map
            map = L.map('map').setView([31.6294, -7.9810], 13); // Default to Marrakech
            
            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
            
            // Try to center map on driver's current location
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    map.setView([position.coords.latitude, position.coords.longitude], 15);
                    
                    // Add marker for current position
                    driverMarker = L.marker([position.coords.latitude, position.coords.longitude])
                        .addTo(map)
                        .bindPopup('Your current location')
                        .openPopup();
                    
                    // Make this marker accessible globally
                    window.driverMarker = driverMarker;
                    window.map = map;
                });
            }
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
                    stopLocationTracking();
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
                    startLocationTracking();
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
                startLocationTracking();
            });
        }
        
        // Handle show route button clicks
        document.querySelectorAll('.show-route').forEach(button => {
            button.addEventListener('click', function() {
                const pickupLat = parseFloat(this.dataset.pickupLat);
                const pickupLng = parseFloat(this.dataset.pickupLng);
                const dropoffLat = parseFloat(this.dataset.dropoffLat);
                const dropoffLng = parseFloat(this.dataset.dropoffLng);
                
                if (map && pickupLat && pickupLng && dropoffLat && dropoffLng) {
                    // Clear existing routing control
                    if (routingControl) {
                        map.removeControl(routingControl);
                    }
                    
                    // Create and add new routing control
                    routingControl = L.Routing.control({
                        waypoints: [
                            L.latLng(pickupLat, pickupLng),
                            L.latLng(dropoffLat, dropoffLng)
                        ],
                        routeWhileDragging: false,
                        lineOptions: {
                            styles: [
                                {color: 'black', opacity: 0.15, weight: 9},
                                {color: 'white', opacity: 0.8, weight: 6},
                                {color: '#3388ff', opacity: 1, weight: 4}
                            ]
                        },
                        createMarker: function(i, waypoint, n) {
                            const marker = L.marker(waypoint.latLng);
                            marker.bindPopup(i === 0 ? 'Pickup Location' : 'Dropoff Location');
                            return marker;
                        }
                    }).addTo(map);
                    
                    // Scroll to map
                    mapContainer.scrollIntoView({ behavior: 'smooth' });
                } else {
                    console.error('Invalid coordinates or map not initialized');
                }
            });
        });
    }
    
    // Variables for location tracking
    let watchId = null;
    let locationIntervalId = null;
    let isTrackingLocation = false;
    
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
                    
                    // Then start continuous tracking
                    // Watch position (continuous updates)
                    watchId = navigator.geolocation.watchPosition(
                        function(position) {
                            updateDriverLocation(position.coords.latitude, position.coords.longitude);
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
                        errorElement.classList.remove('d-none');
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
                if (window.map && window.driverMarker) {
                    window.driverMarker.setLatLng([latitude, longitude]);
                }
                
                // Update driver status display
                updateDriverStatus();
            }
        })
        .catch(error => {
            console.error('Network error when updating location:', error);
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
            navigator.sendBeacon('/driver/set-offline', '');
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
        
        // Start and complete ride buttons
        document.querySelectorAll('.start-ride').forEach(button => {
            button.addEventListener('click', function() {
                const rideId = this.dataset.rideId;
                
                // Get current position for verification
                navigator.geolocation.getCurrentPosition(
                    position => {
                        // Disable button to prevent multiple clicks
                        this.disabled = true;
                        this.classList.add('opacity-50', 'cursor-not-allowed');
                        this.textContent = 'Starting Ride...';
                        
                        // Send request to server
                        fetch(`/driver/ride/${rideId}/start`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({
                                latitude: position.coords.latitude,
                                longitude: position.coords.longitude
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Reload page to show updated ride status
                                window.location.reload();
                            } else {
                                // Show error and re-enable button
                                console.error('Error starting ride:', data.message);
                                alert('Error starting ride: ' + data.message);
                                this.disabled = false;
                                this.classList.remove('opacity-50', 'cursor-not-allowed');
                                this.textContent = 'Start Ride';
                            }
                        })
                        .catch(error => {
                            console.error('Network error when starting ride:', error);
                            alert('Network error. Please try again.');
                            this.disabled = false;
                            this.classList.remove('opacity-50', 'cursor-not-allowed');
                            this.textContent = 'Start Ride';
                        });
                    },
                    error => {
                        console.error('Error getting location:', error);
                        alert('Unable to get your location. Please ensure location sharing is enabled.');
                    }
                );
            });
        });
        
        document.querySelectorAll('.complete-ride').forEach(button => {
            button.addEventListener('click', function() {
                const rideId = this.dataset.rideId;
                
                if (confirm('Are you sure you want to complete this ride?')) {
                    // Get current position for verification
                    navigator.geolocation.getCurrentPosition(
                        position => {
                            // Disable button to prevent multiple clicks
                            this.disabled = true;
                            this.classList.add('opacity-50', 'cursor-not-allowed');
                            this.textContent = 'Completing Ride...';
                            
                            // Send request to server
                            fetch(`/driver/ride/${rideId}/complete`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken
                                },
                                body: JSON.stringify({
                                    latitude: position.coords.latitude,
                                    longitude: position.coords.longitude
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Show ride completion summary
                                    const fareDetails = data.fare;
                                    
                                    // Create fare summary modal
                                    const modalHtml = `
                                        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" id="completion-modal">
                                            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md mx-4">
                                                <h3 class="text-xl font-bold mb-4 text-center text-green-600">Ride Completed!</h3>
                                                <div class="space-y-4">
                                                    <div class="flex justify-between items-center border-b pb-2">
                                                        <span class="font-medium">Base Fare</span>
                                                        <span>MAD ${fareDetails.base_fare.toFixed(2)}</span>
                                                    </div>
                                                    <div class="flex justify-between items-center border-b pb-2">
                                                        <span class="font-medium">Distance (${fareDetails.distance_km.toFixed(1)} km)</span>
                                                        <span>MAD ${fareDetails.distance_fare.toFixed(2)}</span>
                                                    </div>
                                                    ${fareDetails.time_fare > 0 ? `
                                                    <div class="flex justify-between items-center border-b pb-2">
                                                        <span class="font-medium">Time (${fareDetails.duration_minutes} min)</span>
                                                        <span>MAD ${fareDetails.time_fare.toFixed(2)}</span>
                                                    </div>` : ''}
                                                    ${fareDetails.waiting_fee > 0 ? `
                                                    <div class="flex justify-between items-center border-b pb-2">
                                                        <span class="font-medium">Waiting Fee</span>
                                                        <span>MAD ${fareDetails.waiting_fee.toFixed(2)}</span>
                                                    </div>` : ''}
                                                    ${fareDetails.surge_multiplier > 1 ? `
                                                    <div class="flex justify-between items-center border-b pb-2">
                                                        <span class="font-medium">Surge Multiplier</span>
                                                        <span>${fareDetails.surge_multiplier}x</span>
                                                    </div>` : ''}
                                                    <div class="flex justify-between items-center pt-2 text-lg font-bold">
                                                        <span>Total Fare</span>
                                                        <span>MAD ${fareDetails.final_fare.toFixed(2)}</span>
                                                    </div>
                                                </div>
                                                <div class="mt-6 flex justify-center">
                                                    <button class="bg-green-600 text-white py-2 px-6 rounded-md font-medium hover:bg-green-700 transition" id="close-modal">
                                                        Done
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                    
                                    // Add modal to document
                                    document.body.insertAdjacentHTML('beforeend', modalHtml);
                                    
                                    // Add event listener to close button
                                    document.getElementById('close-modal').addEventListener('click', function() {
                                        document.getElementById('completion-modal').remove();
                                        // Reload page after modal is closed
                                        window.location.reload();
                                    });
                                } else {
                                    // Show error and re-enable button
                                    console.error('Error completing ride:', data.message);
                                    alert('Error completing ride: ' + data.message);
                                    this.disabled = false;
                                    this.classList.remove('opacity-50', 'cursor-not-allowed');
                                    this.textContent = 'Complete Ride';
                                }
                            })
                            .catch(error => {
                                console.error('Network error when completing ride:', error);
                                alert('Network error. Please try again.');
                                this.disabled = false;
                                this.classList.remove('opacity-50', 'cursor-not-allowed');
                                this.textContent = 'Complete Ride';
                            });
                        },
                        error => {
                            console.error('Error getting location:', error);
                            alert('Unable to get your location. Please ensure location sharing is enabled.');
                        }
                    );
                }
            });
        });
        
        // Handle legacy ride responses
        document.querySelectorAll('.respond-to-ride').forEach(button => {
            button.addEventListener('click', function() {
                const rideId = this.dataset.rideId;
                const response = this.dataset.response;
                
                // Disable all buttons in this container
                const buttonContainer = this.parentNode;
                const buttons = buttonContainer.querySelectorAll('button');
                buttons.forEach(btn => {
                    btn.disabled = true;
                    btn.classList.add('opacity-50', 'cursor-not-allowed');
                });
                
                // Show loading state
                this.textContent = response === 'accept' ? 'Accepting...' : 'Declining...';
                
                // Send response to server
                fetch(`/driver/ride/${rideId}/respond`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        response: response
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Reload page to show updated status
                        window.location.reload();
                    } else {
                        // Show error and re-enable buttons
                        console.error('Error responding to ride:', data.message);
                        alert('Error: ' + (data.message || 'An unexpected error occurred'));
                        buttons.forEach(btn => {
                            btn.disabled = false;
                            btn.classList.remove('opacity-50', 'cursor-not-allowed');
                        });
                        this.textContent = response === 'accept' ? 'Accept' : 'Decline';
                    }
                })
                .catch(error => {
                    console.error('Network error:', error);
                    alert('Network error. Please try again.');
                    buttons.forEach(btn => {
                        btn.disabled = false;
                        btn.classList.remove('opacity-50', 'cursor-not-allowed');
                    });
                    this.textContent = response === 'accept' ? 'Accept' : 'Decline';
                });
            });
        });
        
        // Handle cancel ride button
        document.querySelectorAll('.cancel-ride').forEach(button => {
            button.addEventListener('click', function() {
                const rideId = this.dataset.rideId;
                
                if (confirm('Are you sure you want to cancel this ride? This may affect your driver rating.')) {
                    // Disable button to prevent multiple clicks
                    this.disabled = true;
                    this.classList.add('opacity-50', 'cursor-not-allowed');
                    this.textContent = 'Cancelling...';
                    
                    // Send request to server
                    fetch(`/driver/ride/${rideId}/cancel`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Reload page to show updated status
                            window.location.reload();
                        } else {
                            // Show error and re-enable button
                            console.error('Error cancelling ride:', data.message);
                            alert('Error: ' + (data.message || 'An unexpected error occurred'));
                            this.disabled = false;
                            this.classList.remove('opacity-50', 'cursor-not-allowed');
                            this.textContent = 'Cancel Ride';
                        }
                    })
                    .catch(error => {
                        console.error('Network error:', error);
                        alert('Network error. Please try again.');
                        this.disabled = false;
                        this.classList.remove('opacity-50', 'cursor-not-allowed');
                        this.textContent = 'Cancel Ride';
                    });
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
                                    // expiredMsg.textContent = 'Request expired on UI but still active on server. You can still respond!';
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
});

document.addEventListener('DOMContentLoaded', function() {
        // Add event listener for payment status check buttons
        const checkPaymentButtons = document.querySelectorAll('.check-payment-status');
        checkPaymentButtons.forEach(button => {
            button.addEventListener('click', function() {
                const rideId = this.dataset.rideId;
                checkRidePaymentStatus(rideId);
            });
        });
        
        // Function to check payment status for a ride
        function checkRidePaymentStatus(rideId) {
            // Show loading state
            const button = document.querySelector(`.check-payment-status[data-ride-id="${rideId}"]`);
            const originalText = button.innerText;
            button.innerText = 'Checking...';
            button.disabled = true;
            
            // Fetch payment status
            fetch(`/driver/ride/${rideId}/payment-status`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log(`Payment status for ride ${rideId}:`, data);
                
                // If ride is completed and paid, redirect to rating page
                if (data.ride_status === 'completed' && data.is_paid && !data.is_reviewed_by_driver) {
                    window.location.href = `/driver/ride/${rideId}/rate`;
                } else {
                    // Update button text based on status
                    if (data.is_paid) {
                        button.innerText = 'Payment received! Redirecting...';
                        setTimeout(() => {
                            window.location.href = `/driver/ride/${rideId}/rate`;
                        }, 1000);
                    } else {
                        button.innerText = 'Not paid yet. Try again later.';
                        setTimeout(() => {
                            button.innerText = originalText;
                            button.disabled = false;
                        }, 3000);
                    }
                }
            })
            .catch(error => {
                console.error('Error checking payment status:', error);
                button.innerText = 'Error checking payment';
                setTimeout(() => {
                    button.innerText = originalText;
                    button.disabled = false;
                }, 3000);
            });
        }
        
        // Auto-check payment status every 30 seconds if notification is present
        const paymentNotification = document.querySelector('[data-ride-id]');
        if (paymentNotification) {
            const rideId = paymentNotification.dataset.rideId;
            setInterval(() => {
                fetch(`/driver/ride/${rideId}/payment-status`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.ride_status === 'completed' && data.is_paid && !data.is_reviewed_by_driver) {
                        window.location.href = `/driver/ride/${rideId}/rate`;
                    }
                })
                .catch(error => {
                    console.error('Error in auto-check:', error);
                });
            }, 30000); // Check every 30 seconds
        }
    });
    </script>
@endsection