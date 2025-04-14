<!-- driver/settings.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>inTime - Driver Settings</title>
    
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
                <a href="{{ route('driver.settings') }}" class="font-medium text-blue-600 transition">Settings</a>
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
                    <a href="{{ route('driver.reviews') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My Reviews</a>
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
                
                <!-- Women-Only Driver Mode (for female drivers only) -->
                @if(Auth::user()->gender === 'female')
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex flex-col space-y-4">
                        <div>
                            <h2 class="text-xl font-bold">Women-Only Driver Mode</h2>
                            <p class="text-gray-600">When enabled, you'll only be matched with female passengers</p>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <button id="toggle-women-only" class="relative inline-flex h-6 w-11 items-center rounded-full {{ $driver->women_only_driver ? 'bg-pink-500' : 'bg-gray-300' }} transition-colors duration-300">
                                    <span id="women-only-circle" class="inline-block h-5 w-5 transform rounded-full bg-white shadow-md transition-transform duration-300 {{ $driver->women_only_driver ? 'translate-x-5' : 'translate-x-1' }}"></span>
                                </button>
                                <span id="women-only-text" class="ml-2 font-medium">{{ $driver->women_only_driver ? 'Women-Only Mode On' : 'Women-Only Mode Off' }}</span>
                            </div>
                            
                            <div class="px-2 py-1 rounded bg-pink-100 text-pink-800 text-xs">
                                <span>For female drivers only</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 bg-gray-50 p-4 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-gray-800">How women-only mode works</h3>
                                <div class="mt-2 text-sm text-gray-600">
                                    <ul class="list-disc pl-5 space-y-1">
                                        <li>You'll only be visible to female passengers</li>
                                        <li>A pink icon will appear next to your profile</li>
                                        <li>This mode is recommended if you have a "Women" vehicle type</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($driver->vehicle && $driver->vehicle->type !== 'women' && $driver->women_only_driver)
                    <div class="mt-4 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    For consistent matching, consider updating your vehicle type to "Women" in vehicle settings.
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                @endif
                
                <!-- Vehicle Settings -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold">Vehicle Information</h2>
                        <a href="{{ route('profile.edit') }}#vehicle-section" class="text-blue-600 text-sm font-medium hover:text-blue-800">
                            Edit Vehicle
                        </a>
                    </div>
                    
                    @if($driver->vehicle)
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="h-20 w-20 bg-gray-200 rounded-md overflow-hidden">
                            @if($driver->vehicle->vehicle_photo)
                                <img src="{{ asset('storage/' . $driver->vehicle->vehicle_photo) }}" alt="Vehicle" class="h-full w-full object-cover">
                            @else
                                <div class="h-full w-full flex items-center justify-center text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m-8 6H4m0 0l4 4m-4-4l4-4" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div>
                            <h3 class="font-medium text-lg">{{ $driver->vehicle->make }} {{ $driver->vehicle->model }}</h3>
                            <p class="text-gray-600">{{ $driver->vehicle->year }} · {{ $driver->vehicle->color }}</p>
                            <p class="text-sm text-gray-500">{{ $driver->vehicle->plate_number }}</p>
                            <div class="mt-1">
                                <span class="px-2 py-1 rounded text-xs font-medium
                                    @if($driver->vehicle->type === 'women')
                                        bg-pink-100 text-pink-800
                                    @elseif($driver->vehicle->type === 'share')
                                        bg-blue-100 text-blue-800
                                    @elseif($driver->vehicle->type === 'comfort')
                                        bg-green-100 text-green-800
                                    @elseif($driver->vehicle->type === 'wav')
                                        bg-purple-100 text-purple-800
                                    @elseif($driver->vehicle->type === 'black')
                                        bg-gray-800 text-white
                                    @endif">
                                    {{ ucfirst($driver->vehicle->type) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="border-t border-gray-200 pt-4 mt-4">
                        <h3 class="font-medium text-gray-700 mb-2">Vehicle Features</h3>
                        <div class="flex flex-wrap gap-2">
                            @forelse($driver->vehicle->features as $feature)
                                <span class="px-2 py-1 rounded-full bg-gray-100 text-gray-700 text-xs">
                                    {{ ucfirst(str_replace('_', ' ', $feature->feature)) }}
                                </span>
                            @empty
                                <span class="text-gray-500 text-sm">No features specified</span>
                            @endforelse
                        </div>
                    </div>
                    
                    @else
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    No vehicle information found. Please add your vehicle details to start accepting ride requests.
                                </p>
                                <div class="mt-2">
                                    <a href="{{ route('profile.edit') }}#vehicle-section" class="text-yellow-700 font-medium text-sm underline">
                                        Add Vehicle Information
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                
                <div class="mt-6">
                    <nav class="grid gap-y-4">
                        <a href="{{ route('driver.dashboard') }}" class="font-medium px-3 py-2 rounded-md hover:bg-gray-100">Dashboard</a>
                        <a href="{{ route('driver.awaiting.rides') }}" class="font-medium px-3 py-2 rounded-md hover:bg-gray-100">Awaiting Rides</a>
                        <a href="{{ route('driver.active.rides') }}" class="font-medium px-3 py-2 rounded-md hover:bg-gray-100">Active Rides</a>
                        <a href="{{ route('driver.history') }}" class="font-medium px-3 py-2 rounded-md hover:bg-gray-100">History</a>
                        <a href="{{ route('driver.earnings') }}" class="font-medium px-3 py-2 rounded-md hover:bg-gray-100">Earnings</a>
                        <a href="{{ route('driver.settings') }}" class="font-medium px-3 py-2 rounded-md bg-blue-50 text-blue-600">Settings</a>
                        <a href="{{ route('profile.edit') }}" class="font-medium px-3 py-2 rounded-md hover:bg-gray-100">Profile Settings</a>
                        <a href="{{ route('driver.reviews') }}" class="font-medium px-3 py-2 rounded-md hover:bg-gray-100">My Reviews</a>
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
        
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Left Column - Driver Mode Settings -->
            <div class="w-full lg:w-1/2 flex flex-col gap-6">
                <!-- Online/Offline Toggle and Location Sharing -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex flex-col space-y-4">
                        <div>
                            <h2 class="text-xl font-bold">Driver Status</h2>
                            <p class="text-gray-600">Toggle your availability to receive ride requests</p>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <!-- Online/Offline Toggle Button -->
                            <div class="flex items-center">
                                <button id="toggle-status" class="relative inline-flex h-6 w-11 items-center rounded-full {{ Auth::user()->is_online ? 'bg-green-500' : 'bg-gray-300' }} transition-colors duration-300">
                                    <span id="toggle-circle" class="inline-block h-5 w-5 transform rounded-full bg-white shadow-md transition-transform duration-300 {{ Auth::user()->is_online ? 'translate-x-5' : 'translate-x-1' }}"></span>
                                </button>
                                <span id="toggle-text" class="ml-2 font-medium">{{ Auth::user()->is_online ? 'Go Offline' : 'Go Online' }}</span>
                            </div>
                            
                            <!-- Location Sharing Button -->
                            <div id="location-sharing-container" class="{{ Auth::user()->is_online ? '' : 'hidden' }}">
                                <button id="share-location" class="bg-blue-600 text-white py-2 px-4 rounded-md font-medium hover:bg-blue-700 transition {{ Auth::user()->is_online ? '' : 'opacity-50 cursor-not-allowed' }}" {{ Auth::user()->is_online ? '' : 'disabled' }}>
                                    <span id="location-status">Share Location</span>
                                </button>
                            </div>