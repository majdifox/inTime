<!-- passenger/cashPayment.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>inTime - Cash Payment</title>
    
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
    <main class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Payment Header -->
            <div class="bg-green-600 px-6 py-4 text-white">
                <h1 class="text-xl font-bold">Cash Payment</h1>
                <p class="text-sm opacity-90">Please pay your driver directly</p>
            </div>
            
            <!-- Cash Payment Instructions -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-center mb-6">
                    <div class="h-20 w-20 bg-green-100 rounded-full flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
                
                <h2 class="text-lg font-medium text-center mb-4">Please Pay Your Driver</h2>
                
                <div class="bg-yellow-50 border border-yellow-100 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Important</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>
                                    You've selected to pay with cash. Please pay your driver <strong>MAD {{ number_format($ride->price, 2) }}</strong> directly. The driver will confirm once they have received your payment.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-blue-600 font-medium">1</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium">Pay the exact amount to your driver</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-blue-600 font-medium">2</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium">Your driver will confirm receipt of payment</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-blue-600 font-medium">3</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium">You'll be able to rate your ride once payment is confirmed</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Driver Details -->
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-medium mb-4">Your Driver</h2>
                
                <div class="flex items-center">
                    <div class="h-16 w-16 bg-gray-200 rounded-full overflow-hidden mr-4">
                        @if($ride->driver->user->profile_picture)
                            <img src="{{ asset('storage/' . $ride->driver->user->profile_picture) }}" alt="Driver" class="h-full w-full object-cover">
                        @else
                            <div class="h-full w-full flex items-center justify-center bg-gray-400 text-white">
                                {{ strtoupper(substr($ride->driver->user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <h3 class="font-medium">{{ $ride->driver->user->name }}</h3>
                        <div class="flex items-center text-sm text-gray-500">
                            <span>{{ $ride->driver->rating }} </span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-500 ml-1" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        </div>
                        <div class="flex items-center mt-1">
                            <span class="text-sm">
                                {{ $ride->driver->vehicle->make }} {{ $ride->driver->vehicle->model }}, {{ $ride->driver->vehicle->color }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Ride Summary -->
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-medium mb-4">Ride Summary</h2>
                
                <div class="text-center text-3xl font-bold mb-4">
                    MAD {{ number_format($ride->price, 2) }}
                </div>
                
                <div class="space-y-3">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mt-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <circle cx="12" cy="12" r="8" stroke-width="2" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium">From</p>
                            <p class="text-sm text-gray-500">{{ $ride->pickup_location }}</p>
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
                            <p class="text-sm font-medium">To</p>
                            <p class="text-sm text-gray-500">{{ $ride->dropoff_location }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mt-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-gray-500">{{ number_format($ride->distance_in_km, 1) }} km</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="p-6">
                <div class="flex flex-col space-y-3">
                    <a href="{{ route('passenger.dashboard') }}" class="text-center w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                        Return to Dashboard
                    </a>
                    
                    <div class="text-center text-sm text-gray-500">
                        <p>You will be redirected to the rating page once the driver confirms your payment.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>