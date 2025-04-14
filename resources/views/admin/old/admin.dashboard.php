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
                                    <!-- User Actions Buttons Template -->
                                <div class="flex items-center space-x-2" data-user-id="{{ $user->id }}">
                                    <!-- View Details Icon -->
                                                                        <button 
                                            onclick="updateUserStatus(currentDriverId, 'activated')"class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                            Activate
                                        </button>
                                        <button 
                                            onclick="updateUserStatus(currentDriverId, 'deactivated')" 
                                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                        >
                                            Deactivate
                                        </button>
                                        <button 
                                            onclick="updateUserStatus(currentDriverId, 'suspended')" 
                                            class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500"
                                        >
                                            Suspend
                                        </button>
                                        <button 
                                            onclick="if(confirm('Are you sure you want to mark this user as deleted?')) updateUserStatus(currentDriverId, 'deleted')" 
                                            class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                                        >
                                            Delete
                                        </button>
                                        
                                        <!-- Verification Button -->
                                        <button 
                                            id="driver-verify-button"
                                            onclick="toggleDriverVerification(currentDriverId, document.getElementById('driver-verification-status').textContent.trim() !== 'Verified')" 
                                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                        >
                                            Verify Driver
                                        </button>
                                </div>
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
        // Global variables to store currently selected user IDs
let currentDriverId = null;
let currentPassengerId = null;

