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
                            <input type="text" id="search-input" placeholder="Search by name, email or phone..." class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-black focus:border-black">
                        </div>
                        <div class="flex gap-2">
                            <select id="status-filter" class="px-4 py-2 border border-gray-300 rounded-md focus:ring-black focus:border-black">
                                <option value="">All Statuses</option>
                                <option value="activated">Activated</option>
                                <option value="deactivated">Deactivated</option>
                                <option value="pending">Pending</option>
                                <option value="suspended">Suspended</option>
                            </select>
                            <button id="filter-button" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black">
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

                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer sort-header" data-field="created_at">
                                    Created At
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer sort-header" data-field="name">
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
                                    Verification
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
                            @foreach($drivers as $driver)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $driver->created_at->format('Y-m-d') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0 mr-3">
                                            @if($driver->profile_picture)
                                                <img class="h-10 w-10 rounded-full" src="{{ Storage::url($driver->profile_picture) }}" alt="">
                                            @else
                                                <img class="h-10 w-10 rounded-full" src="/api/placeholder/40/40" alt="">
                                            @endif
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $driver->name }}
                                            </div>
                                            <div class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                                <span class="text-xs text-gray-500 ml-1">{{ $driver->driver ? number_format($driver->driver->rating, 1) : 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $driver->birthday ? $driver->birthday->format('Y-m-d') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        Driver
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $driver->email }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $driver->phone }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
    @if($driver->driver && $driver->driver->is_verified)
        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
            Verified
        </span>
    @else
        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
            Not Verified
        </span>
    @endif
