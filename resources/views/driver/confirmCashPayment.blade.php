<!-- driver/confirmCashPayment.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>inTime - Confirm Cash Payment</title>
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <!-- Header/Navigation -->
    <header class="px-4 py-4 flex items-center justify-between bg-white shadow-sm">
        <div class="flex items-center space-x-8">
            <!-- Logo -->
            <a href="{{ route('driver.dashboard') }}" class="text-2xl font-bold">inTime</a>
            
            <!-- Navigation Links -->
            <nav class="hidden md:flex space-x-6">
                <a href="{{ route('driver.active.rides') }}" class="font-medium">Active Rides</a>
                <a href="{{ route('driver.history') }}" class="font-medium">History</a>
                <a href="{{ route('driver.earnings') }}" class="font-medium">Earnings</a>
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
    <main class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex items-center mb-4">
        <div class="h-12 w-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <h2 class="text-xl font-bold">Confirm Cash Payment</h2>
    </div>
    
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700">
                    Please confirm that you have received <strong>MAD {{ number_format($ride->price, 2) }}</strong> in cash from your passenger.
                </p>
            </div>
        </div>
    </div>
    
    <div class="space-y-4">
        <div class="flex justify-between items-center border-b border-gray-200 pb-3">
            <span class="font-medium">Amount to Collect:</span>
            <span class="text-lg font-bold">MAD {{ number_format($ride->price, 2) }}</span>
        </div>
        
        <div class="border-b border-gray-200 pb-3">
            <p class="font-medium mb-2">Passenger Information:</p>
            <div class="flex items-center">
                <div class="h-12 w-12 rounded-full bg-gray-200 overflow-hidden mr-3">
                    @if($ride->passenger->user->profile_picture)
                        <img src="{{ asset('storage/' . $ride->passenger->user->profile_picture) }}" alt="Passenger" class="h-full w-full object-cover">
                    @else
                        <div class="h-full w-full flex items-center justify-center text-gray-500 bg-gray-300">
                            {{ substr($ride->passenger->user->name, 0, 1) }}
                        </div>
                    @endif
                </div>
                <div>
                    <p class="font-medium">{{ $ride->passenger->user->name }}</p>
                    <div class="flex items-center text-sm text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        <span>{{ number_format($ride->passenger->rating, 1) }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="border-b border-gray-200 pb-3">
            <p class="font-medium mb-2">Ride Details:</p>
            <div class="grid grid-cols-2 gap-2 text-sm">
                <div>
                    <span class="text-gray-600">Pickup:</span>
                    <p>{{ $ride->pickup_location }}</p>
                </div>
                <div>
                    <span class="text-gray-600">Dropoff:</span>
                    <p>{{ $ride->dropoff_location }}</p>
                </div>
                <div>
                    <span class="text-gray-600">Date:</span>
                    <p>{{ $ride->dropoff_time->format('M d, Y') }}</p>
                </div>
                <div>
                    <span class="text-gray-600">Time:</span>
                    <p>{{ $ride->dropoff_time->format('g:i A') }}</p>
                </div>
                <div>
                    <span class="text-gray-600">Distance:</span>
                    <p>{{ number_format($ride->distance_in_km, 2) }} km</p>
                </div>
                <div>
                    <span class="text-gray-600">Duration:</span>
                    <p>{{ $ride->pickup_time->diffInMinutes($ride->dropoff_time) }} min</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="flex space-x-4 mt-6">
        <form method="POST" action="{{ route('driver.confirm.cash.payment.post', $ride->id) }}" class="flex-1">
            @csrf
            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded transition flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                Confirm Payment Received
            </button>
        </form>
        
        <form method="POST" action="{{ route('driver.submit.payment.issue', $ride->id) }}" class="flex-1">
            @csrf
            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded transition flex items-center justify-center" 
                    onclick="return confirm('Are you sure you want to report a payment issue? This will notify our team.')">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                Report Payment Issue
            </button>
        </form>
    </div>
</div>

<!-- Payment Status Card -->
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-lg font-bold mb-4">Payment Status</h2>
    <div class="space-y-4">
        <div class="flex items-center space-x-2">
            <div class="flex-shrink-0 h-8 w-8 bg-blue-500 rounded-full flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
            </div>
            <div>
                <p class="font-medium">Ride Completed</p>
                <p class="text-sm text-gray-600">{{ $ride->dropoff_time->format('M d, Y g:i A') }}</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-2">
            <div class="flex-shrink-0 h-8 w-8 bg-yellow-500 rounded-full flex items-center justify-center animate-pulse">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                </svg>
            </div>
            <div>
                <p class="font-medium">Awaiting Cash Payment</p>
                <p class="text-sm text-gray-600">MAD {{ number_format($ride->price, 2) }}</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-2 opacity-50">
            <div class="flex-shrink-0 h-8 w-8 bg-gray-300 rounded-full flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
            </div>
            <div>
                <p class="font-medium">Rate Your Passenger</p>
                <p class="text-sm text-gray-600">After confirming payment</p>
            </div>
        </div>
    </div>
    
    <div class="mt-6 flex justify-center">
        <a href="{{ route('driver.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-medium rounded">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Return to Dashboard
        </a>
    </div>
</div>
    </main>
</body>
</html>