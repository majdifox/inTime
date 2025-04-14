<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>inTime - Admin Dashboard</title>
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Flatpickr for date/time picker -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css">
</head>
<body class="bg-gray-50">
    <!-- Header/Navigation -->
    <header class="px-4 py-4 flex items-center justify-between bg-white shadow-sm">
        <div class="flex items-center space-x-8">
            <!-- Logo -->
            <a href="#" class="text-2xl font-bold">inTime</a>
            
            <!-- Navigation Links -->
            <nav class="hidden md:flex space-x-6">
                <a href="#" class="font-medium text-black">Users</a>
                <a href="#" class="font-medium">Drivers</a>
                <a href="#" class="font-medium">Passengers</a>
                <a href="#" class="font-medium">Rides</a>
                <a href="#" class="font-medium">Reports</a>
            </nav>
        </div>
        
        <!-- User Profile -->
        <div class="flex items-center space-x-4">
            <div class="h-10 w-10 rounded-full bg-gray-300 overflow-hidden">
                <img src="/api/placeholder/40/40" alt="Admin" class="h-full w-full object-cover">
            </div>
            <div class="hidden md:block">
                <p class="font-medium">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-500">Admin</p>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="text-sm text-red-500 hover:text-red-700">Logout</button>
            </form>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <div class="max-w-7xl mx-auto">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold">Admin Dashboard</h1>
                <div class="flex space-x-4">
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">{{ $stats['total_users'] ?? 0 }} Users</span>
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">{{ $stats['total_drivers'] ?? 0 }} Drivers</span>
                    <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm font-medium">{{ $stats['total_passengers'] ?? 0 }} Passengers</span>
                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium">{{ $stats['total_rides'] ?? 0 }} Rides</span>
                </div>
            </div>
            
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <!-- Users Card -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-medium">Total Users</h3>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div class="text-3xl font-bold mb-2">{{ $stats['total_users'] ?? 0 }}</div>
                    <div class="text-sm text-gray-500">
                        <span class="text-green-500">+{{ $stats['new_users_today'] ?? 0 }}</span> new today
                    </div>
                </div>
                
                <!-- Drivers Card -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-medium">Active Drivers</h3>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div class="text-3xl font-bold mb-2">{{ $stats['active_drivers'] ?? 0 }}</div>
                    <div class="text-sm text-gray-500">
                        <span class="text-green-500">{{ $stats['online_drivers'] ?? 0 }}</span> currently online
                    </div>
                </div>
                
                <!-- Rides Card -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-medium">Today's Rides</h3>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                    <div class="text-3xl font-bold mb-2">{{ $stats['today_rides'] ?? 0 }}</div>
                    <div class="text-sm text-gray-500">
                        <span class="text-yellow-500">{{ $stats['ongoing_rides'] ?? 0 }}</span> ongoing
                    </div>
                </div>
                
                <!-- Revenue Card -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-medium">Total Revenue</h3>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="text-3xl font-bold mb-2">${{ number_format($stats['total_revenue'] ?? 0, 2) }}</div>
                    <div class="text-sm text-gray-500">
                        <span class="text-green-500">${{ number_format($stats['today_revenue'] ?? 0, 2) }}</span> today
                    </div>
                </div>
            </div>
            
            <!-- Tabs -->
            <div class="mb-6 border-b border-gray-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <a href="#" class="px-1 py-4 border-b-2 border-black font-medium text-sm">
                        Users
                    </a>
                    <a href="#" class="px-1 py-4 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Drivers
                    </a>
                    <a href="#" class="px-1 py-4 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Passengers
                    </a>
                    <a href="#" class="px-1 py-4 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Rides
                    </a>
                </nav>
            </div>
            
            <!-- Users Table -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-lg font-medium">All Users</h2>
                    <div class="flex space-x-2">
                        <input type="text" placeholder="Search users..." class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <button class="bg-blue-500 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-600 transition">Add User</button>
                    </div>
                </div>
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
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($users as $user)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $user->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200 overflow-hidden">
                                            @if($user->profile_picture)
                                                <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
                                            @else
                                                <div class="h-full w-full flex items-center justify-center bg-gray-300 text-gray-600">
                                                    {{ substr($user->name, 0, 1) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $user->birthday ? $user->birthday->format('M d, Y') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($user->role == 'admin')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                            Admin
                                        </span>
                                    @elseif($user->role == 'driver')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Driver
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            Passenger
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $user->email }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $user->phone }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($user->account_status == 'activated')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    @elseif($user->account_status == 'pending')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Pending
                                        </span>
                                    @elseif($user->account_status == 'suspended')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Suspended
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ ucfirst($user->account_status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($user->role == 'driver' && isset($user->driver))
                                        ${{ number_format($user->driver->balance, 2) }}
                                    @else
                                        $0.00
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <!-- Edit Button -->
                                    <button class="text-blue-600 hover:text-blue-900" title="Edit" 
                                            onclick="openEditDriverModal({{ $driver->id }})">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    
                                    <!-- Status Change Buttons -->
                                    @if($driver->user->account_status == 'activated')
                                        <button class="text-yellow-600 hover:text-yellow-900" title="Suspend" 
                                                onclick="changeDriverStatus({{ $driver->id }}, 'suspended')">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </button>
                                        <button class="text-red-600 hover:text-red-900" title="Deactivate" 
                                                onclick="changeDriverStatus({{ $driver->id }}, 'deactivated')">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                            </svg>
                                        </button>
                                    @elseif($driver->user->account_status == 'pending' || $driver->user->account_status == 'deactivated' || $driver->user->account_status == 'suspended')
                                        <button class="text-green-600 hover:text-green-900" title="Activate" 
                                                onclick="changeDriverStatus({{ $driver->id }}, 'activated')">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                    @endif
                                    
                                    <!-- Delete Button -->
                                    <button class="text-red-600 hover:text-red-900" title="Delete" 
                                            onclick="deleteDriver({{ $driver->id }})">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                    
                                    <!-- View Documents Button -->
                                    <button class="text-indigo-600 hover:text-indigo-900" title="View Documents" 
                                            onclick="viewDriverDocuments({{ $driver->id }})">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </main>

    <!-- JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab switching functionality
            const tabs = document.querySelectorAll('nav[aria-label="Tabs"] a');
            tabs.forEach(tab => {
                tab.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Remove active class from all tabs
                    tabs.forEach(t => {
                        t.classList.remove('border-black');
                        t.classList.add('border-transparent', 'text-gray-500');
                    });
                    
                    // Add active class to clicked tab
                    this.classList.remove('border-transparent', 'text-gray-500');
                    this.classList.add('border-black');
                    
                    // Here you would typically handle content switching based on selected tab
                    // For a single file approach, you might use AJAX to load in content or 
                    // toggle visibility of pre-rendered sections
                });
            });
        });
        // Function to handle status changes
