<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $driver->user->name }} - Driver Profile | inTime</title>
    
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
                        <img src="/api/placeholder/40/40" alt="Profile" class="h-full w-full object-cover">
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
            <!-- Left Column - Driver Info -->
            <div class="w-full lg:w-1/3 flex flex-col gap-6">
                <!-- Driver Profile Card -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center space-x-4 mb-6">
                        <div class="h-20 w-20 rounded-full bg-gray-300 overflow-hidden flex-shrink-0">
                            @if($driver->user->profile_picture)
                                <img src="{{ asset('storage/' . $driver->user->profile_picture) }}" alt="{{ $driver->user->name }}" class="h-full w-full object-cover">
                            @else
                                <div class="h-full w-full flex items-center justify-center bg-gray-400 text-white text-2xl font-bold">
                                    {{ strtoupper(substr($driver->user->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <h1 class="text-xl font-bold">{{ $driver->user->name }}</h1>
                            <div class="flex items-center mt-1">
                                <div class="flex items-center">
                                    <span class="text-lg font-medium mr-1">{{ number_format($driver->rating, 1) }}</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                </div>
                                <span class="text-gray-500 text-sm ml-2">{{ $reviewSummary['total_count'] }} reviews</span>
                            </div>
                            <p class="text-gray-500 text-sm mt-1">Driver since {{ $driver->created_at->format('F Y') }}</p>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <h2 class="text-lg font-semibold mb-2">About</h2>
                            <p class="text-gray-600">
                                @if($driver->bio)
                                    {{ $driver->bio }}
                                @else
                                    Professional driver with {{ $driver->completed_rides }} completed rides.
                                @endif
                            </p>
                        </div>
                        
                        <div>
                            <h2 class="text-lg font-semibold mb-2">Stats</h2>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-gray-500 text-sm">Completed Rides</p>
                                    <p class="font-semibold">{{ $driver->completed_rides }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-sm">Response Rate</p>
                                    <p class="font-semibold">{{ isset($driver->response_rate) ? ($driver->response_rate * 100) . '%' : '98%' }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-sm">Cancellation Rate</p>
                                    <p class="font-semibold">{{ isset($driver->cancellation_rate) ? ($driver->cancellation_rate * 100) . '%' : '< 5%' }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-sm">Average ETA</p>
                                    <p class="font-semibold">{{ isset($driver->avg_eta) ? $driver->avg_eta . ' min' : '5-10 min' }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Special attributes section -->
                        <div>
                            <h2 class="text-lg font-semibold mb-2">Driver Attributes</h2>
                            <div class="flex flex-wrap gap-2">
                                <!-- Women-Only Driver Badge (if applicable) -->
                                @if($driver->women_only_driver && $driver->user->gender === 'female')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-pink-100 text-pink-800">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                        </svg>
                                        Women-Only Driver
                                    </span>
                                @endif
                                
                                <!-- Languages (if available) -->
                                @if(isset($driver->languages))
                                    @foreach($driver->languages as $language)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M4.083 9h1.946c.089-1.546.383-2.97.837-4.118A6.004 6.004 0 004.083 9zM10 2a8 8 0 100 16 8 8 0 000-16zm0 2c-.076 0-.232.032-.465.262-.238.234-.497.623-.737 1.182-.389.907-.673 2.142-.766 3.556h3.936c-.093-1.414-.377-2.649-.766-3.556-.24-.56-.5-.948-.737-1.182C10.232 4.032 10.076 4 10 4zm3.971 5c-.089-1.546-.383-2.97-.837-4.118A6.004 6.004 0 0115.917 9h-1.946zm-2.003 2H8.032c.093 1.414.377 2.649.766 3.556.24.56.5.948.737 1.182.233.23.389.262.465.262.076 0 .232-.032.465-.262.238-.234.498-.623.737-1.182.389-.907.673-2.142.766-3.556zm1.166 4.118c.454-1.147.748-2.572.837-4.118h1.946a6.004 6.004 0 01-2.783 4.118zm-6.268 0C6.412 13.97 6.118 12.546 6.03 11H4.083a6.004 6.004 0 002.783 4.118z" clip-rule="evenodd" />
                                        </svg>
                                        {{ $language }}
                                    </span>
                                @endforeach
                                @endif
                                
                                <!-- Experience badge -->
                                @if($driver->completed_rides > 500)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        Experienced Driver
                                    </span>
                                @endif
                                
                                <!-- Top Rated badge -->
                                @if($driver->rating >= 4.8)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        Top Rated
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Vehicle Info Card -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold mb-4">Vehicle Information</h2>
                    
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="h-16 w-16 bg-gray-200 rounded-md overflow-hidden flex-shrink-0">
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
                                <div class="mt-1 px-2 py-1 bg-gray-100 rounded text-sm inline-block">
                                    {{ ucfirst($driver->vehicle->type) }}
                                </div>
                            @else
                                <p class="text-gray-600">Vehicle information not available</p>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Vehicle Features -->
                    @if($driver->vehicle && $driver->vehicle->features->count() > 0)
                        <div class="mt-4">
                            <h3 class="text-md font-medium mb-2">Vehicle Features</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($driver->vehicle->features as $feature)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ ucfirst(str_replace('_', ' ', $feature->feature)) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
                
                <!-- Areas Served -->
                @if(count($recentLocations) > 0)
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-lg font-semibold mb-4">Areas Frequently Served</h2>
                        <div class="flex flex-wrap gap-2">
                            @php $areas = []; @endphp
                            @foreach($recentLocations as $location)
                                @if(!in_array($location['pickup'], $areas))
                                    @php $areas[] = $location['pickup']; @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        {{ $location['pickup'] }}
                                    </span>
                                @endif
                                @if(!in_array($location['dropoff'], $areas))
                                    @php $areas[] = $location['dropoff']; @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        {{ $location['dropoff'] }}
                                    </span>
                                @endif
                                @if(count($areas) >= 8)
                                    @php break; @endphp
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
            
            <!-- Right Column - Reviews & Ratings -->
            <div class="w-full lg:w-2/3 flex flex-col gap-6">
                <!-- Rating Summary Card -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold mb-4">Rating Summary</h2>
                    
                    <div class="flex flex-col md:flex-row gap-8">
                        <!-- Overall Score -->
                        <div class="flex flex-col items-center justify-center">
                            <div class="text-5xl font-bold">{{ number_format($reviewSummary['average_rating'], 1) }}</div>
                            <div class="flex items-center mt-2">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= round($reviewSummary['average_rating']))
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
                            <div class="text-gray-500 text-sm mt-1">{{ $reviewSummary['total_count'] }} reviews</div>
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
                                                    $percentage = $reviewSummary['total_count'] > 0 
                                                        ? ($reviewSummary['ratings_breakdown'][$i] / $reviewSummary['total_count']) * 100 
                                                        : 0;
                                                @endphp
                                                <div class="h-full bg-yellow-500 rounded" style="width: {{ $percentage }}%"></div>
                                            </div>
                                        </div>
                                        <div class="w-12 text-sm text-right text-gray-600">
                                            {{ $reviewSummary['ratings_breakdown'][$i] }}
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
                        <h2 class="text-lg font-semibold">Reviews</h2>
                        
                        @if(Auth::check() && $hasRiddenWith)
                            <a href="#" class="text-blue-600 text-sm hover:text-blue-800">Write a Review</a>
                        @endif
                    </div>
                    
                    @if(count($reviews) > 0)
                        <div class="space-y-6">
                            @foreach($reviews as $review)
                                <div class="border-b border-gray-200 pb-4 {{ !$loop->last ? 'mb-4' : '' }}">
                                    <div class="flex items-start">
                                        <div class="h-10 w-10 rounded-full bg-gray-300 overflow-hidden mr-3 flex-shrink-0">
                                            @if($review->passenger->user->profile_picture)
                                                <img src="{{ asset('storage/' . $review->passenger->user->profile_picture) }}" alt="{{ $review->passenger->user->name }}" class="h-full w-full object-cover">
                                            @else
                                                <div class="h-full w-full flex items-center justify-center bg-gray-400 text-white">
                                                    {{ strtoupper(substr($review->passenger->user->name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex justify-between">
                                                <div>
                                                    <p class="font-medium">{{ $review->passenger->user->name }}</p>
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