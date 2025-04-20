<!-- resources/views/passenger/profile/public.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $passenger->user->name }} - Passenger Profile | inTime</title>
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <!-- Header/Navigation -->
    <header class="px-4 py-4 flex items-center justify-between bg-white shadow-sm">
        <div class="flex items-center space-x-8">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="text-2xl font-bold">inTime</a>
            
            <!-- Navigation Links -->
            <nav class="hidden md:flex space-x-6">
                @auth
                    @if(Auth::user()->role === 'passenger')
                        <a href="{{ route('passenger.dashboard') }}" class="font-medium">Dashboard</a>
                        <a href="{{ route('passenger.history') }}" class="font-medium">Ride History</a>
                    @elseif(Auth::user()->role === 'driver')
                        <a href="{{ route('driver.dashboard') }}" class="font-medium">Dashboard</a>
                        <a href="{{ route('driver.history') }}" class="font-medium">Ride History</a>
                    @endif
                @endauth
            </nav>
        </div>
        
        <!-- User Profile / Auth Links -->
        <div class="flex items-center space-x-4">
            @auth
                <div class="h-10 w-10 rounded-full bg-gray-300 overflow-hidden">
                    @if(Auth::user()->profile_picture)
                        <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Profile" class="h-full w-full object-cover">
                    @else
                        <div class="h-full w-full flex items-center justify-center text-gray-500 bg-gray-300">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
            @else
                <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900">Login</a>
                <a href="{{ route('register') }}" class="bg-black text-white py-2 px-6 rounded-md font-medium hover:bg-gray-800 transition">Register</a>
            @endauth
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Left Column - Passenger Info -->
            <div class="w-full lg:w-1/3 flex flex-col gap-6">
                <!-- Passenger Profile Card -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center space-x-4 mb-6">
                        <div class="h-20 w-20 rounded-full bg-gray-300 overflow-hidden flex-shrink-0">
                            @if($passenger->user->profile_picture)
                                <img src="{{ asset('storage/' . $passenger->user->profile_picture) }}" alt="{{ $passenger->user->name }}" class="h-full w-full object-cover">
                            @else
                                <div class="h-full w-full flex items-center justify-center bg-gray-400 text-white text-2xl font-bold">
                                    {{ strtoupper(substr($passenger->user->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <h1 class="text-xl font-bold">{{ $passenger->user->name }}</h1>
                            <div class="flex items-center mt-1">
                                <div class="flex items-center">
                                    <span class="text-lg font-medium mr-1">{{ number_format($averageRating, 1) }}</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                </div>
                                <span class="text-gray-500 text-sm ml-2">{{ $ratingsCount }} reviews</span>
                            </div>
                            <p class="text-gray-500 text-sm mt-1">Passenger since {{ $passenger->created_at->format('F Y') }}</p>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <h2 class="text-lg font-semibold mb-2">Stats</h2>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-gray-500 text-sm">Completed Rides</p>
                                    <p class="font-semibold">{{ $completedRides }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-gray-500 text-sm">Rating</p>
                                    <div class="flex items-center">
                                        <span class="font-semibold">{{ number_format($averageRating, 1) }}</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Passenger special attributes section -->
                        @if($passenger->user->gender === 'female' && $passenger->user->women_only_rides)
                            <div>
                                <h2 class="text-lg font-semibold mb-2">Preferences</h2>
                                <div class="flex flex-wrap gap-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-pink-100 text-pink-800">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                        </svg>
                                        Women-Only Rides
                                    </span>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Favorite Ride Types -->
                        @if(count($favoriteVehicleTypes) > 0)
                            <div>
                                <h2 class="text-lg font-semibold mb-2">Favorite Ride Types</h2>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($favoriteVehicleTypes as $type)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                            {{ ucfirst($type['vehicle_type']) }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Private Info Section (only visible to profile owner) -->
                    @if($canViewPrivate)
                        <div class="mt-6 border border-blue-200 rounded-lg p-4 bg-blue-50">
                            <h3 class="font-bold mb-2 text-blue-800">Private Information</h3>
                            <p class="text-sm text-blue-700 mb-3">
                                This information is only visible to you.
                            </p>
                            <div class="bg-white rounded p-3 shadow-sm">
                                <p><strong>Email:</strong> {{ $passenger->user->email }}</p>
                                <p><strong>Phone:</strong> {{ $passenger->user->phone }}</p>
                                
                                <a href="{{ route('passenger.profile.private') }}" class="mt-3 inline-block text-blue-600 hover:text-blue-800">
                                    Go to Private Profile
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Right Column - Reviews & Ratings -->
            <div class="w-full lg:w-2/3 flex flex-col gap-6">
                <!-- Rating Summary Card -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold mb-4">Rating Summary</h2>
                    
                    <div class="flex flex-col md:flex-row gap-8">
                        <!-- Overall Score -->
                        <div class="flex flex-col items-center justify-center">
                            <div class="text-5xl font-bold">{{ number_format($averageRating, 1) }}</div>
                            <div class="flex items-center mt-2">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= round($averageRating))
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-300" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    @endif
                                @endfor
                            </div>
                            <div class="text-gray-500 text-sm mt-1">{{ $ratingsCount }} reviews</div>
                        </div>
                        
                        <!-- Ratings Breakdown -->
                        <div class="flex-1">
                            <div class="space-y-2">
                                @for($i = 5; $i >= 1; $i--)
                                    <div class="flex items-center">
                                        <div class="w-12 text-sm text-gray-600">{{ $i }} stars</div>
                                        <div class="flex-1 mx-2">
                                            <div class="h-2 bg-gray-200 rounded">
                                                @php
                                                    $percentage = $ratingsCount > 0 
                                                        ? ($ratingsBreakdown[$i] / $ratingsCount) * 100 
                                                        : 0;
                                                @endphp
                                                <div class="h-full bg-yellow-500 rounded" style="width: {{ $percentage }}%"></div>
                                            </div>
                                        </div>
                                        <div class="w-12 text-sm text-right text-gray-600">
                                            {{ $ratingsBreakdown[$i] }}
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Reviews Card -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold">Driver Reviews</h2>
                    </div>
                    
                    @if(count($reviews) > 0)
                        <div class="space-y-6">
                            @foreach($reviews as $review)
                                <div class="border-b border-gray-200 pb-4 {{ !$loop->last ? 'mb-4' : '' }}">
                                    <div class="flex items-start">
                                        <div class="h-10 w-10 rounded-full bg-gray-300 overflow-hidden mr-3 flex-shrink-0">
                                            @if($review->reviewer->profile_picture)
                                                <img src="{{ asset('storage/' . $review->reviewer->profile_picture) }}" alt="{{ $review->reviewer->name }}" class="h-full w-full object-cover">
                                            @else
                                                <div class="h-full w-full flex items-center justify-center bg-gray-400 text-white">
                                                    {{ strtoupper(substr($review->reviewer->name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex justify-between">
                                                <div>
                                                    <p class="font-medium">{{ $review->reviewer->name }} <span class="text-gray-500 text-sm">(Driver)</span></p>
                                                    <p class="text-gray-500 text-sm">{{ $review->created_at->format('M d, Y') }}</p>
                                                </div>
                                                <div class="flex items-center">
                                                    <div class="flex">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            @if($i <= $review->rating)
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                                </svg>
                                                            @else
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-300" viewBox="0 0 20 20" fill="currentColor">
                                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                                </svg>
                                                            @endif
                                                        @endfor
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            @if($review->comment)
                                                <p class="mt-2 text-gray-600">{{ $review->comment }}</p>
                                            @endif
                                            
                                            @if($review->ride)
                                                <div class="mt-2 text-xs text-gray-500">
                                                    @if($review->ride->vehicle_type)
                                                        <span class="capitalize">{{ $review->ride->vehicle_type }}</span> ride ·
                                                    @endif
                                                    {{ $review->ride->dropoff_time ? $review->ride->dropoff_time->format('M d, Y') : $review->ride->created_at->format('M d, Y') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $reviews->links() }}
                        </div>
                    @else
                        <div class="bg-gray-50 p-6 rounded-md text-center">
                            <p class="text-gray-500">No reviews yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-white py-8 border-t border-gray-200 mt-8">
        <div class="container mx-auto px-4">
            <div class="text-center">
                <p class="text-gray-600 text-sm">© {{ date('Y') }} inTime. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>