</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusClasses = [
                                            'activated' => 'bg-green-100 text-green-800',
                                            'deactivated' => 'bg-red-100 text-red-800',
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'suspended' => 'bg-orange-100 text-orange-800',
                                            'deleted' => 'bg-gray-100 text-gray-800'
                                        ];
                                        $statusClass = $statusClasses[$driver->account_status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                        {{ ucfirst($driver->account_status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    DH {{ number_format($driver->driver ? $driver->driver->balance : 0, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button class="text-green-600 hover:text-green-900 user-status-btn" 
                                                data-user-id="{{ $driver->id }}" 
                                                data-status="activated" 
                                                title="Activate">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                        <button class="text-red-600 hover:text-red-900 user-status-btn" 
                                                data-user-id="{{ $driver->id }}" 
                                                data-status="deactivated" 
                                                title="Deactivate">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                        <button class="text-yellow-600 hover:text-yellow-900 user-status-btn" 
                                                data-user-id="{{ $driver->id }}" 
                                                data-status="suspended" 
                                                title="Suspend">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </button>
                                        <button class="text-gray-600 hover:text-gray-900 delete-user-btn" 
                                                data-user-id="{{ $driver->id }}"
                                                title="Delete">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                        <a href="{{ route('admin.driver.show', ['id' => $driver->id]) }}" 
                                        class="text-blue-600 hover:text-blue-900"
                                        title="View Details">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        
                        <!-- Passengers table - initially hidden -->
                        <tbody class="bg-white divide-y divide-gray-200 hidden" id="passengers-table">
                            @foreach($passengers as $passenger)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $passenger->created_at->format('Y-m-d') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0 mr-3">
                                            @if($passenger->profile_picture)
                                                <img class="h-10 w-10 rounded-full" src="{{ Storage::url($passenger->profile_picture) }}" alt="">
                                            @else
                                                <img class="h-10 w-10 rounded-full" src="/api/placeholder/40/40" alt="">
                                            @endif
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $passenger->name }}
                                            </div>
                                            <div class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                                <span class="text-xs text-gray-500 ml-1">{{ $passenger->passenger ? number_format($passenger->passenger->rating, 1) : 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $passenger->birthday ? $passenger->birthday->format('Y-m-d') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                        Passenger
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $passenger->email }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $passenger->phone }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusClasses = [
                                            'activated' => 'bg-green-100 text-green-800',
                                            'deactivated' => 'bg-red-100 text-red-800',
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'suspended' => 'bg-orange-100 text-orange-800',
                                            'deleted' => 'bg-gray-100 text-gray-800'
                                        ];
                                        $statusClass = $statusClasses[$passenger->account_status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                        {{ ucfirst($passenger->account_status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    DH 0.00
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button class="text-green-600 hover:text-green-900 user-status-btn" 
                                                data-user-id="{{ $passenger->id }}" 
                                                data-status="activated" 
                                                title="Activate">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                        <button class="text-red-600 hover:text-red-900 user-status-btn" 
                                                data-user-id="{{ $passenger->id }}" 
                                                data-status="deactivated" 
                                                title="Deactivate">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                        <button class="text-yellow-600 hover:text-yellow-900 user-status-btn" 
                                                data-user-id="{{ $passenger->id }}" 
                                                data-status="suspended" 
                                                title="Suspend">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </button>
                                        <button class="text-gray-600 hover:text-gray-900 delete-user-btn" 
                                                data-user-id="{{ $passenger->id }}"
                                                title="Delete">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                        <a href="{{ route('admin.driver.show', ['id' => $driver->id]) }}" 
                                            class="text-blue-600 hover:text-blue-900"
                                            title="View Details">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
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
                                Showing <span class="font-medium">{{ $drivers->firstItem() ?? 0 }}</span> to <span class="font-medium">{{ $drivers->lastItem() ?? 0 }}</span> of <span class="font-medium">{{ $drivers->total() ?? 0 }}</span> results
                            </p>
                        </div>
                        <div id="pagination-container">
                            {{ $drivers->links() }}
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
                                <p id="driver-name" class="font-medium">Loading...</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Email</p>
                                <p id="driver-email" class="font-medium">Loading...</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Phone</p>
                                <p id="driver-phone" class="font-medium">Loading...</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Birthday</p>
                                <p id="driver-birthday" class="font-medium">Loading...</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Joined Date</p>
                                <p id="driver-joined-date" class="font-medium">Loading...</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Status</p>
                                <span id="driver-status" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Loading...
                                </span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Rating</p>
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    <span id="driver-rating" class="ml-1">Loading...</span>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Completed Rides</p>
                                <p id="driver-completed-rides" class="font-medium">Loading...</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Balance</p>
                                <p id="driver-balance" class="font-medium">Loading...</p>
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
                            <p id="driver-license-number" class="font-medium">Loading...</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">License Expiry</p>
                            <p id="driver-license-expiry" class="font-medium">Loading...</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Verification Status</p>
                            <span id="driver-verification-status" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Loading...
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
                            <p id="vehicle-make-model" class="font-medium">Loading...</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Year</p>
                            <p id="vehicle-year" class="font-medium">Loading...</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Color</p>
                            <p id="vehicle-color" class="font-medium">Loading...</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Plate Number</p>
                            <p id="vehicle-plate" class="font-medium">Loading...</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Type</p>
                            <p id="vehicle-type" class="font-medium">Loading...</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Capacity</p>
                            <p id="vehicle-capacity" class="font-medium">Loading...</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Insurance Expiry</p>
                            <p id="vehicle-insurance-expiry" class="font-medium">Loading...</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Registration Expiry</p>
                            <p id="vehicle-registration-expiry" class="font-medium">Loading...</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Status</p>
                            <span id="vehicle-status" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Loading...
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
                    <button id="driver-activate-btn" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Activate
                    </button>
                    <button id="driver-deactivate-btn" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Deactivate
                    </button>
                    <button id="driver-suspend-btn" class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                        Suspend
                    </button>
                    <button id="driver-delete-btn" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
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
                                <p id="passenger-name" class="font-medium">Loading...</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Email</p>
                                <p id="passenger-email" class="font-medium">Loading...</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Phone</p>
                                <p id="passenger-phone" class="font-medium">Loading...</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Birthday</p>
                                <p id="passenger-birthday" class="font-medium">Loading...</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Joined Date</p>
                                <p id="passenger-joined-date" class="font-medium">Loading...</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Status</p>
                                <span id="passenger-status" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Loading...
                                </span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Rating</p>
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    <span id="passenger-rating" class="ml-1">Loading...</span>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Total Rides</p>
                                <p id="passenger-total-rides" class="font-medium">Loading...</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Passenger Preferences -->
                <div class="mb-6 border-t pt-6">
                    <h3 class="text-lg font-semibold mb-4">Preferences</h3>
                    <div id="passenger-preferences" class="prose max-w-none">
                        <p>Loading preferences...</p>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="mt-6 flex justify-end space-x-3 border-t pt-6">
                    <button id="passenger-activate-btn" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Activate
                    </button>
                    <button id="passenger-deactivate-btn" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Deactivate
                    </button>
                    <button id="passenger-suspend-btn" class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                        Suspend
                    </button>
                    <button id="passenger-delete-btn" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
       // Set up CSRF token for all AJAX requests
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Variables for storing current state
let currentUserId = null;
let currentUserType = null;
let sortField = 'created_at';
let sortDirection = 'desc';
let activeTab = 'drivers'; // 'drivers' or 'passengers'

// Toggle between drivers and passengers tables
document.getElementById('show-drivers').addEventListener('click', function() {
    document.getElementById('drivers-table').classList.remove('hidden');
    document.getElementById('passengers-table').classList.add('hidden');
    document.getElementById('show-drivers').classList.remove('bg-white', 'border', 'border-gray-300', 'text-gray-700');
    document.getElementById('show-drivers').classList.add('bg-black', 'text-white', 'hover:bg-gray-800');
    document.getElementById('show-passengers').classList.remove('bg-black', 'text-white', 'hover:bg-gray-800');
    document.getElementById('show-passengers').classList.add('bg-white', 'border', 'border-gray-300', 'text-gray-700');
    activeTab = 'drivers';
    loadUsers();
});

// Toggle to passengers table
document.getElementById('show-passengers').addEventListener('click', function() {
    document.getElementById('passengers-table').classList.remove('hidden');
    document.getElementById('drivers-table').classList.add('hidden');
    document.getElementById('show-passengers').classList.remove('bg-white', 'border', 'border-gray-300', 'text-gray-700');
    document.getElementById('show-passengers').classList.add('bg-black', 'text-white', 'hover:bg-gray-800');
    document.getElementById('show-drivers').classList.remove('bg-black', 'text-white', 'hover:bg-gray-800');
    document.getElementById('show-drivers').classList.add('bg-white', 'border', 'border-gray-300', 'text-gray-700');
    activeTab = 'passengers';
    loadUsers();
});

// Set up sort headers
document.querySelectorAll('.sort-header').forEach(header => {
    header.addEventListener('click', function() {
        const field = this.getAttribute('data-field');
        if (field === sortField) {
            sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            sortField = field;
            sortDirection = 'asc';
        }
        loadUsers();
    });
});

// Set up search input to trigger on 'Enter' key
document.getElementById('search-input').addEventListener('keyup', function(event) {
    if (event.key === 'Enter') {
        loadUsers();
    }
});

// Set up filter button
document.getElementById('filter-button').addEventListener('click', function() {
    loadUsers();
});

// Set up CSRF token for all AJAX requests
function setupAjax() {
    // Get CSRF token from meta tag
    const token = document.querySelector('meta[name="csrf-token"]');
    
    if (token) {
        // Set default headers for Axios
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content');
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        
        console.log('CSRF token set for AJAX requests');
    } else {
        console.error('CSRF token not found!');
    }
    
    // Add a request interceptor to show any errors
    axios.interceptors.response.use(
        response => response,
        error => {
            console.error('Axios request failed:', error);
            
            // Log more detailed error information if available
            if (error.response) {
                console.error('Server responded with:', error.response.status, error.response.data);
            } else if (error.request) {
                console.error('No response received:', error.request);
            } else {
                console.error('Error setting up request:', error.message);
            }
            
            return Promise.reject(error);
        }
    );
}

// Call this function when the document loads
document.addEventListener('DOMContentLoaded', function() {
    setupAjax();
    attachEventListeners();
});

// Load users based on current filters and active tab
function loadUsers() {
    const searchTerm = document.getElementById('search-input').value;
    const statusFilter = document.getElementById('status-filter').value;
    
    // Show loading state
    const activeTable = document.getElementById(activeTab === 'drivers' ? 'drivers-table' : 'passengers-table');
    activeTable.innerHTML = '<tr><td colspan="9" class="px-6 py-4 text-center">Loading...</td></tr>';
    
    const endpoint = activeTab === 'drivers' ? '/admin/drivers' : '/admin/passengers';
    
    console.log(`Loading ${activeTab} with search: ${searchTerm}, status: ${statusFilter}, sort: ${sortField} ${sortDirection}`);
    
    axios.get(endpoint, {
        params: {
            search: searchTerm,
            status: statusFilter,
            sort_field: sortField,
            sort_direction: sortDirection
        }
    })
    .then(function(response) {
        console.log(`${activeTab} data loaded successfully`, response.data);
        
        // Replace table content with new data
        if (activeTab === 'drivers') {
            document.getElementById('drivers-table').innerHTML = response.data.html;
        } else {
            document.getElementById('passengers-table').innerHTML = response.data.html;
        }
        
        // Update pagination
        const paginationContainer = document.getElementById('pagination-container');
        if (paginationContainer) {
            paginationContainer.innerHTML = response.data.pagination;
        }
        
        // Reattach event listeners to the new DOM elements
        attachEventListeners();
    })
    .catch(function(error) {
        console.error(`Error loading ${activeTab}:`, error);
        
        let errorMessage = 'Error loading data. Please try again.';
        if (error.response && error.response.data && error.response.data.message) {
            errorMessage = error.response.data.message;
        }
        
        activeTable.innerHTML = `<tr><td colspan="9" class="px-6 py-4 text-center text-red-500">${errorMessage}</td></tr>`;
        
        // Try to reattach event listeners anyway for any existing elements
        setTimeout(() => {
            try {
                attachEventListeners();
            } catch (e) {
                console.error('Error reattaching event listeners:', e);
            }
        }, 100);
    });
}
// Attach event listeners to the elements (needed after DOM updates)
function attachEventListeners() {
    console.log('Attaching event listeners to updated DOM elements');
    
    // Attach event listeners to view details buttons
    document.querySelectorAll('.view-details-btn').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            const userType = this.getAttribute('data-user-type');
            
            console.log(`View details clicked for ${userType} ${userId}`);
            
            if (userType === 'driver') {
                openDriverModal(userId);
            } else {
                openPassengerModal(userId);
            }
        });
    });
    
    // Attach event listeners to user status buttons
    document.querySelectorAll('.user-status-btn').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            const status = this.getAttribute('data-status');
            
            console.log(`Status change clicked: ${status} for user ${userId}`);
            
            updateUserStatus(userId, status);
        });
    });
    
    // Attach event listeners to delete user buttons
    document.querySelectorAll('.delete-user-btn').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            
            console.log(`Delete clicked for user ${userId}`);
            
            deleteUser(userId);
        });
    });
    
    // Attach event listeners to pagination links
    document.querySelectorAll('.pagination-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const url = this.getAttribute('href');
            if (url) {
                console.log(`Pagination link clicked: ${url}`);
                
                // Extract page number from URL
                const urlObj = new URL(url, window.location.origin);
                const page = urlObj.searchParams.get('page');
                
                console.log(`Loading page ${page}`);
                
                // Load the specific page
                loadUsersPage(page);
            }
        });
    });
    
    console.log('Event listeners attached successfully');
}