// Function to open driver modal and load data
function openDriverModal(driverId) {
    currentDriverId = driverId;
    
    // Show modal
    const modal = document.getElementById('driver-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    // Fetch driver data
    fetch(`/admin/drivers/${driverId}/details`)
        .then(response => response.json())
        .then(data => {
            // Populate user information
            document.getElementById('driver-name').textContent = data.user.name;
            document.getElementById('driver-email').textContent = data.user.email;
            document.getElementById('driver-phone').textContent = data.user.phone;
            document.getElementById('driver-birthday').textContent = formatDate(data.user.birthday);
            document.getElementById('driver-joined-date').textContent = formatDate(data.user.created_at);
            
            // Set status badge
            const statusBadge = document.getElementById('driver-status');
            statusBadge.textContent = capitalizeFirstLetter(data.user.account_status);
            updateStatusBadgeColor(statusBadge, data.user.account_status);
            
            // Set profile picture
            if (data.user.profile_picture) {
                document.getElementById('driver-profile-picture').src = data.user.profile_picture;
            }
            
            // Populate driver-specific info
            document.getElementById('driver-rating').textContent = data.rating || 'N/A';
            document.getElementById('driver-completed-rides').textContent = data.completed_rides;
            document.getElementById('driver-balance').textContent = `DH ${data.balance}`;
            
            // Populate license information
            document.getElementById('driver-license-number').textContent = data.license_number;
            document.getElementById('driver-license-expiry').textContent = formatDate(data.license_expiry);
            
            // Set verification status badge
            const verificationBadge = document.getElementById('driver-verification-status');
            verificationBadge.textContent = data.is_verified ? 'Verified' : 'Not Verified';
            updateStatusBadgeColor(verificationBadge, data.is_verified ? 'verified' : 'not-verified');
            
            // Set license photo
            if (data.license_photo) {
                document.getElementById('driver-license-photo').src = data.license_photo;
            }
            
            // Populate vehicle information if available
            if (data.vehicle) {
                document.getElementById('vehicle-make-model').textContent = `${data.vehicle.make} ${data.vehicle.model}`;
                document.getElementById('vehicle-year').textContent = data.vehicle.year;
                document.getElementById('vehicle-color').textContent = data.vehicle.color;
                document.getElementById('vehicle-plate').textContent = data.vehicle.plate_number;
                document.getElementById('vehicle-type').textContent = capitalizeFirstLetter(data.vehicle.type);
                document.getElementById('vehicle-capacity').textContent = `${data.vehicle.capacity} passengers`;
                document.getElementById('vehicle-insurance-expiry').textContent = formatDate(data.vehicle.insurance_expiry);
                document.getElementById('vehicle-registration-expiry').textContent = formatDate(data.vehicle.registration_expiry);
                
                // Set vehicle status badge
                const vehicleStatusBadge = document.getElementById('vehicle-status');
                vehicleStatusBadge.textContent = data.vehicle.is_active ? 'Active' : 'Inactive';
                updateStatusBadgeColor(vehicleStatusBadge, data.vehicle.is_active ? 'active' : 'inactive');
                
                // Set vehicle photo
                if (data.vehicle.vehicle_photo) {
                    document.getElementById('vehicle-photo').src = data.vehicle.vehicle_photo;
                }
            }
        })
        .catch(error => {
            console.error('Error fetching driver details:', error);
            alert('Failed to load driver details. Please try again.');
        });
}

// Function to open passenger modal and load data
function openPassengerModal(passengerId) {
    currentPassengerId = passengerId;
    
    // Show modal
    const modal = document.getElementById('passenger-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    // Fetch passenger data
    fetch(`/admin/passengers/${passengerId}/details`)
        .then(response => response.json())
        .then(data => {
            // Populate user information
            document.getElementById('passenger-name').textContent = data.user.name;
            document.getElementById('passenger-email').textContent = data.user.email;
            document.getElementById('passenger-phone').textContent = data.user.phone;
            document.getElementById('passenger-birthday').textContent = formatDate(data.user.birthday);
            document.getElementById('passenger-joined-date').textContent = formatDate(data.user.created_at);
            
            // Set status badge
            const statusBadge = document.getElementById('passenger-status');
            statusBadge.textContent = capitalizeFirstLetter(data.user.account_status);
            updateStatusBadgeColor(statusBadge, data.user.account_status);
            
            // Set profile picture
            if (data.user.profile_picture) {
                document.getElementById('passenger-profile-picture').src = data.user.profile_picture;
            }
            
            // Populate passenger-specific info
            document.getElementById('passenger-rating').textContent = data.rating || 'N/A';
            document.getElementById('passenger-total-rides').textContent = data.total_rides;
            
            // Populate passenger preferences if available
            if (data.preferences) {
                const preferencesContainer = document.getElementById('passenger-preferences');
                // Parse preferences if stored as JSON, or use as-is if already HTML
                try {
                    const preferences = JSON.parse(data.preferences);
                    let preferencesHTML = '<ul>';
                    
                    if (Array.isArray(preferences)) {
                        preferences.forEach(pref => {
                            preferencesHTML += `<li>${pref}</li>`;
                        });
                    } else {
                        for (const key in preferences) {
                            preferencesHTML += `<li>${key}: ${preferences[key]}</li>`;
                        }
                    }
                    
                    preferencesHTML += '</ul>';
                    preferencesContainer.innerHTML = preferencesHTML;
                } catch (e) {
                    // If not JSON, use as-is
                    preferencesContainer.innerHTML = data.preferences;
                }
            } else {
                document.getElementById('passenger-preferences').innerHTML = '<p>No preferences set</p>';
            }
        })
        .catch(error => {
            console.error('Error fetching passenger details:', error);
            alert('Failed to load passenger details. Please try again.');
        });
}

// Function to close driver modal
function closeDriverModal() {
    const modal = document.getElementById('driver-modal');
    modal.classList.remove('flex');
    modal.classList.add('hidden');
    currentDriverId = null;
}

// Function to close passenger modal
function closePassengerModal() {
    const modal = document.getElementById('passenger-modal');
    modal.classList.remove('flex');
    modal.classList.add('hidden');
    currentPassengerId = null;
}

// Function to update user status
function updateUserStatus(userId, status) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch(`/admin/users/${userId}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update UI to reflect the new status
            const statusElements = document.querySelectorAll(`[data-user-id="${userId}"] .status-badge`);
            statusElements.forEach(element => {
                element.textContent = capitalizeFirstLetter(status);
                updateStatusBadgeColor(element, status);
            });
            
            // If a modal is open, update the status badge in the modal
            if (currentDriverId && document.getElementById('driver-modal').classList.contains('flex')) {
                const driverStatusBadge = document.getElementById('driver-status');
                driverStatusBadge.textContent = capitalizeFirstLetter(status);
                updateStatusBadgeColor(driverStatusBadge, status);
            } else if (currentPassengerId && document.getElementById('passenger-modal').classList.contains('flex')) {
                const passengerStatusBadge = document.getElementById('passenger-status');
                passengerStatusBadge.textContent = capitalizeFirstLetter(status);
                updateStatusBadgeColor(passengerStatusBadge, status);
            }
            
            // Show success message
            showNotification(data.message, 'success');
        } else {
            showNotification('Failed to update user status', 'error');
        }
    })
    .catch(error => {
        console.error('Error updating user status:', error);
        showNotification('An error occurred while updating user status', 'error');
    });
}

// Function to verify/unverify driver
function toggleDriverVerification(driverId, isVerified) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch(`/admin/drivers/${driverId}/verify`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ is_verified: isVerified })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update UI to reflect the new verification status
            if (currentDriverId && document.getElementById('driver-modal').classList.contains('flex')) {
                const verificationBadge = document.getElementById('driver-verification-status');
                verificationBadge.textContent = isVerified ? 'Verified' : 'Not Verified';
                updateStatusBadgeColor(verificationBadge, isVerified ? 'verified' : 'not-verified');
            }
            
            // Show success message
            showNotification(data.message, 'success');
        } else {
            showNotification('Failed to update verification status', 'error');
        }
    })
    .catch(error => {
        console.error('Error updating driver verification:', error);
        showNotification('An error occurred while updating verification status', 'error');
    });
}

// Function to toggle vehicle status
function toggleVehicleStatus(vehicleId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch(`/admin/vehicles/${vehicleId}/toggle-status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update UI to reflect the new vehicle status
            if (currentDriverId && document.getElementById('driver-modal').classList.contains('flex')) {
                const vehicleStatusBadge = document.getElementById('vehicle-status');
                vehicleStatusBadge.textContent = data.vehicle.is_active ? 'Active' : 'Inactive';
                updateStatusBadgeColor(vehicleStatusBadge, data.vehicle.is_active ? 'active' : 'inactive');
            }
            
            // Show success message
            showNotification(data.message, 'success');
        } else {
            showNotification('Failed to update vehicle status', 'error');
        }
    })
    .catch(error => {
        console.error('Error toggling vehicle status:', error);
        showNotification('An error occurred while updating vehicle status', 'error');
    });
}

