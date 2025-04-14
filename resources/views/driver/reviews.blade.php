<!-- driver/reviews.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>inTime - My Reviews</title>
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
                    <a href="{{ route('driver.reviews') }}" class="block px-4 py-2 text-sm text-blue-600 bg-gray-100">My Reviews</a>
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
                        <a href="{{ route('driver.history') }}" class="font-medium px-3 py-2 rounded-md hover:bg-gray-100">History</a>
                        <a href="{{ route('driver.earnings') }}" class="font-medium px-3 py-2 rounded-md hover:bg-gray-100">Earnings</a>
                        <a href="{{ route('profile.edit') }}" class="font-medium px-3 py-2 rounded-md hover:bg-gray-100">Profile Settings</a>
                        <a href="{{ route('driver.reviews') }}" class="font-medium px-3 py-2 rounded-md bg-blue-50 text-blue-600">My Reviews</a>
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
                <h1 class="text-2xl font-bold">My Reviews</h1>
                
                <div class="flex items-center space-x-2">
                    <div class="flex items-center">
                        <div class="flex">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= round($driver->rating ?? 0))
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
                        <span class="ml-2 font-medium">{{ number_format($driver->rating ?? 0, 1) }}/5.0</span>
                    </div>
                    <span class="text-gray-500">·</span>
                    <span class="text-gray-500">{{ $reviews->total() ?? 0 }} {{ Str::plural('review', $reviews->total() ?? 0) }}</span>
                </div>
            </div>
            
            <!-- Filter Options -->
            <div class="mb-6 flex flex-wrap gap-3">
                <a href="{{ route('driver.reviews', ['rating' => 'all']) }}" class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium {{ request('rating') == 'all' || !request('rating') ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' }}">
                    All Reviews
                </a>
                <a href="{{ route('driver.reviews', ['rating' => 5]) }}" class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium {{ request('rating') == 5 ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    5 Stars
                </a>
                <a href="{{ route('driver.reviews', ['rating' => 4]) }}" class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium {{ request('rating') == 4 ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    4 Stars
                </a>
                <a href="{{ route('driver.reviews', ['rating' => 3]) }}" class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium {{ request('rating') == 3 ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    3 Stars
                </a>
                <a href="{{ route('driver.reviews', ['rating' => 2]) }}" class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium {{ request('rating') == 2 ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    2 Stars
                </a>
                <a href="{{ route('driver.reviews', ['rating' => 1]) }}" class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium {{ request('rating') == 1 ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    1 Star
                </a>
            </div>
            
            @if(count($reviews) === 0)
                <div class="bg-gray-50 rounded-md p-8 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                    <p class="text-gray-500 font-medium">No reviews found</p>
                    <p class="text-sm text-gray-400 mt-1">Complete more rides to get reviews from passengers</p>
                </div>
            @else
                <!-- Reviews List -->
                <div class="space-y-6">
                    @foreach($reviews as $review)
                        <div class="border-b pb-6 last:border-0 last:pb-0">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-gray-300 overflow-hidden">
                                        @if($review->reviewer && $review->reviewer->profile_picture)
                                            <img src="{{ asset('storage/' . $review->reviewer->profile_picture) }}" alt="Reviewer" class="h-full w-full object-cover">
                                        @else
                                            <div class="h-full w-full flex items-center justify-center text-gray-500 bg-gray-300">
                                                {{ substr($review->reviewer->name ?? 'U', 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="ml-4 flex-1">
                                    <div class="flex justify-between">
                                        <div>
                                            <h3 class="font-medium">{{ $review->reviewer->name ?? 'Anonymous Passenger' }}</h3>
                                            <div class="flex items-center mt-1">
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
                                                <p class="text-xs text-gray-500 ml-2">
                                                    {{ $review->created_at->format('M d, Y') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            @if($review->ride)
                                                <div>{{ $review->ride->pickup_location }} → {{ $review->ride->dropoff_location }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    @if($review->comment)
                                        <div class="mt-2 text-gray-700">
                                            {{ $review->comment }}
                                        </div>
                                    @endif
                                    
                                    @if($review->ride)
                                        <div class="mt-3 text-xs text-gray-500 flex flex-wrap gap-3">
                                            <div class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                @if($review->ride->pickup_time && $review->ride->dropoff_time)
                                                    {{ $review->ride->pickup_time->diffInMinutes($review->ride->dropoff_time) }} min ride
                                                @else
                                                    Duration N/A
                                                @endif
                                            </div>
                                            
                                            <div class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                {{ number_format($review->ride->distance_in_km ?? 0, 1) }} km
                                            </div>
                                            
                                            <div class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                MAD {{ number_format($review->ride->price ?? $review->ride->ride_cost ?? 0, 2) }}
                                            </div>
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
            @endif
        </div>
    </main>

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
        });
    </script>
</body>
</html>