function changeDriverStatus(driverId, status) {
    if (confirm('Are you sure you want to change this driver\'s status?')) {
        axios.post('/admin/drivers/' + driverId + '/status', {
            status: status
        })
        .then(function (response) {
            toastr.success('Driver status updated successfully!');
            // Reload the page or update the UI
            window.location.reload();
        })
        .catch(function (error) {
            toastr.error('Error updating driver status');
            console.error(error);
        });
    }
}

// Function to handle driver deletion
function deleteDriver(driverId) {
    if (confirm('Are you sure you want to delete this driver? This action cannot be undone.')) {
        axios.delete('/admin/drivers/' + driverId)
        .then(function (response) {
            toastr.success('Driver deleted successfully!');
            // Reload the page or update the UI
            window.location.reload();
        })
        .catch(function (error) {
            toastr.error('Error deleting driver');
            console.error(error);
        });
    }
}

// Function to open edit modal
function openEditDriverModal(driverId) {
    // Fetch driver data
    axios.get('/admin/drivers/' + driverId)
        .then(function (response) {
            // Populate your modal with driver data
            const driver = response.data.driver;
            document.getElementById('edit-driver-id').value = driver.id;
            document.getElementById('edit-driver-name').value = driver.user.name;
            document.getElementById('edit-driver-email').value = driver.user.email;
            document.getElementById('edit-driver-phone').value = driver.user.phone;
            document.getElementById('edit-license-number').value = driver.license_number;
            document.getElementById('edit-license-expiry').value = driver.license_expiry;
            
            // Show the modal
            $('#editDriverModal').modal('show');
        })
        .catch(function (error) {
            toastr.error('Error fetching driver data');
            console.error(error);
        });
}

// Function to view driver documents
function viewDriverDocuments(driverId) {
    axios.get('/admin/drivers/' + driverId + '/documents')
        .then(function (response) {
            // Populate document modal with images and data
            const driver = response.data.driver;
            const vehicle = response.data.vehicle;
            
            // Set driver license image
            document.getElementById('license-image').src = driver.license_photo 
                ? '/storage/' + driver.license_photo 
                : '/images/no-document.png';
                
            // Set vehicle image
            document.getElementById('vehicle-image').src = vehicle.vehicle_photo 
                ? '/storage/' + vehicle.vehicle_photo 
                : '/images/no-document.png';
                
            // Set driver details
            document.getElementById('driver-name').textContent = driver.user.name;
            document.getElementById('license-number').textContent = driver.license_number;
            document.getElementById('license-expiry').textContent = driver.license_expiry;
            
            // Set vehicle details
            document.getElementById('vehicle-make').textContent = vehicle.make;
            document.getElementById('vehicle-model').textContent = vehicle.model;
            document.getElementById('vehicle-year').textContent = vehicle.year;
            document.getElementById('vehicle-plate').textContent = vehicle.plate_number;
            
            // Show the modal
            $('#viewDocumentsModal').modal('show');
        })
        .catch(function (error) {
            toastr.error('Error fetching driver documents');
            console.error(error);
        });
}
    </script>
</body>
</html>