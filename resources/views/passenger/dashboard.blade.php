@extends('passenger.layouts.passenger')

@vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/passenger/passenger-dashboard.js', 'resources/js/passenger/women-only-toggle.js'])

@section('title', 'inTime - Passenger Dashboard')

@section('styles')
    <!-- OpenStreetMap with Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leafle    t.css" 
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" 
          crossorigin=""/>
    <!-- Leaflet Routing Machine for directions -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
@endsection

@section('head-scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" 
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" 
            crossorigin=""></script>
    <script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>
    <script src="{{ asset('js/women-only-toggle.js') }}"></script>
@endsection

@section('content')
    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Left Column - Book a Ride, User Info -->
        <div class="w-full lg:w-1/3 flex flex-col gap-6">
            <!-- Book a Ride Card -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold mb-4">Book a Ride</h2>
                
                <form action="{{ route('passenger.book') }}" method="GET">
                    <div class="space-y-4">
                        <button type="submit" class="w-full bg-black text-white py-3 px-4 rounded-md font-medium hover:bg-gray-800 transition flex items-center justify-center">
                            Book Now
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-.707-5.707a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- User Info Card -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center space-x-4 mb-4">
                    <div class="h-16 w-16 rounded-full bg-gray-300 overflow-hidden">
                        @if(Auth::user()->profile_picture)
                            <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Profile" class="h-full w-full object-cover">
                        @else
                            <div class="h-full w-full flex items-center justify-center bg-gray-400 text-white">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <h2 class="text-xl font-bold">{{ Auth::user()->name }}</h2>
                        <p class="text-gray-600">{{ Auth::user()->email }}</p>
                    </div>
                </div>
                
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Total Rides</span>
                        <span class="font-medium">{{ $passenger->total_rides ?? 0 }}</span>
                    </div>
                    
                    @if(isset($passenger->rating) && $passenger->rating > 0)
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Rating</span>
                        <div class="flex items-center">
                            <span class="font-medium mr-1">{{ number_format($passenger->rating, 1) }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Ride Preferences - Only Women-Only Rides -->
            @if(Auth::user()->gender === 'female')
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold">Women-Only Rides</h2>
                </div>
                
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 mb-1">When enabled, you'll be matched only with female drivers.</p>
                        <div class="text-sm text-gray-600" id="women-only-status">
                            Status: 
                            @if(Auth::user()->women_only_rides)
                            <span class="text-pink-600 font-medium">Enabled</span>
                            @else
                            <span class="text-gray-600 font-medium">Disabled</span>
                            @endif
                        </div>
                    </div>
                    
                    <button type="button" id="women-only-toggle" class="relative inline-flex h-6 w-11 items-center rounded-full {{ Auth::user()->women_only_rides ? 'bg-pink-500' : 'bg-gray-300' }} transition-colors duration-300">
                        <span id="women-only-toggle-dot" class="inline-block h-5 w-5 transform rounded-full bg-white shadow-md transition-transform duration-300 {{ Auth::user()->women_only_rides ? 'translate-x-5' : 'translate-x-1' }}"></span>
                    </button>
                </div>
            </div>
            @endif
        </div>
        
        <!-- Right Column - Map, Active/Recent Rides -->
        <div class="w-full lg:w-2/3 flex flex-col gap-6">
            <!-- Map Container - Enhanced size and better responsiveness -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="h-96 md:h-100 lg:h-120" id="map"></div>
            </div>
            
            <!-- Active Ride -->
            @if($activeRide)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold mb-4">Active Ride</h2>
                
                <div class="border-l-4 border-blue-500 pl-4 py-2">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h3 class="font-medium">
                                @if($activeRide->driver)
                                    Ride with {{ $activeRide->driver->user->name }}
                                @else
                                    Finding a driver...
                                @endif
                            </h3>
                            <p class="text-sm text-gray-500">
                                @if($activeRide->pickup_time)
                                    Started: {{ $activeRide->pickup_time->format('g:i A') }}
                                @elseif($activeRide->reservation_status == 'matching')
                                    Matching with a driver...
                                @else
                                    Scheduled: {{ $activeRide->reservation_date->format('M d, Y g:i A') }}
                                @endif
                            </p>
                        </div>
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
                    
                    <div class="space-y-3 mb-4">
                        <div class="flex items-start">
                            <div class="mt-1 mr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <circle cx="12" cy="12" r="8" stroke-width="2" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium">Pickup Location</p>
                                <p class="text-sm text-gray-600">{{ $activeRide->pickup_location }}</p>
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
                                <p class="text-sm text-gray-600">{{ $activeRide->dropoff_location }}</p>
                            </div>
                        </div>
                        
                        @if($activeRide->price)
                        <div class="flex items-start">
                            <div class="mt-1 mr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium">Price</p>
                                <p class="text-sm text-gray-600">
                                    MAD {{ number_format($activeRide->price, 2) }}
                                    @if($activeRide->surge_multiplier > 1)
                                        <span class="text-xs text-red-600 ml-1">(Surge x{{ $activeRide->surge_multiplier }})</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        @endif
                        
                        @if($activeRide->vehicle_type)
                        <div class="flex items-start">
                            <div class="mt-1 mr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium">Vehicle Type</p>
                                <p class="text-sm text-gray-600">{{ ucfirst($activeRide->vehicle_type) }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    <div class="flex space-x-3">
                        <a href="{{ route('passenger.active.ride') }}" class="bg-black text-white py-2 px-4 rounded-md text-sm font-medium hover:bg-gray-800 transition flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            View Details
                        </a>
                        
                        @if($activeRide->driver && $activeRide->reservation_status == 'accepted')
                            <a href="tel:{{ $activeRide->driver->user->phone }}" class="border border-gray-300 text-gray-700 py-2 px-4 rounded-md text-sm font-medium hover:bg-gray-50 transition flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                </svg>
                                Call Driver
                            </a>
                        @endif
                        
                        @if(in_array($activeRide->reservation_status, ['matching', 'pending', 'accepted']) && !$activeRide->pickup_time)
                            <form action="{{ route('passenger.cancel.ride', $activeRide->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this ride?')">
                                @csrf
                                <button type="submit" class="border border-red-300 text-red-700 py-2 px-4 rounded-md text-sm font-medium hover:bg-red-50 transition flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                    Cancel Ride
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Recent Rides or Historical Rides -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold mb-4">{{ $activeRide ? 'Recent Rides' : 'Your Rides' }}</h2>
                
                @if(count($rideHistory) === 0)
                    <div class="bg-gray-50 rounded-md p-4 text-center">
                        <p class="text-gray-500">You don't have any past rides yet</p>
                        <p class="text-sm text-gray-400 mt-1">Book your first ride to get started</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($rideHistory as $ride)
                            <div class="border rounded-md p-4">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h3 class="font-medium">
                                            @if($ride->driver)
                                                Ride with {{ $ride->driver->user->name }}
                                            @else
                                                Cancelled Ride
                                            @endif
                                        </h3>
                                        <p class="text-sm text-gray-500">{{ $ride->dropoff_time ? $ride->dropoff_time->format('M d, Y g:i A') : $ride->reservation_date->format('M d, Y g:i A') }}</p>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        @if($ride->ride_status == 'completed') 
                                            bg-green-100 text-green-800
                                        @elseif($ride->reservation_status == 'cancelled') 
                                            bg-red-100 text-red-800
                                        @else 
                                            bg-gray-100 text-gray-800
                                        @endif
                                    ">
                                        @if($ride->ride_status == 'completed')
                                            Completed
                                        @elseif($ride->reservation_status == 'cancelled')
                                            Cancelled
                                        @else
                                            {{ ucfirst($ride->reservation_status) }}
                                        @endif
                                    </span>
                                </div>
                                
                                <div class="space-y-2 mb-3">
                                    <div class="flex items-start">
                                        <div class="mt-0.5 mr-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </div>
                                        <p class="text-sm text-gray-600">{{ $ride->pickup_location }} → {{ $ride->dropoff_location }}</p>
                                    </div>
                                    
                                    @if($ride->price)
                                    <div class="flex items-start">
                                        <div class="mt-0.5 mr-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <p class="text-sm text-gray-600">MAD {{ number_format($ride->price, 2) }}</p>
                                    </div>
                                    @endif
                                    
                                    @if($ride->vehicle_type)
                                    <div class="flex items-start">
                                        <div class="mt-0.5 mr-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                        </div>
                                        <p class="text-sm text-gray-600">{{ ucfirst($ride->vehicle_type) }}</p>
                                    </div>
                                    @endif
                                </div>
                                
                                @if($ride->ride_status == 'completed' && !$ride->is_reviewed)
                                    <button type="button" class="text-blue-600 text-sm font-medium hover:text-blue-800" 
                                            onclick="openRateRideModal({{ $ride->id }})">
                                        Rate this ride
                                    </button>
                                @endif
                            </div>
                        @endforeach
                        
                        <div class="text-center mt-4">
                            <a href="{{ route('passenger.history') }}" class="text-blue-600 text-sm font-medium hover:text-blue-800">
                                View all rides
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('modals')
    <!-- Rate Ride Modal -->
    <div id="rate-ride-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            
            <div class="relative inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div>
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-5">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Rate Your Ride</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Please rate your experience and provide any feedback you may have.
                            </p>
                        </div>
                    </div>
                </div>
                
                <form id="rate-ride-form" method="POST" class="mt-5">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                        <div class="flex items-center justify-center space-x-1">
                            <input type="hidden" name="rating" id="rating-value" value="5">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button" class="rating-star" data-value="{{ $i }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                </button>
                            @endfor
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="comment" class="block text-sm font-medium text-gray-700">Comment (Optional)</label>
                        div class="mt-1">
                            <textarea id="comment" name="comment" rows="3" class="shadow-sm focus:ring-black focus:border-black block w-full sm:text-sm border-gray-300 rounded-md"></textarea>
                        </div>
                    </div>
                    
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-black text-base font-medium text-white hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black sm:col-start-2 sm:text-sm">
                            Submit Rating
                        </button>
                        <button type="button" id="cancel-rating" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize map
    const map = L.map('map').setView([31.63, -8.0], 13); // Default center on Marrakech, Morocco
    
    // Add OpenStreetMap tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
    }).addTo(map);
    
    // Variables for modals
    const rateRideModal = document.getElementById('rate-ride-modal');
    const rateRideForm = document.getElementById('rate-ride-form');
    const cancelRatingBtn = document.getElementById('cancel-rating');
    const ratingStars = document.querySelectorAll('.rating-star');
    const ratingValue = document.getElementById('rating-value');
    
    // Variables for markers
    let userMarker = null;
    let driverMarker = null;
    let pickupMarker = null;
    let dropoffMarker = null;
    let routingControl = null;
    
    // Check for user location and center map
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            position => {
                const userCoords = [position.coords.latitude, position.coords.longitude];
                
                // Add user location marker
                userMarker = L.marker(userCoords).addTo(map);
                userMarker.bindPopup('Your current location').openPopup();
                
                // Center map on user location
                map.setView(userCoords, 14);
            },
            error => {
                console.error("Error getting location:", error);
            }
        );
    }
    
    // Function to show ride on map
    function showRideOnMap(pickupLat, pickupLng, dropoffLat, dropoffLng, pickupName, dropoffName) {
        const pickupCoords = [pickupLat, pickupLng];
        const dropoffCoords = [dropoffLat, dropoffLng];
        
        // Add pickup marker
        pickupMarker = L.marker(pickupCoords, {
            alt: 'Pickup Location'
        }).addTo(map);
        pickupMarker.bindPopup(`<strong>Pickup</strong><br>${pickupName}`);
        
        // Add dropoff marker
        dropoffMarker = L.marker(dropoffCoords, {
            alt: 'Dropoff Location'
        }).addTo(map);
        dropoffMarker.bindPopup(`<strong>Dropoff</strong><br>${dropoffName}`);
        
        // Create a bounds object and fit the map to show all markers
        const bounds = L.latLngBounds(pickupCoords, dropoffCoords);
        map.fitBounds(bounds, { padding: [50, 50] });
        
        // Add routing between pickup and dropoff
        routingControl = L.Routing.control({
            waypoints: [
                L.latLng(pickupCoords[0], pickupCoords[1]),
                L.latLng(dropoffCoords[0], dropoffCoords[1])
            ],
            routeWhileDragging: false,
            showAlternatives: false,
            fitSelectedRoutes: false,
            lineOptions: {
                styles: [
                    { color: '#6366F1', opacity: 0.8, weight: 6 },
                    { color: '#4F46E5', opacity: 0.5, weight: 2 }
                ]
            },
            createMarker: function() { return null; } // Don't create default markers
        }).addTo(map);
        
        // Minimize the control sidebar
        routingControl.hide();
    }
    
    // Function to update driver marker on map
    function updateDriverMarker(lat, lng) {
        if (!driverMarker) {
            // Create a custom icon for the driver
            const driverIcon = L.divIcon({
                className: 'custom-driver-marker',
                iconSize: [24, 24],
                iconAnchor: [12, 12],
                popupAnchor: [0, -12]
            });
            
            driverMarker = L.marker([lat, lng], {
                icon: driverIcon,
                alt: 'Driver Location'
            }).addTo(map);
            driverMarker.bindPopup('Driver Location').openPopup();
        } else {
            driverMarker.setLatLng([lat, lng]);
        }
    }
    
    // Open rate ride modal function
    window.openRateRideModal = function(rideId) {
        if (rateRideForm) {
            rateRideForm.action = `/passenger/ride/${rideId}/rate`;
            rateRideModal.classList.remove('hidden');
        }
    }
    
    // Star rating functionality
    if (ratingStars && ratingValue) {
        ratingStars.forEach(star => {
            star.addEventListener('click', function() {
                const value = this.getAttribute('data-value');
                ratingValue.value = value;
                
                // Update star visuals
                ratingStars.forEach(s => {
                    const starValue = s.getAttribute('data-value');
                    const svg = s.querySelector('svg');
                    if (starValue <= value) {
                        svg.classList.add('text-yellow-400');
                        svg.classList.remove('text-gray-300');
                    } else {
                        svg.classList.add('text-gray-300');
                        svg.classList.remove('text-yellow-400');
                    }
                });
            });
        });
    }
    
    // Cancel rating button
    if (cancelRatingBtn) {
        cancelRatingBtn.addEventListener('click', function() {
            rateRideModal.classList.add('hidden');
        });
    }
    
    // Close modals when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target === rateRideModal) {
            rateRideModal.classList.add('hidden');
        }
    });
    
    // Add CSS for driver marker
    const style = document.createElement('style');
    style.innerHTML = `
        .custom-driver-marker {
            background-color: rgb(59, 130, 246);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 24px;
            width: 24px;
            border-radius: 50%;
            box-shadow: rgba(0, 0, 0, 0.16) 0px 4px 16px;
            position: relative;
        }
        
        .custom-driver-marker::after {
            content: "";
            background-color: rgb(255, 255, 255);
            height: 6px;
            width: 6px;
            border-radius: 50%;
            position: absolute;
        }
    `;
    document.head.appendChild(style);
    
    // Refresh map when the window is resized
    window.addEventListener('resize', function() {
        map.invalidateSize();
    });

    // Handle profile dropdown menu
    const profileMenuButton = document.getElementById('profile-menu-button');
    const profileDropdownMenu = document.getElementById('profile-dropdown-menu');
    
    if (profileMenuButton && profileDropdownMenu) {
        profileMenuButton.addEventListener('click', function() {
            profileDropdownMenu.classList.toggle('hidden');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (!profileMenuButton.contains(event.target) && !profileDropdownMenu.contains(event.target)) {
                profileDropdownMenu.classList.add('hidden');
            }
        });
    }
    
    // Women-only toggle functionality
    const womenOnlyToggle = document.getElementById('women-only-toggle');
    
    if (womenOnlyToggle) {
        womenOnlyToggle.addEventListener('click', function() {
            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Send the AJAX request using the simple route
            fetch('/simple-toggle-women-only', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                console.log('Toggle successful:', data);
                // Update UI based on the response
                if (data.is_enabled) {
                    womenOnlyToggle.classList.add('bg-pink-500');
                    womenOnlyToggle.classList.remove('bg-gray-300');
                    document.getElementById('women-only-toggle-dot').classList.add('translate-x-5');
                    document.getElementById('women-only-toggle-dot').classList.remove('translate-x-1');
                    document.getElementById('women-only-status').innerHTML = 'Status: <span class="text-pink-600 font-medium">Enabled</span>';
                } else {
                    womenOnlyToggle.classList.remove('bg-pink-500');
                    womenOnlyToggle.classList.add('bg-gray-300');
                    document.getElementById('women-only-toggle-dot').classList.remove('translate-x-5');
                    document.getElementById('women-only-toggle-dot').classList.add('translate-x-1');
                    document.getElementById('women-only-status').innerHTML = 'Status: <span class="text-gray-600 font-medium">Disabled</span>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to update preference. Please try again.');
            });
        });
    }
    
    // Function to show notification
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 px-4 py-2 rounded shadow-lg z-50 ${type === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`;
        notification.textContent = message;
        document.body.appendChild(notification);
        
        // Remove notification after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
    
    // Conditional code for active ride
    if (document.getElementById('active-ride-map')) {
        // Additional active ride functionality would go here
    }
});
    
</script>
@endsection

