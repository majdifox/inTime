<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            <a href="" class="text-2xl font-bold">inTime</a>
            
            <!-- Navigation Links -->
            <nav class="hidden md:flex space-x-6">
                <a href="" class="font-medium">Dashboard</a>
                <a href="" class="font-medium text-black">Users Management</a>
                <a href="" class="font-medium">Rides Management</a>
                <a href="" class="font-medium">Reports</a>
                <a href="" class="font-medium">Settings</a>
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
        </div>
    </header>
    <main class="container mx-auto px-4 py-8">
        <div class="max-w-8xl mx-auto">
            <div class="flex items-center justify-between mb-6">
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
                        <p class="text-2xl font-bold text-green-600">248</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <p class="text-sm text-green-600 mt-2">+12% from last month</p>
            </div>
            
            <!-- Cancelled Rides -->
            <div class="bg-red-50 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Cancelled Rides</p>
                        <p class="text-2xl font-bold text-red-600">32</p>
                    </div>
                    <div class="bg-red-100 p-3 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                </div>
                <p class="text-sm text-red-600 mt-2">-3% from last month</p>
            </div>
            
            <!-- General Income -->
            <div class="bg-blue-50 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">General Income</p>
                        <p class="text-2xl font-bold text-blue-600">$12,438</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <p class="text-sm text-blue-600 mt-2">+8% from last month</p>
            </div>
            
            <!-- Average Rating -->
            <div class="bg-yellow-50 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Average Rating</p>
                        <p class="text-2xl font-bold text-yellow-600">4.8</p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                    </div>
                </div>
                <p class="text-sm text-yellow-600 mt-2">+0.2 from last month</p>
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
                            <p class="text-2xl font-bold">18</p>
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
                        <p class="text-sm font-medium">12</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">On Ride</p>
                        <p class="text-sm font-medium">6</p>
                    </div>
                </div>
            </div>
            
            <!-- Total Drivers -->
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Drivers</p>
                        <p class="text-2xl font-bold">42</p>
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
                        <p class="text-sm font-medium">36</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Pending</p>
                        <p class="text-sm font-medium">4</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Suspended</p>
                        <p class="text-sm font-medium">2</p>
                    </div>
                </div>
            </div>
            
            <!-- Pending Rides -->
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Pending Rides</p>
                        <p class="text-2xl font-bold">8</p>
                    </div>
                    <div class="bg-gray-100 p-3 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-3">
                    <button class="w-full bg-blue-500 text-white py-2 px-4 rounded-md font-medium hover:bg-blue-600 transition">
                        View All Pending Rides
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
        </div>        
        </div>
