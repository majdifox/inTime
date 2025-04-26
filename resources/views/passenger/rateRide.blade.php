<!-- passenger/rateRide.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>inTime - Rate Your Driver</title>
    
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
                <a href="{{ route('passenger.history') }}" class="font-medium">Ride History</a>
                <a href="{{ route('passenger.profile.private') }}" class="font-medium">My Profile</a>
            </nav>
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
    <main class="container mx-auto px-48 py-8">
        
<!-- Payment Confirmation Card -->
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-lg font-bold mb-4">Payment Confirmation</h2>
    <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 rounded-md">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-green-700">
                    Your payment of <strong>MAD {{ number_format($ride->price, 2) }}</strong> has been successfully processed.
                </p>
            </div>
        </div>
    </div>
    
    <!-- Vehicle Details -->
    <div class="mt-4">
        <h3 class="text-md font-medium mb-2">Vehicle Information</h3>
        <div class="flex items-center space-x-4">
            <div class="h-12 w-12 bg-gray-200 rounded-md overflow-hidden">
                @if($ride->driver->vehicle && $ride->driver->vehicle->vehicle_photo)
                    <img src="{{ asset('storage/' . $ride->driver->vehicle->vehicle_photo) }}" alt="Vehicle" class="h-full w-full object-cover">
                @else
                    <div class="h-full w-full flex items-center justify-center text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m-8 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                    </div>
                @endif
            </div>
            <div>
                <p class="font-medium">{{ $ride->driver->vehicle->make }} {{ $ride->driver->vehicle->model }}</p>
                <p class="text-sm text-gray-600">{{ $ride->driver->vehicle->color }} · {{ $ride->driver->vehicle->plate_number }}</p>
                <div class="mt-1">
                    <span class="px-2 py-0.5 rounded text-xs font-medium 
                        @if($ride->driver->vehicle->type === 'women')
                            bg-pink-100 text-pink-800
                        @elseif($ride->driver->vehicle->type === 'share')
                            bg-blue-100 text-blue-800
                        @elseif($ride->driver->vehicle->type === 'comfort')
                            bg-green-100 text-green-800
                        @elseif($ride->driver->vehicle->type === 'wav')
                            bg-purple-100 text-purple-800
                        @elseif($ride->driver->vehicle->type === 'black')
                            bg-gray-800 text-white
                        @endif
                    ">
                        {{ ucfirst($ride->driver->vehicle->type) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
<br>
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex items-center mb-4">
        <div class="h-12 w-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
            </svg>
        </div>
        <h2 class="text-xl font-bold">Rate Your Driver</h2>
    </div>
    
    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    Your rating helps improve the inTime community. Please take a moment to rate your experience.
                </p>
            </div>
        </div>
    </div>
    
    <div class="mb-6">
        <div class="flex items-center space-x-3 mb-4">
            <!-- Driver Photo -->
            <div class="h-16 w-16 rounded-full bg-gray-200 overflow-hidden">
                @if($ride->driver->user->profile_picture)
                    <img src="{{ asset('storage/' . $ride->driver->user->profile_picture) }}" alt="Driver" class="h-full w-full object-cover">
                @else
                    <div class="h-full w-full flex items-center justify-center bg-gray-400 text-white">
                        {{ strtoupper(substr($ride->driver->user->name, 0, 1)) }}
                    </div>
                @endif
            </div>
            <div>
                <h3 class="font-medium text-lg">{{ $ride->driver->user->name }}</h3>
                <div class="flex items-center">
                    <span class="text-gray-600 mr-2">{{ $ride->driver->user->gender ? ucfirst($ride->driver->user->gender) : '' }}</span>
                    @if($ride->driver->women_only_driver)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-pink-100 text-pink-800">
                            Women Only
                        </span>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Ride Details -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-gray-50 rounded-md mb-4">
            <div>
                <div class="flex items-start mb-3">
                    <div class="flex-shrink-0 mt-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <circle cx="12" cy="12" r="8" stroke-width="2" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">Pickup Location</p>
                        <p class="text-sm text-gray-600">{{ $ride->pickup_location }}</p>
                    </div>
                </div>
                
                <div class="flex items-start mb-3">
                    <div class="flex-shrink-0 mt-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">Destination</p>
                        <p class="text-sm text-gray-600">{{ $ride->dropoff_location }}</p>
                    </div>
                </div>
            </div>
            
            <div>
                <div class="flex items-start mb-3">
                    <div class="flex-shrink-0 mt-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">Pickup Time</p>
                        <p class="text-sm text-gray-600">{{ $ride->pickup_time->format('M d, Y g:i A') }}</p>
                    </div>
                </div>
                
                <div class="flex items-start mb-3">
                    <div class="flex-shrink-0 mt-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">Fare</p>
                        <p class="text-sm text-gray-600">MAD {{ number_format($ride->price, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Rating Form -->
    <form method="POST" action="{{ route('passenger.submit.rating', $ride->id) }}" class="space-y-6">
    @csrf
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">
                How would you rate your ride with {{ $ride->driver->user->name }}?
            </label>
            
            <div class="flex justify-center">
                <div class="flex space-x-2">
                    <input type="radio" name="rating" id="star1" value="1" class="hidden peer/star1" required>
                    <input type="radio" name="rating" id="star2" value="2" class="hidden peer/star2">
                    <input type="radio" name="rating" id="star3" value="3" class="hidden peer/star3">
                    <input type="radio" name="rating" id="star4" value="4" class="hidden peer/star4">
                    <input type="radio" name="rating" id="star5" value="5" class="hidden peer/star5" checked>
                    
                    <label for="star1" class="cursor-pointer text-3xl text-gray-300 peer-checked/star1:text-yellow-400 hover:text-yellow-400">★</label>
                    <label for="star2" class="cursor-pointer text-3xl text-gray-300 peer-checked/star2:text-yellow-400 hover:text-yellow-400 peer-checked/star1:hover:text-yellow-400">★</label>
                    <label for="star3" class="cursor-pointer text-3xl text-gray-300 peer-checked/star3:text-yellow-400 hover:text-yellow-400 peer-checked/star2:hover:text-yellow-400 peer-checked/star1:hover:text-yellow-400">★</label>
                    <label for="star4" class="cursor-pointer text-3xl text-gray-300 peer-checked/star4:text-yellow-400 hover:text-yellow-400 peer-checked/star3:hover:text-yellow-400 peer-checked/star2:hover:text-yellow-400 peer-checked/star1:hover:text-yellow-400">★</label>
                    <label for="star5" class="cursor-pointer text-3xl text-gray-300 peer-checked/star5:text-yellow-400 hover:text-yellow-400 peer-checked/star4:hover:text-yellow-400 peer-checked/star3:hover:text-yellow-400 peer-checked/star2:hover:text-yellow-400 peer-checked/star1:hover:text-yellow-400">★</label>
                </div>
            </div>
        </div>
        
        <div>
            <label for="comment" class="block text-sm font-medium text-gray-700 mb-1">
                Additional comments (optional)
            </label>
            <textarea id="comment" name="comment" rows="4" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="Share your experience..."></textarea>
        </div>
        
        <div class="flex justify-center pt-4">
            <button type="submit" class="bg-blue-600 text-white py-2 px-8 rounded-md font-medium hover:bg-blue-700 transition">
                Submit Rating
            </button>
        </div>
    </form>
</div>


<!-- JavaScript for star rating enhancement -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize rating stars
        const stars = document.querySelectorAll('input[name="rating"]');
        const starLabels = document.querySelectorAll('label[for^="star"]');
        
        // Add hover effect
        starLabels.forEach((label, index) => {
            label.addEventListener('mouseover', () => {
                for (let i = 0; i <= index; i++) {
                    starLabels[i].classList.add('text-yellow-400');
                    starLabels[i].classList.remove('text-gray-300');
                }
                for (let i = index + 1; i < starLabels.length; i++) {
                    starLabels[i].classList.add('text-gray-300');
                    starLabels[i].classList.remove('text-yellow-400');
                }
            });
            
            label.addEventListener('mouseout', () => {
                starLabels.forEach((starLabel, i) => {
                    const star = stars[i];
                    if (star.checked) {
                        for (let j = 0; j <= i; j++) {
                            starLabels[j].classList.add('text-yellow-400');
                            starLabels[j].classList.remove('text-gray-300');
                        }
                        for (let j = i + 1; j < starLabels.length; j++) {
                            starLabels[j].classList.add('text-gray-300');
                            starLabels[j].classList.remove('text-yellow-400');
                        }
                    }
                });
            });
        });
        
        // Set initial state
        document.getElementById('star5').checked = true;
        starLabels.forEach(label => {
            label.classList.add('text-yellow-400');
            label.classList.remove('text-gray-300');
        });
        
        // Handle click events
        stars.forEach((star, index) => {
            star.addEventListener('change', () => {
                for (let i = 0; i <= index; i++) {
                    starLabels[i].classList.add('text-yellow-400');
                    starLabels[i].classList.remove('text-gray-300');
                }
                for (let i = index + 1; i < starLabels.length; i++) {
                    starLabels[i].classList.add('text-gray-300');
                    starLabels[i].classList.remove('text-yellow-400');
                }
            });
        });
    });
</script>

</body>
</html>