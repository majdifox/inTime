<!-- resources/views/passenger/profile/private.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>inTime - My Profile</title>
    
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
                <a href="{{ route('passenger.book') }}" class="font-medium">Book Ride</a>
                <a href="{{ route('passenger.history') }}" class="font-medium">Ride History</a>
            </nav>
        </div>
        
       
        
        <div class="h-10 w-10 rounded-full bg-gray-300 overflow-hidden">
            @if(Auth::user()->profile_picture)
                <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Profile" class="h-full w-full object-cover">
            @else
                <div class="h-full w-full flex items-center justify-center bg-gray-400 text-white">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
            @endif
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <div class="max-w-5xl mx-auto">
            <!-- User Info Section -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                <div class="relative bg-gray-800 text-white p-6">
                    <div class="flex items-center">
                        <div class="h-24 w-24 rounded-full bg-gray-300 overflow-hidden mr-6">
                            @if(Auth::user()->profile_picture)
                                <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Profile" class="h-full w-full object-cover">
                            @else
                                <div class="h-full w-full flex items-center justify-center bg-gray-400 text-white text-2xl">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold">{{ Auth::user()->name }}</h1>
                            <p class="text-gray-300">{{ Auth::user()->email }}</p>
                            <p class="text-gray-300">{{ Auth::user()->phone }}</p>
                            
                            <div class="flex items-center mt-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>Joined {{ Auth::user()->created_at->format('F Y') }}</span>
                            </div>
                        </div>
                        
            
                    </div>
                </div>
                
                <!-- Stats Cards -->
                <div class="p-6">
                    <h2 class="text-xl font-bold mb-4">Ride Statistics</h2>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-center p-4 bg-gray-50 rounded-lg shadow-sm">
                            <p class="text-gray-500 text-sm">Total Rides</p>
                            <p class="text-2xl font-bold">{{ $rideStats['total'] }}</p>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg shadow-sm">
                            <p class="text-gray-500 text-sm">Completed</p>
                            <p class="text-2xl font-bold">{{ $rideStats['completed'] }}</p>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg shadow-sm">
                            <p class="text-gray-500 text-sm">Cancelled</p>
                            <p class="text-2xl font-bold">{{ $rideStats['cancelled'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

             
    
</body>
</html>