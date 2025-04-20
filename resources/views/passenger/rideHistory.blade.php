<!-- passenger/rideHistory.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>inTime - Ride History</title>
    
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
                <a href="{{ route('passenger.profile.private') }}" class="font-medium">My Profile</a>
            </nav>
        </div>
        
        <div class="flex justify-center space-x-4">
            <a href="{{ route('passenger.dashboard') }}" class="bg-black text-white py-2 px-6 rounded-md font-medium hover:bg-gray-800 transition">
                Back to Dashboard
            </a>
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
        <div class="max-w-4xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Ride History</h1>
                <!-- In passenger/rideHistory.blade.php in the ride card -->
                @foreach($rides as $ride)
    <!-- Your ride display code -->
    @if($ride->driver)
        <a href="{{ route('driver.public.profile', $ride->driver->id) }}" class="text-blue-600 hover:text-blue-800 text-sm">
            View Driver Profile
        </a>
    @endif
@endforeach
                
                <!-- Filter Dropdown (optional) -->
                <div class="relative">
                    <button type="button" id="filter-button" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd" />
                        </svg>
                        Filter
                    </button>
                    
                    <div id="filter-dropdown" class="hidden origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10">
                        <div class="py-1">
                            <a href="{{ route('passenger.history') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">All Rides</a>
                            <a href="{{ route('passenger.history', ['filter' => 'completed']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Completed</a>
                            <a href="{{ route('passenger.history', ['filter' => 'cancelled']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Cancelled</a>
                        </div>
                    </div>
                </div>
            </div>
            
            @if(count($rides) === 0)
                <div class="bg-white rounded-lg shadow-md p-6 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h2 class="text-xl font-bold mb-2">No rides found</h2>
                    <p class="text-gray-600">You haven't taken any rides yet. Book your first ride now!</p>
                    <div class="mt-6">
                        <a href="{{ route('passenger.book') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-black hover:bg-gray-800 focus:outline-none">
                            Book a Ride
                        </a>
                    </div>
                </div>
            @else
                <!-- Ride History List -->
                <div class="space-y-4">
                    @foreach($rides as $ride)
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex items-center">
                                    <div class="mr-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            @if($ride->ride_status == 'completed') 
                                                bg-green-100 text-green-800
                                            @elseif($ride->reservation_status == 'cancelled') 
                                                bg-red-100 text-red-800
                                            @else 
                                                bg-gray-100 text-gray-800
                                            @endif
                                        ">
                                            {{ $ride->getStatusText() }}
                                        </span>
                                    </div>
                                    <div>
                                        <h3 class="font-bold">{{ $ride->dropoff_time ? $ride->dropoff_time->format('M d, Y') : $ride->reservation_date->format('M d, Y') }}</h3>
                                        <p class="text-sm text-gray-500">{{ $ride->dropoff_time ? $ride->dropoff_time->format('g:i A') : $ride->reservation_date->format('g:i A') }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold">MAD {{ number_format($ride->price, 2) }}</p>
                                    @if($ride->surge_multiplier > 1)
                                        <p class="text-xs text-red-600">Surge x{{ $ride->surge_multiplier }}</p>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-4 mb-4">
                                <div class="h-12 w-12 bg-gray-200 rounded-full flex items-center justify-center">
                                    @switch($ride->vehicle_type)
                                        @case('share')
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            @break
                                        @case('comfort')
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            @break
                                        @case('women')
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-pink-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                            @break
                                        @case('wav')
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                            </svg>
                                            @break
                                        @case('black')
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                            </svg>
                                            @break
                                        @default
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m-4 4H4m0 0l4 4m-4-4l4-4" />
                                            </svg>
                                    @endswitch
                                </div>
                                <div>
                                    <p class="font-medium">{{ ucfirst($ride->vehicle_type) }}</p>
                                    <p class="text-sm text-gray-600">{{ number_format($ride->distance_in_km, 1) }} km</p>
                                </div>
                                
                                @if($ride->driver)
                                    <div class="ml-6 flex items-center">
                                        <div class="h-10 w-10 rounded-full bg-gray-300 overflow-hidden mr-3">
                                            @if($ride->driver->user->profile_picture)
                                                <img src="{{ asset('storage/' . $ride->driver->user->profile_picture) }}" alt="Driver" class="h-full w-full object-cover">
                                            @else
                                                <div class="h-full w-full flex items-center justify-center bg-gray-400 text-white">
                                                    {{ strtoupper(substr($ride->driver->user->name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-medium">{{ $ride->driver->user->name }}</p>
                                            @if($ride->driver->rating)
                                                <div class="flex items-center text-sm">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-500 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                    <span>{{ number_format($ride->driver->rating, 1) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="border-t pt-4">
                                <div class="space-y-3">
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
                                            <p class="text-sm font-medium">Destination</p>
                                            <p class="text-sm text-gray-600">{{ $ride->dropoff_location }}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-4 flex justify-between items-center">
                                    @if($ride->ride_status == 'completed' && !$ride->is_reviewed)
                                        <button type="button" class="text-blue-600 text-sm font-medium hover:text-blue-800 review-ride-btn" data-ride-id="{{ $ride->id }}">
                                            Rate this ride
                                        </button>
                                    @elseif($ride->ride_status == 'completed' && $ride->is_reviewed)
                                        <span class="text-green-600 text-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                            Reviewed
                                        </span>
                                    @else
                                        <span></span>
                                    @endif
                                    
                                    <a href="#" class="text-gray-500 text-sm hover:text-gray-700">Get Receipt</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="mt-6">
                    {{ $rides->links() }}
                </div>
            @endif
        </div>
    </main>
    
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
                        <div class="mt-1">
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

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Filter dropdown toggle
            const filterButton = document.getElementById('filter-button');
            const filterDropdown = document.getElementById('filter-dropdown');
            
            if (filterButton && filterDropdown) {
                filterButton.addEventListener('click', function() {
                    filterDropdown.classList.toggle('hidden');
                });
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function(event) {
                    if (!filterButton.contains(event.target) && !filterDropdown.contains(event.target)) {
                        filterDropdown.classList.add('hidden');
                    }
                });
            }
            
            // Rate ride modal
            const rateRideModal = document.getElementById('rate-ride-modal');
            const rateRideForm = document.getElementById('rate-ride-form');
            const rateRideButtons = document.querySelectorAll('.review-ride-btn');
            const cancelRatingBtn = document.getElementById('cancel-rating');
            const ratingStars = document.querySelectorAll('.rating-star');
            const ratingValue = document.getElementById('rating-value');
            
            // Show modal when clicking rate buttons
            rateRideButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const rideId = this.getAttribute('data-ride-id');
                    rateRideForm.action = `{{ url('passenger/ride') }}/${rideId}/rate`;
                    rateRideModal.classList.remove('hidden');
                });
            });
            
            // Star rating functionality
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
            
            // Close modal when clicking cancel
            if (cancelRatingBtn) {
                cancelRatingBtn.addEventListener('click', function() {
                    rateRideModal.classList.add('hidden');
                });
            }
            
            // Close modal when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target === rateRideModal) {
                    rateRideModal.classList.add('hidden');
                }
            });
        });
    </script>
</body>
</html>