</main>
    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <div class="max-w-8xl mx-auto">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold">Users Management</h1>
                <div class="flex space-x-2">
                    <button id="show-drivers" class="px-4 py-2 bg-black text-white rounded-md hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black">
                        Drivers
                    </button>
                    <button id="show-passengers" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black">
                        Passengers
                    </button>
                </div>
            </div>
            
            <!-- Search & Filter Section -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                <div class="p-4">
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <input type="text" placeholder="Search by name, email or phone..." class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-black focus:border-black">
                        </div>
                        <div class="flex gap-2">
                            <select class="px py-2 border border-gray-300 rounded-md focus:ring-black focus:border-black">
                                <option value="">All Statuses</option>
                                <option value="activated">Activated</option>
                                <option value="deactivated">Deactivated</option>
                                <option value="pending">Pending</option>
                                <option value="suspended">Suspended</option>
                            </select>
                            <button class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black">
                                Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Users Table -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                    Created At
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                    User Name
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Birthday
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Role
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Email
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Phone
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Balance
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="drivers-table">
                            <!-- Sample Driver 1 -->
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    2025-01-15
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0 mr-3">
                                            <img class="h-10 w-10 rounded-full" src="/api/placeholder/40/40" alt="">
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                John Driver
                                            </div>
                                            <div class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                                <span class="text-xs text-gray-500 ml-1">4.5</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    1985-06-22
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        Driver
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    john.driver@example.com
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    +212-555-7890
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Activated
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    DH 2,500.00
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button class="text-green-600 hover:text-green-900" title="Activate">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                        <button class="text-red-600 hover:text-red-900" title="Deactivate">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                        <button class="text-yellow-600 hover:text-yellow-900" title="Suspend">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </button>
                                        <button class="text-gray-600 hover:text-gray-900" title="Delete">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                        <button class="text-blue-600 hover:text-blue-900" title="View Details" onclick="openDriverModal(1)">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <!-- Sample Driver 2 -->
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    2025-02-03
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0 mr-3">
                                            <img class="h-10 w-10 rounded-full" src="/api/placeholder/40/40" alt="">
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                Jane Driver
                                            </div>
                                            <div class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                                <span class="text-xs text-gray-500 ml-1">4.8</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    1990-04-15
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        Driver
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    jane.driver@example.com
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    +212-555-1234
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Pending
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    DH 1,200.00
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button class="text-green-600 hover:text-green-900" title="Activate">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                        <button class="text-red-600 hover:text-red-900" title="Deactivate">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                        <button class="text-yellow-600 hover:text-yellow-900" title="Suspend">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </button>
                                        <button class="text-gray-600 hover:text-gray-900" title="Delete">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                        <button class="text-blue-600 hover:text-blue-900" title="View Details" onclick="openDriverModal(2)">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        
                        <!-- Passengers table - initially hidden -->
                        <tbody class="bg-white divide-y divide-gray-200 hidden" id="passengers-table">
                            <!-- Sample Passenger 1 -->
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    2025-01-10
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0 mr-3">
                                            <img class="h-10 w-10 rounded-full" src="/api/placeholder/40/40" alt="">
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                John Passenger
                                            </div>
                                            <div class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                                <span class="text-xs text-gray-500 ml-1">4.7</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    1992-08-14
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                        Passenger
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    john.passenger@example.com
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    +212-555-6789
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Activated
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    DH 0.00
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button class="text-green-600 hover:text-green-900" title="Activate">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                        <button class="text-red-600 hover:text-red-900" title="Deactivate">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                        <button class="text-yellow-600 hover:text-yellow-900" title="Suspend">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </button>
                                        <button class="text-gray-600 hover:text-gray-900" title="Delete">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                        <button class="text-blue-600 hover:text-blue-900" title="View Details" onclick="openPassengerModal(1)">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                           <!-- Sample Passenger 2 -->
                           <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    2025-02-18
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0 mr-3">
                                            <img class="h-10 w-10 rounded-full" src="/api/placeholder/40/40" alt="">
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                Jane Passenger
                                            </div>
                                            <div class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                                <span class="text-xs text-gray-500 ml-1">4.3</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    1995-11-05
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                        Passenger
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    jane.passenger@example.com
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    +212-555-4321
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Deactivated
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    DH 0.00
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button class="text-green-600 hover:text-green-900" title="Activate">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                        <button class="text-red-600 hover:text-red-900" title="Deactivate">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                        <button class="text-yellow-600 hover:text-yellow-900" title="Suspend">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </button>
                                        <button class="text-gray-600 hover:text-gray-900" title="Delete">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                        <button class="text-blue-600 hover:text-blue-900" title="View Details" onclick="openPassengerModal(2)">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div class="px-6 py-3 flex items-center justify-between border-t border-gray-200">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Previous
                        </a>
                        <a href="#" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Next
                        </a>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Showing <span class="font-medium">1</span> to <span class="font-medium">10</span> of <span class="font-medium">97</span> results
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <span class="sr-only">Previous</span>
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                                <a href="#" aria-current="page" class="z-10 bg-black border-black text-white relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                    1
                                </a>
                                <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                    2
                                </a>
                                <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                    3
                                </a>
                                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                                    ...
                                </span>
                                <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                    10
                                </a>
                                <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <span class="sr-only">Next</span>
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Driver Details Modal -->
    <div id="driver-modal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-screen overflow-y-auto">
            <div class="sticky top-0 bg-white px-6 py-4 border-b flex items-center justify-between">
                <h2 class="text-xl font-bold">Driver Details</h2>
                <button onclick="closeDriverModal()" class="text-gray-400 hover:text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="px-6 py-4">
                <!-- User Basic Info Section -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-4">User Information</h3>
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="md:w-1/3">
                            <div class="h-40 w-40 rounded-lg bg-gray-200 overflow-hidden">
                                <img id="driver-profile-picture" src="/api/placeholder/160/160" alt="Profile" class="h-full w-full object-cover">
                            </div>
                        </div>
                        <div class="md:w-2/3 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Name</p>
                                <p id="driver-name" class="font-medium">John Driver</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Email</p>
                                <p id="driver-email" class="font-medium">john.driver@example.com</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Phone</p>
                                <p id="driver-phone" class="font-medium">+212-555-7890</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Birthday</p>
                                <p id="driver-birthday" class="font-medium">22/06/1985</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Joined Date</p>
                                <p id="driver-joined-date" class="font-medium">15/01/2025</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Status</p>
                                <span id="driver-status" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Activated
                                </span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Rating</p>
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    <span id="driver-rating" class="ml-1">4.5</span>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Completed Rides</p>
                                <p id="driver-completed-rides" class="font-medium">245</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Balance</p>
                                <p id="driver-balance" class="font-medium">DH 2,500.00</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Driver License Info -->
                <div class="mb-6 border-t pt-6">
                    <h3 class="text-lg font-semibold mb-4">License Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">License Number</p>
                            <p id="driver-license-number" class="font-medium">DL-123456789</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">License Expiry</p>
                            <p id="driver-license-expiry" class="font-medium">12/10/2027</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Verification Status</p>
                            <span id="driver-verification-status" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Verified
                            </span>
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="text-sm text-gray-500 mb-2">License Photo</p>
                        <div class="h-40 w-64 rounded-lg bg-gray-200 overflow-hidden">
                            <img id="driver-license-photo" src="/api/placeholder/256/160" alt="License" class="h-full w-full object-cover">
                        </div>
                    </div>
                </div>
                
                <!-- Vehicle Information -->
                <div class="mb-6 border-t pt-6">
                    <h3 class="text-lg font-semibold mb-4">Vehicle Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Make & Model</p>
                            <p id="vehicle-make-model" class="font-medium">Toyota Camry</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Year</p>
                            <p id="vehicle-year" class="font-medium">2022</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Color</p>
                            <p id="vehicle-color" class="font-medium">Silver</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Plate Number</p>
                            <p id="vehicle-plate" class="font-medium">ABC-1234</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Type</p>
                            <p id="vehicle-type" class="font-medium">Comfort</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Capacity</p>
                            <p id="vehicle-capacity" class="font-medium">4 passengers</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Insurance Expiry</p>
                            <p id="vehicle-insurance-expiry" class="font-medium">18/05/2026</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Registration Expiry</p>
                            <p id="vehicle-registration-expiry" class="font-medium">23/09/2026</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Status</p>
                            <span id="vehicle-status" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Active
                            </span>
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="text-sm text-gray-500 mb-2">Vehicle Photo</p>
                        <div class="h-40 w-64 rounded-lg bg-gray-200 overflow-hidden">
                            <img id="vehicle-photo" src="/api/placeholder/256/160" alt="Vehicle" class="h-full w-full object-cover">
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="mt-6 flex justify-end space-x-3 border-t pt-6">
                    <button class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Activate
                    </button>
                    <button class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Deactivate
                    </button>
                    <button class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                        Suspend
                    </button>
                    <button class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Passenger Details Modal -->
    <div id="passenger-modal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-screen overflow-y-auto">
            <div class="sticky top-0 bg-white px-6 py-4 border-b flex items-center justify-between">
                <h2 class="text-xl font-bold">Passenger Details</h2>
                <button onclick="closePassengerModal()" class="text-gray-400 hover:text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="px-6 py-4">
                <!-- User Basic Info Section -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-4">User Information</h3>
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="md:w-1/3">
                            <div class="h-40 w-40 rounded-lg bg-gray-200 overflow-hidden">
                                <img id="passenger-profile-picture" src="/api/placeholder/160/160" alt="Profile" class="h-full w-full object-cover">
                            </div>
                        </div>
                        <div class="md:w-2/3 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Name</p>
                                <p id="passenger-name" class="font-medium">John Passenger</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Email</p>
                                <p id="passenger-email" class="font-medium">john.passenger@example.com</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Phone</p>
                                <p id="passenger-phone" class="font-medium">+212-555-6789</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Birthday</p>
                                <p id="passenger-birthday" class="font-medium">14/08/1992</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Joined Date</p>
                                <p id="passenger-joined-date" class="font-medium">10/01/2025</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Status</p>
                                <span id="passenger-status" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Activated
                                </span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Rating</p>
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    <span id="passenger-rating" class="ml-1">4.7</span>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Total Rides</p>
                                <p id="passenger-total-rides" class="font-medium">35</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Passenger Preferences -->
                <div class="mb-6 border-t pt-6">
                    <h3 class="text-lg font-semibold mb-4">Preferences</h3>
                    <div id="passenger-preferences" class="prose max-w-none">
                        <ul>
                            <li>Prefers air conditioning turned on</li>
                            <li>Prefers minimal conversation during rides</li>
                            <li>Prefers back seat</li>
                        </ul>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="mt-6 flex justify-end space-x-3 border-t pt-6">
                    <button class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Activate
                    </button>
                    <button class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Deactivate
                    </button>
                    <button class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                        Suspend
                    </button>
                    <button class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Delete
                    </button>
                </div>
                
            </div>
            
        </div>
        
    </div>
    

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>
    <script>
        // Toggle between drivers and passengers tables
        document.getElementById('show-drivers').addEventListener('click', function() {
    document.getElementById('drivers-table').classList.remove('hidden');
    document.getElementById('passengers-table').classList.add('hidden');
    document.getElementById('show-drivers').classList.remove('bg-white', 'border', 'border-gray-300', 'text-gray-700');
    document.getElementById('show-drivers').classList.add('bg-black', 'text-white', 'hover:bg-gray-800');
    document.getElementById('show-passengers').classList.remove('bg-black', 'text-white', 'hover:bg-gray-800');
    document.getElementById('show-passengers').classList.add('bg-white', 'border', 'border-gray-300', 'text-gray-700');
        });

        // Toggle to passengers table
        document.getElementById('show-passengers').addEventListener('click', function() {
            document.getElementById('passengers-table').classList.remove('hidden');
            document.getElementById('drivers-table').classList.add('hidden');
            document.getElementById('show-passengers').classList.remove('bg-white', 'border', 'border-gray-300', 'text-gray-700');
            document.getElementById('show-passengers').classList.add('bg-black', 'text-white', 'hover:bg-gray-800');
            document.getElementById('show-drivers').classList.remove('bg-black', 'text-white', 'hover:bg-gray-800');
            document.getElementById('show-drivers').classList.add('bg-white', 'border', 'border-gray-300', 'text-gray-700');
        });

        // Driver Modal Functions
        function openDriverModal(driverId) {
            // Fetch driver data based on driverId (you can replace this with actual data fetching logic)
            const driverData = {
                name: "John Driver",
                email: "john.driver@example.com",
                phone: "+212-555-7890",
                birthday: "22/06/1985",
                joinedDate: "15/01/2025",
                status: "Activated",
                rating: "4.5",
                completedRides: "245",
                balance: "DH 2,500.00",
                licenseNumber: "DL-123456789",
                licenseExpiry: "12/10/2027",
                verificationStatus: "Verified",
                vehicleMakeModel: "Toyota Camry",
                vehicleYear: "2022",
                vehicleColor: "Silver",
                vehiclePlate: "ABC-1234",
                vehicleType: "Comfort",
                vehicleCapacity: "4 passengers",
                vehicleInsuranceExpiry: "18/05/2026",
                vehicleRegistrationExpiry: "23/09/2026",
                vehicleStatus: "Active"
            };

            // Populate modal with driver data
            document.getElementById('driver-name').textContent = driverData.name;
            document.getElementById('driver-email').textContent = driverData.email;
            document.getElementById('driver-phone').textContent = driverData.phone;
            document.getElementById('driver-birthday').textContent = driverData.birthday;
            document.getElementById('driver-joined-date').textContent = driverData.joinedDate;
            document.getElementById('driver-status').textContent = driverData.status;
            document.getElementById('driver-rating').textContent = driverData.rating;
            document.getElementById('driver-completed-rides').textContent = driverData.completedRides;
            document.getElementById('driver-balance').textContent = driverData.balance;
            document.getElementById('driver-license-number').textContent = driverData.licenseNumber;
            document.getElementById('driver-license-expiry').textContent = driverData.licenseExpiry;
            document.getElementById('driver-verification-status').textContent = driverData.verificationStatus;
            document.getElementById('vehicle-make-model').textContent = driverData.vehicleMakeModel;
            document.getElementById('vehicle-year').textContent = driverData.vehicleYear;
            document.getElementById('vehicle-color').textContent = driverData.vehicleColor;
            document.getElementById('vehicle-plate').textContent = driverData.vehiclePlate;
            document.getElementById('vehicle-type').textContent = driverData.vehicleType;
            document.getElementById('vehicle-capacity').textContent = driverData.vehicleCapacity;
            document.getElementById('vehicle-insurance-expiry').textContent = driverData.vehicleInsuranceExpiry;
            document.getElementById('vehicle-registration-expiry').textContent = driverData.vehicleRegistrationExpiry;
            document.getElementById('vehicle-status').textContent = driverData.vehicleStatus;

            // Show the modal
            document.getElementById('driver-modal').classList.remove('hidden');
        }

        function closeDriverModal() {
            document.getElementById('driver-modal').classList.add('hidden');
        }

        // Passenger Modal Functions
        function openPassengerModal(passengerId) {
            // Fetch passenger data based on passengerId (you can replace this with actual data fetching logic)
            const passengerData = {
                name: "John Passenger",
                email: "john.passenger@example.com",
                phone: "+212-555-6789",
                birthday: "14/08/1992",
                joinedDate: "10/01/2025",
                status: "Activated",
                rating: "4.7",
                totalRides: "35",
                preferences: [
                    "Prefers air conditioning turned on",
                    "Prefers minimal conversation during rides",
                    "Prefers back seat"
                ]
            };

            // Populate modal with passenger data
            document.getElementById('passenger-name').textContent = passengerData.name;
            document.getElementById('passenger-email').textContent = passengerData.email;
            document.getElementById('passenger-phone').textContent = passengerData.phone;
            document.getElementById('passenger-birthday').textContent = passengerData.birthday;
            document.getElementById('passenger-joined-date').textContent = passengerData.joinedDate;
            document.getElementById('passenger-status').textContent = passengerData.status;
            document.getElementById('passenger-rating').textContent = passengerData.rating;
            document.getElementById('passenger-total-rides').textContent = passengerData.totalRides;

            // Populate preferences
            const preferencesList = document.createElement('ul');
            passengerData.preferences.forEach(preference => {
                const listItem = document.createElement('li');
                listItem.textContent = preference;
                preferencesList.appendChild(listItem);
            });
            document.getElementById('passenger-preferences').innerHTML = '';
            document.getElementById('passenger-preferences').appendChild(preferencesList);

            // Show the modal
            document.getElementById('passenger-modal').classList.remove('hidden');
        }

        function closePassengerModal() {
            document.getElementById('passenger-modal').classList.add('hidden');
        }
    </script>
</body>
</html>