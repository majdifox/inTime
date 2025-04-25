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
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Payment Header -->
            <div class="bg-green-600 px-6 py-4 text-white">
                <h1 class="text-xl font-bold">Cash Payment Confirmation</h1>
                <p class="text-sm opacity-90">Confirm you've received payment from passenger</p>
            </div>
            
            <!-- Passenger Details -->
            <div class="border-b border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <div class="h-12 w-12 bg-gray-200 rounded-full overflow-hidden mr-4">
                            @if($ride->passenger->user->profile_picture)
                                <img src="{{ asset('storage/' . $ride->passenger->user->profile_picture) }}" alt="Passenger" class="h-full w-full object-cover">
                            @else
                                <div class="h-full w-full flex items-center justify-center bg-gray-400 text-white">
                                    {{ strtoupper(substr($ride->passenger->user->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <h3 class="font-medium">{{ $ride->passenger->user->name }}</h3>
                            <div class="flex items-center text-sm text-gray-500">
                                <span>{{ $ride->passenger->rating ?? '5.0' }} </span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-500 ml-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-3 p-4 bg-yellow-50 border border-yellow-100 rounded-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Payment Pending</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>The passenger has chosen to pay with cash. Please confirm once you have received MAD {{ number_format($ride->price, 2) }} from the passenger.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Ride Details -->
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-medium mb-4">Ride Summary</h2>
                
                <div class="space-y-3">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mt-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-gray-500">{{ $ride->dropoff_time->format('l, F j, Y') }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mt-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-gray-500">{{ $ride->pickup_time->format('g:i A') }} - {{ $ride->dropoff_time->format('g:i A') }}</p>
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
                    
                    <div class="border-t border-gray-200 pt-3 mt-3 flex justify-between font-medium">
                        <span>Total Amount</span>
                        <span>MAD {{ number_format($ride->price, 2) }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="p-6">
                <form action="{{ route('driver.confirm.cash.payment', $ride->id) }}" method="POST">
                    @csrf
                    <div class="flex flex-col space-y-3">
                        <button type="submit" class="w-full py-3 px-4 bg-green-600 hover:bg-green-700 text-white font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                            Confirm Payment Received
                        </button>
                        
                        <a href="{{ route('driver.payment.issue', $ride->id) }}" class="text-center w-full py-3 px-4 border border-red-300 text-red-700 font-medium rounded-md shadow-sm hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                            Report Payment Issue
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>