// Load specific page of users
function loadUsersPage(page) {
    const searchTerm = document.getElementById('search-input').value;
    const statusFilter = document.getElementById('status-filter').value;
    
    const endpoint = activeTab === 'drivers' ? '/admin/drivers' : '/admin/passengers';
    
    axios.get(endpoint, {
        params: {
            search: searchTerm,
            status: statusFilter,
            sort_field: sortField,
            sort_direction: sortDirection,
            page: page
        }
    })
    .then(function(response) {
        // Update table and pagination as before
        if (activeTab === 'drivers') {
            document.getElementById('drivers-table').innerHTML = response.data.html;
        } else {
            document.getElementById('passengers-table').innerHTML = response.data.html;
        }
        
        document.getElementById('pagination-container').innerHTML = response.data.pagination;
        
        // Reattach event listeners
        attachEventListeners();
    })
    .catch(function(error) {
        console.error('Error loading users page:', error);
    });
}

// Driver Modal Functions
function openDriverModal(driverId) {
    currentUserId = driverId;
    currentUserType = 'driver';
    
    document.getElementById('driver-modal').classList.remove('hidden');
    document.getElementById('driver-modal').classList.add('flex');
    
    // Show loading state for all fields
    document.getElementById('driver-name').textContent = 'Loading...';
    document.getElementById('driver-email').textContent = 'Loading...';
    document.getElementById('driver-phone').textContent = 'Loading...';
    document.getElementById('driver-birthday').textContent = 'Loading...';
    document.getElementById('driver-joined-date').textContent = 'Loading...';
    document.getElementById('driver-status').textContent = 'Loading...';
    document.getElementById('driver-rating').textContent = 'Loading...';
    document.getElementById('driver-completed-rides').textContent = 'Loading...';
    document.getElementById('driver-balance').textContent = 'Loading...';
    document.getElementById('driver-license-number').textContent = 'Loading...';
    document.getElementById('driver-license-expiry').textContent = 'Loading...';
    document.getElementById('driver-verification-status').textContent = 'Loading...';
    
    // Reset vehicle information to loading state
    document.getElementById('vehicle-make-model').textContent = 'Loading...';
    document.getElementById('vehicle-year').textContent = 'Loading...';
    document.getElementById('vehicle-color').textContent = 'Loading...';
    document.getElementById('vehicle-plate').textContent = 'Loading...';
    document.getElementById('vehicle-type').textContent = 'Loading...';
    document.getElementById('vehicle-capacity').textContent = 'Loading...';
    document.getElementById('vehicle-insurance-expiry').textContent = 'Loading...';
    document.getElementById('vehicle-registration-expiry').textContent = 'Loading...';
    document.getElementById('vehicle-status').textContent = 'Loading...';
    
    // Reset images
    document.getElementById('driver-profile-picture').src = '/api/placeholder/160/160';
    document.getElementById('driver-license-photo').src = '/api/placeholder/256/160';
    document.getElementById('vehicle-photo').src = '/api/placeholder/256/160';
    
    // Add debugging for the API call
    console.log(`Fetching driver details for ID: ${driverId}`);
    
    // Fetch driver details
    axios.get(`/admin/driver/${driverId}`)
        .then(function(response) {
            console.log('Driver data received:', response.data);
            
            if (!response.data.success) {
                throw new Error('Failed to fetch driver details');
            }
            
            const driver = response.data.driver;
            
            // Debug the driver object
            console.log('Driver object structure:', JSON.stringify(driver, null, 2));
            
            // Populate user info
            document.getElementById('driver-name').textContent = driver.name || 'N/A';
            document.getElementById('driver-email').textContent = driver.email || 'N/A';
            document.getElementById('driver-phone').textContent = driver.phone || 'N/A';
            document.getElementById('driver-birthday').textContent = driver.birthday ? new Date(driver.birthday).toLocaleDateString() : 'N/A';
            document.getElementById('driver-joined-date').textContent = driver.created_at ? new Date(driver.created_at).toLocaleDateString() : 'N/A';
            
            // Status classes
            const statusClasses = {
                'activated': 'bg-green-100 text-green-800',
                'deactivated': 'bg-red-100 text-red-800',
                'pending': 'bg-yellow-100 text-yellow-800',
                'suspended': 'bg-orange-100 text-orange-800'
            };
            
            const statusElement = document.getElementById('driver-status');
            statusElement.textContent = driver.account_status ? (driver.account_status.charAt(0).toUpperCase() + driver.account_status.slice(1)) : 'N/A';
            statusElement.className = 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full';
            const statusClass = driver.account_status ? (statusClasses[driver.account_status] || 'bg-gray-100 text-gray-800') : 'bg-gray-100 text-gray-800';
            statusElement.classList.add(...statusClass.split(' '));
            
            // Profile picture
            if (driver.profile_picture) {
                document.getElementById('driver-profile-picture').src = driver.profile_picture;
            }
            
            // Driver specific info
            if (driver.driver) {
                console.log('Driver profile data:', driver.driver);
                
                const driverData = driver.driver;
                document.getElementById('driver-rating').textContent = driverData.rating ? parseFloat(driverData.rating).toFixed(1) : 'N/A';
                document.getElementById('driver-completed-rides').textContent = driverData.completed_rides !== undefined ? driverData.completed_rides : 0;
                document.getElementById('driver-balance').textContent = `DH ${parseFloat(driverData.balance || 0).toFixed(2)}`;
                
                // License info
                document.getElementById('driver-license-number').textContent = driverData.license_number || 'N/A';
                document.getElementById('driver-license-expiry').textContent = driverData.license_expiry ? new Date(driverData.license_expiry).toLocaleDateString() : 'N/A';
                
                const verificationElement = document.getElementById('driver-verification-status');
                verificationElement.textContent = driverData.is_verified ? 'Verified' : 'Not Verified';
                verificationElement.className = 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full';
                verificationElement.classList.add(driverData.is_verified ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800');
                
                // License photo
                if (driverData.license_photo) {
                    document.getElementById('driver-license-photo').src = driverData.license_photo;
                    console.log('License photo URL:', driverData.license_photo);
                }
                
                // Vehicle info - Check if vehicle exists
                if (driverData.vehicle) {
                    console.log('Vehicle data:', driverData.vehicle);
                    
                    const vehicle = driverData.vehicle;
                    document.getElementById('vehicle-make-model').textContent = `${vehicle.make || ''} ${vehicle.model || ''}`.trim() || 'N/A';
                    document.getElementById('vehicle-year').textContent = vehicle.year || 'N/A';
                    document.getElementById('vehicle-color').textContent = vehicle.color || 'N/A';
                    document.getElementById('vehicle-plate').textContent = vehicle.plate_number || 'N/A';
                    document.getElementById('vehicle-type').textContent = vehicle.type ? (vehicle.type.charAt(0).toUpperCase() + vehicle.type.slice(1)) : 'N/A';
                    document.getElementById('vehicle-capacity').textContent = vehicle.capacity ? `${vehicle.capacity} passengers` : 'N/A';
                    document.getElementById('vehicle-insurance-expiry').textContent = vehicle.insurance_expiry ? new Date(vehicle.insurance_expiry).toLocaleDateString() : 'N/A';
                    document.getElementById('vehicle-registration-expiry').textContent = vehicle.registration_expiry ? new Date(vehicle.registration_expiry).toLocaleDateString() : 'N/A';
                    
                    const vehicleStatusElement = document.getElementById('vehicle-status');
                    vehicleStatusElement.textContent = vehicle.is_active ? 'Active' : 'Inactive';
                    vehicleStatusElement.className = 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full';
                    vehicleStatusElement.classList.add(vehicle.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800');
                    
                    // Vehicle photo
                    if (vehicle.vehicle_photo) {
                        document.getElementById('vehicle-photo').src = vehicle.vehicle_photo;
                        console.log('Vehicle photo URL:', vehicle.vehicle_photo);
                    }
                } else {
                    // No vehicle data
                    document.getElementById('vehicle-make-model').textContent = 'No vehicle registered';
                    document.getElementById('vehicle-year').textContent = 'N/A';
                    document.getElementById('vehicle-color').textContent = 'N/A';
                    document.getElementById('vehicle-plate').textContent = 'N/A';
                    document.getElementById('vehicle-type').textContent = 'N/A';
                    document.getElementById('vehicle-capacity').textContent = 'N/A';
                    document.getElementById('vehicle-insurance-expiry').textContent = 'N/A';
                    document.getElementById('vehicle-registration-expiry').textContent = 'N/A';
                    document.getElementById('vehicle-status').textContent = 'N/A';
                }
            } else {
                // No driver profile data
                document.getElementById('driver-rating').textContent = 'N/A';
                document.getElementById('driver-completed-rides').textContent = 'N/A';
                document.getElementById('driver-balance').textContent = 'N/A';
                document.getElementById('driver-license-number').textContent = 'N/A';
                document.getElementById('driver-license-expiry').textContent = 'N/A';
                document.getElementById('driver-verification-status').textContent = 'N/A';
                
                // No vehicle info
                document.getElementById('vehicle-make-model').textContent = 'No driver profile';
                document.getElementById('vehicle-year').textContent = 'N/A';
                document.getElementById('vehicle-color').textContent = 'N/A';
                document.getElementById('vehicle-plate').textContent = 'N/A';
                document.getElementById('vehicle-type').textContent = 'N/A';
                document.getElementById('vehicle-capacity').textContent = 'N/A';
                document.getElementById('vehicle-insurance-expiry').textContent = 'N/A';
                document.getElementById('vehicle-registration-expiry').textContent = 'N/A';
                document.getElementById('vehicle-status').textContent = 'N/A';
            }
            
            // Set up action buttons
            setupDriverActionButtons(driverId);
        })
        .catch(function(error) {
            console.error('Error fetching driver details:', error);
            console.error('Error details:', error.response ? error.response.data : 'No response data');
            
            // Show error state but don't close the modal or show alert
            document.getElementById('driver-name').textContent = 'Error loading data';
            document.getElementById('driver-email').textContent = 'Please try again';
            document.getElementById('driver-phone').textContent = 'N/A';
            document.getElementById('driver-birthday').textContent = 'N/A';
            document.getElementById('driver-joined-date').textContent = 'N/A';
            document.getElementById('driver-status').textContent = 'N/A';
            document.getElementById('driver-rating').textContent = 'N/A';
            document.getElementById('driver-completed-rides').textContent = 'N/A';
            document.getElementById('driver-balance').textContent = 'N/A';
            document.getElementById('driver-license-number').textContent = 'N/A';
            document.getElementById('driver-license-expiry').textContent = 'N/A';
            document.getElementById('driver-verification-status').textContent = 'N/A';
            document.getElementById('vehicle-make-model').textContent = 'Error loading vehicle data';
            
            // Setup action buttons anyway so the modal can be closed
            setupDriverActionButtons(driverId);
        });
}

function closeDriverModal() {
    document.getElementById('driver-modal').classList.add('hidden');
    document.getElementById('driver-modal').classList.remove('flex');
    currentUserId = null;
    currentUserType = null;
}

// Passenger Modal Functions
function openPassengerModal(passengerId) {
    currentUserId = passengerId;
    currentUserType = 'passenger';
    
    document.getElementById('passenger-modal').classList.remove('hidden');
    document.getElementById('passenger-modal').classList.add('flex');
    
    // Show loading state
    document.getElementById('passenger-name').textContent = 'Loading...';
    document.getElementById('passenger-email').textContent = 'Loading...';
    document.getElementById('passenger-phone').textContent = 'Loading...';
    document.getElementById('passenger-birthday').textContent = 'Loading...';
    document.getElementById('passenger-joined-date').textContent = 'Loading...';
    document.getElementById('passenger-status').textContent = 'Loading...';
    document.getElementById('passenger-rating').textContent = 'Loading...';
    document.getElementById('passenger-total-rides').textContent = 'Loading...';
    document.getElementById('passenger-preferences').innerHTML = 'Loading...';
    
    // Reset profile picture
    document.getElementById('passenger-profile-picture').src = '/api/placeholder/160/160';
    
    // Fetch passenger details
    axios.get(`/admin/passenger/${passengerId}`)
        .then(function(response) {
            console.log('Passenger data received:', response.data);
            
            if (!response.data.success) {
                throw new Error('Failed to fetch passenger details');
            }
            
            const passenger = response.data.passenger;
            
            // Populate user info
            document.getElementById('passenger-name').textContent = passenger.name || 'N/A';
            document.getElementById('passenger-email').textContent = passenger.email || 'N/A';
            document.getElementById('passenger-phone').textContent = passenger.phone || 'N/A';
            document.getElementById('passenger-birthday').textContent = passenger.birthday ? new Date(passenger.birthday).toLocaleDateString() : 'N/A';
            document.getElementById('passenger-joined-date').textContent = new Date(passenger.created_at).toLocaleDateString();
            
            // Status classes
            const statusClasses = {
                'activated': 'bg-green-100 text-green-800',
                'deactivated': 'bg-red-100 text-red-800',
                'pending': 'bg-yellow-100 text-yellow-800',
                'suspended': 'bg-orange-100 text-orange-800'
            };
            
            const statusElement = document.getElementById('passenger-status');
            statusElement.textContent = passenger.account_status.charAt(0).toUpperCase() + passenger.account_status.slice(1);
            statusElement.className = 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full';
            statusElement.classList.add(...(statusClasses[passenger.account_status] || 'bg-gray-100 text-gray-800').split(' '));
            
            // Profile picture
            if (passenger.profile_picture) {
                document.getElementById('passenger-profile-picture').src = passenger.profile_picture;
            }
            
            if (passenger.passenger) {
                // Passenger specific info
                document.getElementById('passenger-rating').textContent = passenger.passenger.rating || 'N/A';
                document.getElementById('passenger-total-rides').textContent = passenger.passenger.total_rides || 0;
                
                // Preferences
                const preferencesList = document.createElement('ul');
                if (passenger.passenger.preferences && passenger.passenger.preferences.length > 0) {
                    passenger.passenger.preferences.forEach(preference => {
                        const listItem = document.createElement('li');
                        listItem.textContent = preference;
                        preferencesList.appendChild(listItem);
                    });
                } else {
                    const listItem = document.createElement('li');
                    listItem.textContent = 'No preferences set';
                    preferencesList.appendChild(listItem);
                }
                document.getElementById('passenger-preferences').innerHTML = '';
                document.getElementById('passenger-preferences').appendChild(preferencesList);
            } else {
                // No passenger profile data
                document.getElementById('passenger-rating').textContent = 'N/A';
                document.getElementById('passenger-total-rides').textContent = 'N/A';
                document.getElementById('passenger-preferences').innerHTML = '<p>No preferences found</p>';
            }
            
            // Set up action buttons
            setupPassengerActionButtons(passengerId);
        })
        .catch(function(error) {
            console.error('Error fetching passenger details:', error);
            
            // Show error state but don't close the modal or show alert
            document.getElementById('passenger-name').textContent = 'Error loading data';
            document.getElementById('passenger-email').textContent = 'Please try again';
            document.getElementById('passenger-phone').textContent = 'N/A';
            document.getElementById('passenger-birthday').textContent = 'N/A';
            document.getElementById('passenger-joined-date').textContent = 'N/A';
            document.getElementById('passenger-status').textContent = 'N/A';
            document.getElementById('passenger-rating').textContent = 'N/A';
            document.getElementById('passenger-total-rides').textContent = 'N/A';
            document.getElementById('passenger-preferences').innerHTML = '<p>Error loading preferences</p>';
            
            // Setup action buttons anyway so the modal can be closed
            setupPassengerActionButtons(passengerId);
        });
}

function closePassengerModal() {
    document.getElementById('passenger-modal').classList.add('hidden');
    document.getElementById('passenger-modal').classList.remove('flex');
    currentUserId = null;
    currentUserType = null;
}
// Set up driver action buttons
function setupDriverActionButtons(driverId) {
    // Get all action buttons
    const activateBtn = document.getElementById('driver-activate-btn');
    const deactivateBtn = document.getElementById('driver-deactivate-btn');
    const suspendBtn = document.getElementById('driver-suspend-btn');
    const deleteBtn = document.getElementById('driver-delete-btn');
    
    // Remove all existing event listeners by cloning the buttons
    const newActivateBtn = activateBtn.cloneNode(true);
    activateBtn.parentNode.replaceChild(newActivateBtn, activateBtn);
    
    const newDeactivateBtn = deactivateBtn.cloneNode(true);
    deactivateBtn.parentNode.replaceChild(newDeactivateBtn, deactivateBtn);
    
    const newSuspendBtn = suspendBtn.cloneNode(true);
    suspendBtn.parentNode.replaceChild(newSuspendBtn, suspendBtn);
    
    const newDeleteBtn = deleteBtn.cloneNode(true);
    deleteBtn.parentNode.replaceChild(newDeleteBtn, deleteBtn);
    
    // Add new event listeners with explicit data parameters (no closures)
    newActivateBtn.addEventListener('click', function() {
        console.log('Activating driver:', driverId);
        updateUserStatus(driverId, 'activated');
    });
    
    newDeactivateBtn.addEventListener('click', function() {
        console.log('Deactivating driver:', driverId);
        updateUserStatus(driverId, 'deactivated');
    });
    
    newSuspendBtn.addEventListener('click', function() {
        console.log('Suspending driver:', driverId);
        updateUserStatus(driverId, 'suspended');
    });
    
    newDeleteBtn.addEventListener('click', function() {
        console.log('Deleting driver:', driverId);
        deleteUser(driverId);
    });
    
    console.log('Driver action buttons setup complete for driver:', driverId);
}

// Set up passenger action buttons
function setupPassengerActionButtons(passengerId) {
    // Get all action buttons
    const activateBtn = document.getElementById('passenger-activate-btn');
    const deactivateBtn = document.getElementById('passenger-deactivate-btn');
    const suspendBtn = document.getElementById('passenger-suspend-btn');
    const deleteBtn = document.getElementById('passenger-delete-btn');
    
    // Remove all existing event listeners by cloning the buttons
    const newActivateBtn = activateBtn.cloneNode(true);
    activateBtn.parentNode.replaceChild(newActivateBtn, activateBtn);
    
    const newDeactivateBtn = deactivateBtn.cloneNode(true);
    deactivateBtn.parentNode.replaceChild(newDeactivateBtn, deactivateBtn);
    
    const newSuspendBtn = suspendBtn.cloneNode(true);
    suspendBtn.parentNode.replaceChild(newSuspendBtn, suspendBtn);
    
    const newDeleteBtn = deleteBtn.cloneNode(true);
    deleteBtn.parentNode.replaceChild(newDeleteBtn, deleteBtn);
    
    // Add new event listeners with explicit data parameters
    newActivateBtn.addEventListener('click', function() {
        console.log('Activating passenger:', passengerId);
        updateUserStatus(passengerId, 'activated');
    });
    
    newDeactivateBtn.addEventListener('click', function() {
        console.log('Deactivating passenger:', passengerId);
        updateUserStatus(passengerId, 'deactivated');
    });
    
    newSuspendBtn.addEventListener('click', function() {
        console.log('Suspending passenger:', passengerId);
        updateUserStatus(passengerId, 'suspended');
    });
    
    newDeleteBtn.addEventListener('click', function() {
        console.log('Deleting passenger:', passengerId);
        deleteUser(passengerId);
    });
    
    console.log('Passenger action buttons setup complete for passenger:', passengerId);
}

// Update user status
function updateUserStatus(userId, status) {
    if (!confirm(`Are you sure you want to ${status} this user?`)) {
        return;
    }
    
    // Show loading indicator or disable buttons if needed
    const userRow = document.querySelector(`[data-user-id="${userId}"]`).closest('tr');
    if (userRow) {
        userRow.classList.add('opacity-50');
    }
    
    axios.patch(`/admin/user/${userId}/status`, {
        status: status
    })
    .then(function(response) {
        console.log('Update response:', response.data);
        
        if (response.data.success) {
            // Success handling
            
            // Don't show an alert - it disrupts the user experience
            // alert('User status updated successfully');
            
            // Remove loading state
            if (userRow) {
                userRow.classList.remove('opacity-50');
            }
            
            // Close any open modals
            closeDriverModal();
            closePassengerModal();
            
            // Reload the users list
            loadUsers();
        } else {
            // The request was processed but returned a failure status
            console.error('Server returned error:', response.data.message);
            alert(response.data.message || 'Error updating user status. Please try again.');
            
            // Remove loading state
            if (userRow) {
                userRow.classList.remove('opacity-50');
            }
        }
    })
    .catch(function(error) {
        console.error('Error updating user status:', error);
        
        // Extract the error message from the response if available
        let errorMessage = 'Error updating user status. Please try again.';
        if (error.response && error.response.data && error.response.data.message) {
            errorMessage = error.response.data.message;
        }
        
        alert(errorMessage);
        
        // Remove loading state
        if (userRow) {
            userRow.classList.remove('opacity-50');
        }
    });
}

// Delete user
function deleteUser(userId) {
    if (!confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        return;
    }
    
    axios.delete(`/admin/user/${userId}`)
    .then(function(response) {
        alert('User deleted successfully');
        
        // Close any open modals
        closeDriverModal();
        closePassengerModal();
        
        // Reload the users list
        loadUsers();
    })
    .catch(function(error) {
        console.error('Error deleting user:', error);
        alert('Error deleting user. Please try again.');
    });
}

// Initial setup
document.addEventListener('DOMContentLoaded', function() {
    // Attach initial event listeners
    attachEventListeners();
});
    </script>
</body>
</html>