<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>inTime - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css">
     <!-- Include Tailwind CSS -->
     @vite('resources/css/app.css')
</head>
<body class="bg-gray-50">
    <!-- Header/Navigation -->
    <header class="px-4 py-4 flex items-center justify-between bg-white shadow-sm">
        <div class="flex items-center space-x-8">
            <!-- Logo -->
            <a href="{{ route('admin.dashboard') }}" class="text-2xl font-bold">inTime</a>
            
            <!-- Navigation Links -->
            <nav class="hidden md:flex space-x-6">
                <a href="{{ route('admin.dashboard') }}" class="font-medium {{ request()->routeIs('admin.dashboard') ? 'text-black' : 'text-gray-500 hover:text-black' }}">Dashboard</a>
                <a href="{{ route('admin.users') }}" class="font-medium {{ request()->routeIs('admin.users') ? 'text-black' : 'text-gray-500 hover:text-black' }}">Users Management</a>
                <a href="#" class="font-medium text-gray-500 hover:text-black">Rides Management</a>
                <a href="#" class="font-medium text-gray-500 hover:text-black">Reports</a>
                <a href="#" class="font-medium text-gray-500 hover:text-black">Settings</a>
            </nav>
        </div>
        
        <!-- User Profile -->
        <div class="flex items-center space-x-4">
            <div class="h-10 w-10 rounded-full bg-gray-300 overflow-hidden">
                @if(Auth::user()->profile_picture)
                    <img src="{{ Storage::url(Auth::user()->profile_picture) }}" alt="Profile" class="h-full w-full object-cover">
                @else
                    <img src="/api/placeholder/40/40" alt="Profile" class="h-full w-full object-cover">
                @endif
            </div>
            <div class="hidden md:block">
                <p class="text-sm font-medium">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">
        <div class="max-w-8xl mx-auto">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold">Dashboard</h1>
            </div>

            <!-- Statistics Section -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                <div class="p-6">
                    <h2 class="text-xl font-bold mb-4">Ride Statistics</h2>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <!-- Total Completed Rides -->
                        <div class="bg-green-50 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-500">Total Completed</p>
                                    <p class="text-2xl font-bold text-green-600">{{ $stats['total_completed'] }}</p>
                                </div>
                                <div class="bg-green-100 p-3 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                            <p class="text-sm {{ $percentChanges['total_completed_change'] >= 0 ? 'text-green-600' : 'text-red-600' }} mt-2">
                                {{ $percentChanges['total_completed_change'] >= 0 ? '+' : '' }}{{ $percentChanges['total_completed_change'] }}% from last month
                            </p>
                        </div>
                        
                        <!-- Cancelled Rides -->
                        <div class="bg-red-50 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-500">Cancelled Rides</p>
                                    <p class="text-2xl font-bold text-red-600">{{ $stats['cancelled_rides'] }}</p>
                                </div>
                                <div class="bg-red-100 p-3 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </div>
                            </div>
                            <p class="text-sm {{ $percentChanges['cancelled_rides_change'] >= 0 ? 'text-green-600' : 'text-red-600' }} mt-2">
                                {{ $percentChanges['cancelled_rides_change'] >= 0 ? '+' : '' }}{{ $percentChanges['cancelled_rides_change'] }}% from last month
                            </p>
                        </div>
                        
                        <!-- General Income -->
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-500">General Income</p>
                                    <p class="text-2xl font-bold text-blue-600">${{ number_format($stats['general_income'], 2) }}</p>
                                </div>
                                <div class="bg-blue-100 p-3 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                            <p class="text-sm {{ $percentChanges['general_income_change'] >= 0 ? 'text-blue-600' : 'text-red-600' }} mt-2">
                                {{ $percentChanges['general_income_change'] >= 0 ? '+' : '' }}{{ $percentChanges['general_income_change'] }}% from last month
                            </p>
                        </div>
                        
                        <!-- Average Rating -->
                        <div class="bg-yellow-50 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-500">Average Rating</p>
                                    <p class="text-2xl font-bold text-yellow-600">{{ number_format($stats['average_rating'], 1) }}</p>
                                </div>
                                <div class="bg-yellow-100 p-3 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                    </svg>
                                </div>
                            </div>
                            <p class="text-sm {{ $percentChanges['average_rating_change'] >= 0 ? 'text-yellow-600' : 'text-red-600' }} mt-2">
                                {{ $percentChanges['average_rating_change'] >= 0 ? '+' : '' }}{{ $percentChanges['average_rating_change'] }} from last month
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Supervision Section -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                <div class="p-6">
                    <h2 class="text-xl font-bold mb-4">Supervision</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Online Drivers -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-500">Online Drivers</p>
                                    <div class="flex items-center">
                                        <p class="text-2xl font-bold">{{ $driverStats['online_drivers'] }}</p>
                                        <span class="ml-2 px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Active</span>
                                    </div>
                                </div>
                                <div class="bg-gray-100 p-3 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex justify-between mt-3">
                                <div>
                                    <p class="text-xs text-gray-500">Available</p>
                                    <p class="text-sm font-medium">{{ $driverStats['available_drivers'] }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">On Ride</p>
                                    <p class="text-sm font-medium">{{ $driverStats['on_ride_drivers'] }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Total Drivers -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-500">Total Drivers</p>
                                    <p class="text-2xl font-bold">{{ $driverStats['total_drivers'] }}</p>
                                </div>
                                <div class="bg-gray-100 p-3 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-2 mt-3">
                                <div>
                                    <p class="text-xs text-gray-500">Active</p>
                                    <p class="text-sm font-medium">{{ $driverStats['active_drivers'] }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Pending</p>
                                    <p class="text-sm font-medium">{{ $driverStats['pending_drivers'] }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Suspended</p>
                                    <p class="text-sm font-medium">{{ $driverStats['suspended_drivers'] }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Pending Rides -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-500">Pending Rides</p>
                                    <p class="text-2xl font-bold">{{ $pendingRides }}</p>
                                </div>
                                <div class="bg-gray-100 p-3 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('admin.rides.pending') }}" class="block w-full bg-blue-500 text-white py-2 px-4 rounded-md font-medium hover:bg-blue-600 transition text-center">
                                    View All Pending Rides
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        // Set up CSRF token for all AJAX requests
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    </script>
</body>
</html>