// Helper function to format date
function formatDate(dateString) {
    if (!dateString) return 'N/A';
    
    const date = new Date(dateString);
    const day = date.getDate().toString().padStart(2, '0');
    const month = (date.getMonth() + 1).toString().padStart(2, '0');
    const year = date.getFullYear();
    
    return `${day}/${month}/${year}`;
}

// Helper function to capitalize first letter
function capitalizeFirstLetter(string) {
    if (!string) return '';
    return string.charAt(0).toUpperCase() + string.slice(1);
}

// Helper function to update status badge color
function updateStatusBadgeColor(element, status) {
    // Remove all existing status classes
    element.classList.remove(
        'bg-green-100', 'text-green-800',
        'bg-red-100', 'text-red-800',
        'bg-yellow-100', 'text-yellow-800',
        'bg-gray-100', 'text-gray-800',
        'bg-blue-100', 'text-blue-800'
    );
    
    // Add appropriate status classes
    switch (status) {
        case 'activated':
        case 'active':
        case 'verified':
            element.classList.add('bg-green-100', 'text-green-800');
            break;
        case 'deactivated':
        case 'inactive':
        case 'not-verified':
            element.classList.add('bg-red-100', 'text-red-800');
            break;
        case 'suspended':
            element.classList.add('bg-yellow-100', 'text-yellow-800');
            break;
        case 'deleted':
            element.classList.add('bg-gray-100', 'text-gray-800');
            break;
        case 'pending':
            element.classList.add('bg-blue-100', 'text-blue-800');
            break;
        default:
            element.classList.add('bg-gray-100', 'text-gray-800');
    }
}

// Helper function to show notifications
function showNotification(message, type = 'success') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-3 rounded-md text-white z-50 ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    }`;
    notification.textContent = message;
    
    // Add to document
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Attach event listeners to action buttons in driver modal
document.addEventListener('DOMContentLoaded', function() {
    // Driver modal action buttons
    const driverModal = document.getElementById('driver-modal');
    if (driverModal) {
        const actionButtons = driverModal.querySelectorAll('button');
        
        actionButtons.forEach(button => {
            button.addEventListener('click', function() {
                if (!currentDriverId) return;
                
                const buttonText = this.textContent.trim().toLowerCase();
                
                // Handle different actions
                if (buttonText === 'activate') {
                    updateUserStatus(currentDriverId, 'activated');
                } else if (buttonText === 'deactivate') {
                    updateUserStatus(currentDriverId, 'deactivated');
                } else if (buttonText === 'suspend') {
                    updateUserStatus(currentDriverId, 'suspended');
                } else if (buttonText === 'delete') {
                    if (confirm('Are you sure you want to mark this user as deleted? This action can be reversed.')) {
                        updateUserStatus(currentDriverId, 'deleted');
                    }
                }
            });
        });
    }
    
    // Passenger modal action buttons
    const passengerModal = document.getElementById('passenger-modal');
    if (passengerModal) {
        const actionButtons = passengerModal.querySelectorAll('button');
        
        actionButtons.forEach(button => {
            button.addEventListener('click', function() {
                if (!currentPassengerId) return;
                
                const buttonText = this.textContent.trim().toLowerCase();
                
                // Handle different actions
                if (buttonText === 'activate') {
                    updateUserStatus(currentPassengerId, 'activated');
                } else if (buttonText === 'deactivate') {
                    updateUserStatus(currentPassengerId, 'deactivated');
                } else if (buttonText === 'suspend') {
                    updateUserStatus(currentPassengerId, 'suspended');
                } else if (buttonText === 'delete') {
                    if (confirm('Are you sure you want to mark this user as deleted? This action can be reversed.')) {
                        updateUserStatus(currentPassengerId, 'deleted');
                    }
                }
            });
        });
    }
});
    </script>
</body>
</html>