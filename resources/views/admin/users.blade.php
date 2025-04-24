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
    <main class="container mx-auto px-8 py-8">
        <div class="max-w-8xl mx-auto">
            <!-- Status Messages -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <h1 class="text-2xl font-bold mb-6">Users Management</h1>
            
            <!-- Tabs Navigation -->
            <div class="border-b border-gray-200 mb-6">
                <ul class="flex flex-wrap -mb-px">
                    <li class="mr-2">
                        <a href="#drivers-tab" id="drivers-tab-link" class="inline-block py-2 px-4 text-sm font-medium text-center text-gray-900 border-b-2 border-black active">
                            Drivers
                        </a>
                    </li>
                    <li class="mr-2">
                        <a href="#passengers-tab" id="passengers-tab-link" class="inline-block py-2 px-4 text-sm font-medium text-center text-gray-500 border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300">
                            Passengers
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- Drivers Tab Content -->
            <div id="drivers-tab" class="tab-content">
                <!-- Search and Filter Bar -->
                <div class="mb-6 flex flex-col md:flex-row gap-4 justify-between">
                    <!-- Search box -->
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                            </svg>
                        </div>
                        <input type="text" id="drivers-search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full pl-10 p-2.5" placeholder="Search by name, email or phone">
                    </div>
                    
                    <!-- Status Filter -->
                    <div class="w-full md:w-48">
                        <select id="drivers-status-filter" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            <option value="">All Statuses</option>
                            <option value="activated">Activated</option>
                            <option value="deactivated">Deactivated</option>
                            <option value="pending">Pending</option>
                            <option value="suspended">Suspended</option>
                        </select>
                    </div>
                </div>
                
                <!-- Drivers Table -->
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg mb-14">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3">Created At</th>
                                <th scope="col" class="px-6 py-3">Photo</th>
                                <th scope="col" class="px-6 py-3">User Name</th>
                                <th scope="col" class="px-6 py-3">Birthday</th>
                                <th scope="col" class="px-6 py-3">Email</th>
                                <th scope="col" class="px-6 py-3">Phone</th>
                                <th scope="col" class="px-6 py-3">Status</th>
                                <th scope="col" class="px-6 py-3">Verification</th>
                                <th scope="col" class="px-6 py-3">Balance</th>
                                <th scope="col" class="px-6 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="drivers-table-body">
                            @foreach($drivers as $driver)
                            <tr class="bg-white border-b hover:bg-gray-50">
                                <td class="px-6 py-4">{{ $driver->created_at->format('Y-m-d') }}</td>
                                <td class="px-6 py-4">
                                    <div class="h-10 w-10 rounded-full bg-gray-200 overflow-hidden">
                                        @if($driver->profile_picture)
                                            <img src="{{ Storage::url($driver->profile_picture) }}" alt="Profile" class="h-full w-full object-cover">
                                        @else
                                            <img src="/api/placeholder/40/40" alt="Profile" class="h-full w-full object-cover">
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $driver->name }}</td>
                                <td class="px-6 py-4">{{ $driver->birthday ? date('Y-m-d', strtotime($driver->birthday)) : 'N/A' }}</td>
                                <td class="px-6 py-4">{{ $driver->email }}</td>
                                <td class="px-6 py-4">{{ $driver->phone }}</td>
                                <td class="px-6 py-4">
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
                                <td class="px-6 py-4">
                                    @if($driver->driver && $driver->driver->is_verified)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Verified
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Not Verified
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    DH {{ $driver->driver ? number_format($driver->driver->balance, 2) : '0.00' }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.driver.show', $driver->id) }}" class="font-medium text-blue-600 hover:underline">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <div class="relative" x-data="{ open: false }">
                                            <button @click="open = !open" class="font-medium text-gray-600 hover:text-gray-900">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                                </svg>
                                            </button>
                                            <div x-show="open" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                                <div class="py-1">
                                                    <a href="{{ route('admin.user.status', ['id' => $driver->id, 'status' => 'activated']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Activate</a>
                                                    <a href="{{ route('admin.user.status', ['id' => $driver->id, 'status' => 'deactivated']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Deactivate</a>
                                                    <a href="{{ route('admin.user.status', ['id' => $driver->id, 'status' => 'suspended']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Suspend</a>
                                                    <a href="{{ route('admin.user.delete', ['id' => $driver->id]) }}" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100" onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">Delete</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="mt-4" id="drivers-pagination">
                    {{ $drivers->links() }}
                </div>
            </div>
            
            <!-- Passengers Tab Content (Hidden by default) -->
            <div id="passengers-tab" class="tab-content hidden">
                <!-- Search and Filter Bar -->
                <div class="mb-6 flex flex-col md:flex-row gap-4 justify-between">
                    <!-- Search box -->
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                            </svg>
                        </div>
                        <input type="text" id="passengers-search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full pl-10 p-2.5" placeholder="Search by name, email or phone">
                    </div>
                    
                    <!-- Status Filter -->
                    <div class="w-full md:w-48">
                        <select id="passengers-status-filter" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            <option value="">All Statuses</option>
                            <option value="activated">Activated</option>
                            <option value="deactivated">Deactivated</option>
                            <option value="pending">Pending</option>
                            <option value="suspended">Suspended</option>
                        </select>
                    </div>
                </div>
                
                <!-- Passengers Table -->
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg mb-8">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3">Created At</th>
                                <th scope="col" class="px-6 py-3">Photo</th>
                                <th scope="col" class="px-6 py-3">User Name</th>
                                <th scope="col" class="px-6 py-3">Birthday</th>
                                <th scope="col" class="px-6 py-3">Email</th>
                                <th scope="col" class="px-6 py-3">Phone</th>
                                <th scope="col" class="px-6 py-3">Status</th>
                                <th scope="col" class="px-6 py-3">Total Rides</th>
                                <th scope="col" class="px-6 py-3">Rating</th>
                                <th scope="col" class="px-6 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="passengers-table-body">
                            @foreach($passengers as $passenger)
                            <tr class="bg-white border-b hover:bg-gray-50">
                                <td class="px-6 py-4">{{ $passenger->created_at->format('Y-m-d') }}</td>
                                <td class="px-6 py-4">
                                    <div class="h-10 w-10 rounded-full bg-gray-200 overflow-hidden">
                                        @if($passenger->profile_picture)
                                            <img src="{{ Storage::url($passenger->profile_picture) }}" alt="Profile" class="h-full w-full object-cover">
                                        @else
                                            <img src="/api/placeholder/40/40" alt="Profile" class="h-full w-full object-cover">
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $passenger->name }}</td>
                                <td class="px-6 py-4">{{ $passenger->birthday ? date('Y-m-d', strtotime($passenger->birthday)) : 'N/A' }}</td>
                                <td class="px-6 py-4">{{ $passenger->email }}</td>
                                <td class="px-6 py-4">{{ $passenger->phone }}</td>
                                <td class="px-6 py-4">
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
                                <td class="px-6 py-4">
                                    {{ $passenger->passenger ? ($passenger->passenger->total_rides ?? 0) : 0 }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        <span class="ml-1">{{ $passenger->passenger && $passenger->passenger->rating ? number_format($passenger->passenger->rating, 1) : 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.passenger.show', $passenger->id) }}" class="font-medium text-blue-600 hover:underline">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <div class="relative" x-data="{ open: false }">
                                            <button @click="open = !open" class="font-medium text-gray-600 hover:text-gray-900">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                                </svg>
                                            </button>
                                            <div x-show="open" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                                <div class="py-1">
                                                    <a href="{{ route('admin.user.status', ['id' => $passenger->id, 'status' => 'activated']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Activate</a>
                                                    <a href="{{ route('admin.user.status', ['id' => $passenger->id, 'status' => 'deactivated']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Deactivate</a>
                                                    <a href="{{ route('admin.user.status', ['id' => $passenger->id, 'status' => 'suspended']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Suspend</a>
                                                    <a href="{{ route('admin.user.delete', ['id' => $passenger->id]) }}" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100" onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">Delete</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="mt-4" id="passengers-pagination">
                    {{ $passengers->links() }}
                </div>
            </div>
        </div>
    </main>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab switching
            const driversTabLink = document.getElementById('drivers-tab-link');
            const passengersTabLink = document.getElementById('passengers-tab-link');
            const driversTab = document.getElementById('drivers-tab');
            const passengersTab = document.getElementById('passengers-tab');

            driversTabLink.addEventListener('click', function(e) {
                e.preventDefault();
                // Show drivers tab, hide passengers tab
                driversTab.classList.remove('hidden');
                passengersTab.classList.add('hidden');
                
                // Update active tab styling
                driversTabLink.classList.add('text-gray-900', 'border-black');
                driversTabLink.classList.remove('text-gray-500', 'border-transparent');
                passengersTabLink.classList.add('text-gray-500', 'border-transparent');
                passengersTabLink.classList.remove('text-gray-900', 'border-black');
            });

            passengersTabLink.addEventListener('click', function(e) {
                e.preventDefault();
                // Show passengers tab, hide drivers tab
                passengersTab.classList.remove('hidden');
                driversTab.classList.add('hidden');
                
                // Update active tab styling
                passengersTabLink.classList.add('text-gray-900', 'border-black');
                passengersTabLink.classList.remove('text-gray-500', 'border-transparent');
                driversTabLink.classList.add('text-gray-500', 'border-transparent');
                driversTabLink.classList.remove('text-gray-900', 'border-black');
            });

            // Drivers search functionality
            const driversSearch = document.getElementById('drivers-search');
            const driversStatusFilter = document.getElementById('drivers-status-filter');
            
            function filterDrivers() {
                const searchTerm = driversSearch.value.toLowerCase();
                const statusFilter = driversStatusFilter.value;
                
                fetch(`{{ route('admin.drivers') }}?search=${searchTerm}&status=${statusFilter}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('drivers-table-body').innerHTML = data.html;
                        document.getElementById('drivers-pagination').innerHTML = data.pagination;
                    }
                })
                .catch(error => console.error('Error:', error));
            }
            
            driversSearch.addEventListener('input', debounce(filterDrivers, 300));
            driversStatusFilter.addEventListener('change', filterDrivers);
            
            // Passengers search functionality
            const passengersSearch = document.getElementById('passengers-search');
            const passengersStatusFilter = document.getElementById('passengers-status-filter');
            
            function filterPassengers() {
                const searchTerm = passengersSearch.value.toLowerCase();
                const statusFilter = passengersStatusFilter.value;
                
                fetch(`{{ route('admin.passengers') }}?search=${searchTerm}&status=${statusFilter}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('passengers-table-body').innerHTML = data.html;
                        document.getElementById('passengers-pagination').innerHTML = data.pagination;
                    }
                })
                .catch(error => console.error('Error:', error));
            }
            
            passengersSearch.addEventListener('input', debounce(filterPassengers, 300));
            passengersStatusFilter.addEventListener('change', filterPassengers);
            
            // Helper function to debounce search input
            function debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }
        });
    </script>
</body>
</html>