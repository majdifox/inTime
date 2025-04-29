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
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex items-center mb-4">
        <div class="h-12 w-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <h2 class="text-xl font-bold">Cash Payment</h2>
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
                    Please pay your driver MAD {{ number_format($ride->price, 2) }} in cash. Your driver will confirm receipt of payment.
                </p>
            </div>
        </div>
    </div>
    
    <div class="space-y-4">
        <div class="flex justify-between items-center border-b border-gray-200 pb-3">
            <span class="font-medium">Total Amount:</span>
            <span class="text-lg font-bold">MAD {{ number_format($ride->price, 2) }}</span>
        </div>
        
        <div class="border-b border-gray-200 pb-3">
            <p class="font-medium mb-2">Payment Instructions:</p>
            <ol class="list-decimal ml-5 space-y-2 text-gray-700">
                <li>Please have the exact amount ready if possible</li>
                <li>Pay the driver directly</li>
                <li>Your driver will confirm receipt of your payment</li>
                <li>You'll be able to rate your ride after payment is confirmed</li>
            </ol>
        </div>
        
        <div>
            <p class="font-medium mb-2">Driver Information:</p>
            <div class="flex items-center">
                <div class="h-12 w-12 rounded-full bg-gray-200 overflow-hidden mr-3">
                    @if($ride->driver->user->profile_picture)
                        <img src="{{ asset('storage/' . $ride->driver->user->profile_picture) }}" alt="Driver" class="h-full w-full object-cover">
                    @else
                        <div class="h-full w-full flex items-center justify-center text-gray-500 bg-gray-300">
                            {{ substr($ride->driver->user->name, 0, 1) }}
                        </div>
                    @endif
                </div>
                <div>
                    <p class="font-medium">{{ $ride->driver->user->name }}</p>
                    <div class="flex items-center text-sm text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        <span>{{ number_format($ride->driver->rating, 1) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-6">
        <p class="text-sm text-gray-600 mb-2">Having trouble with payment? Contact support:</p>
        <a href="tel:+1234567890" class="text-blue-600 hover:text-blue-800 font-medium flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
            </svg>
            Customer Support
        </a>
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
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                </svg>
            </div>
            <div>
                <p class="font-medium">Awaiting Payment Confirmation</p>
                <p class="text-sm text-gray-600">Your driver will confirm payment receipt</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-2 opacity-50">
            <div class="flex-shrink-0 h-8 w-8 bg-gray-300 rounded-full flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div>
                <p class="font-medium">Rate Your Ride</p>
                <p class="text-sm text-gray-600">After payment is confirmed</p>
            </div>
        </div>
    </div>
    
    <div class="mt-6 flex justify-center">
        <a href="{{ route('passenger.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-medium rounded">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Return to Dashboard
        </a>
    </div>
</div>

<!-- Auto-refresh to check payment status -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check payment status every 10 seconds
        const checkPaymentStatus = () => {
            fetch('/passenger/ride/{{ $ride->id }}/payment-status')
                .then(response => response.json())
                .then(data => {
                    if (data.is_paid) {
                        // Redirect to rating page when payment is confirmed
                        window.location.href = "{{ route('passenger.rate.ride', $ride->id) }}";
                    }
                })
                .catch(error => console.error('Error checking payment status:', error));
        };
        
        // Start polling
        const intervalId = setInterval(checkPaymentStatus, 10000);
        
        // Clear interval when page is closed/navigated away from
        window.addEventListener('beforeunload', function() {
            clearInterval(intervalId);
        });
    });
</script>
    </main>
</body>
</html>