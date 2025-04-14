<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>inTime - Pending Rides</title>
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
                <a href="#" class="font-medium {{ request()->routeIs('admin.rides.*') ? 'text-black' : 'text-gray-500 hover:text-black' }}">Rides Management</a>
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
                <h1 class="text-2xl font-bold">Pending Rides</h1>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Back to Dashboard
                    </a>
                </div>
            </div>
            
            <!-- Rides Table -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Reservation Date
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Passenger
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Driver
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Pickup Location
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Dropoff Location
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($pendingRides as $ride)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $ride->reservation_date->format('Y-m-d H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-8 w-8 flex-shrink-0 mr-3">
                                                @if($ride->passenger->user->profile_picture)
                                                    <img class="h-8 w-8 rounded-full" src="{{ Storage::url($ride->passenger->user->profile_picture) }}" alt="">
                                                @else
                                                    <img class="h-8 w-8 rounded-full" src="/api/placeholder/32/32" alt="">
                                                @endif
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $ride->passenger->user->name }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $ride->passenger->user->phone }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-8 w-8 flex-shrink-0 mr-3">
                                                @if($ride->driver->user->profile_picture)
                                                    <img class="h-8 w-8 rounded-full" src="{{ Storage::url($ride->driver->user->profile_picture) }}" alt="">
                                                @else
                                                    <img class="h-8 w-8 rounded-full" src="/api/placeholder/32/32" alt="">
                                                @endif
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $ride->driver->user->name }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $ride->driver->user->phone }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $ride->pickup_location }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $ride->dropoff_location }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            {{ ucfirst($ride->reservation_status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <button class="text-green-600 hover:text-green-900" title="Approve" onclick="updateRideStatus({{ $ride->id }}, 'accepted')">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </button>
                                            <button class="text-red-600 hover:text-red-900" title="Reject" onclick="updateRideStatus({{ $ride->id }}, 'not_accepted')">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                            <button class="text-blue-600 hover:text-blue-900" title="View Details" onclick="viewRideDetails({{ $ride->id }})">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        No pending rides found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="px-6 py-3 flex items-center justify-between border-t border-gray-200">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <a href="{{ $pendingRides->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 {{ $pendingRides->onFirstPage() ? 'opacity-50 cursor-not-allowed' : '' }}">
                            Previous
                        </a>
                        <a href="{{ $pendingRides->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 {{ !$pendingRides->hasMorePages() ? 'opacity-50 cursor-not-allowed' : '' }}">
                            Next
                        </a>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Showing <span class="font-medium">{{ $pendingRides->firstItem() ?? 0 }}</span> to <span class="font-medium">{{ $pendingRides->lastItem() ?? 0 }}</span> of <span class="font-medium">{{ $pendingRides->total() }}</span> results
                            </p>
                        </div>
                        <div>
                            {{ $pendingRides->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Ride Details Modal -->
    <div id="ride-details-modal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-screen overflow-y-auto">
            <div class="sticky top-0 bg-white px-6 py-4 border-b flex items-center justify-between">
                <h2 class="text-xl font-bold">Ride Details</h2>
                <button onclick="closeRideModal()" class="text-gray-400 hover:text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="px-6 py-4">
                <!-- Ride Info -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-4">Ride Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Reservation Date</p>
                            <p id="ride-reservation-date" class="font-medium">Loading...</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Status</p>
                            <p id="ride-status" class="font-medium">Loading...</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Pickup Location</p>
                            <p id="ride-pickup" class="font-medium">Loading...</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Dropoff Location</p>
                            <p id="ride-dropoff" class="font-medium">Loading...</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Estimated Cost</p>
                            <p id="ride-cost" class="font-medium">Loading...</p>
                        </div>
                    </div>
                </div>
                
                <!-- Passenger Info -->
                <div class="mb-6 border-t pt-6">
                    <h3 class="text-lg font-semibold mb-4">Passenger Information</h3>
                    <div class="flex items-center mb-4">
                        <div class="h-10 w-10 flex-shrink-0 mr-3">
                            <img id="passenger-image" class="h-10 w-10 rounded-full" src="/api/placeholder/40/40" alt="">
                        </div>
                        <div>
                            <p id="passenger-name" class="font-medium">Loading...</p>
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                <span id="passenger-rating" class="text-xs text-gray-500 ml-1">Loading...</span>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Phone</p>
                            <p id="passenger-phone" class="font-medium">Loading...</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Email</p>
                            <p id="passenger-email" class="font-medium">Loading...</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Total Rides</p>
                            <p id="passenger-rides" class="font-medium">Loading...</p>
                        </div>
                    </div>
                </div>
                
                <!-- Driver Info -->
                <div class="mb-6 border-t pt-6">
                    <h3 class="text-lg font-semibold mb-4">Driver Information</h3>
                    <div class="flex items-center mb-4">
                        <div class="h-10 w-10 flex-shrink-0 mr-3">
                            <img id="driver-image" class="h-10 w-10 rounded-full" src="/api/placeholder/40/40" alt="">
                        </div>
                        <div>
                            <p id="driver-name" class="font-medium">Loading...</p>
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                <span id="driver-rating" class="text-xs text-gray-500 ml-1">Loading...</span>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Phone</p>
                            <p id="driver-phone" class="font-medium">Loading...</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Vehicle</p>
                            <p id="driver-vehicle" class="font-medium">Loading...</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">License Plate</p>
                            <p id="driver-plate" class="font-medium">Loading...</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Completed Rides</p>
                            <p id="driver-rides" class="font-medium">Loading...</p>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="mt-6 flex justify-end space-x-3 border-t pt-6">
                    <button id="approve-ride-btn" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Approve Ride
                    </button>
                    <button id="reject-ride-btn" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Reject Ride
                    </button>
                    <button id="close-modal-btn" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400">
                        Close
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
        
        let currentRideId = null;

        // Function to view ride details
        function viewRideDetails(rideId) {
            currentRideId = rideId;
            
            // Show the modal
            document.getElementById('ride-details-modal').classList.remove('hidden');
            document.getElementById('ride-details-modal').classList.add('flex');
            
            // Fetch ride details
            axios.get(`/admin/rides/${rideId}`)
                .then(function(response) {
                    const ride = response.data.ride;
                    
                    // Populate ride info
                    document.getElementById('ride-reservation-date').textContent = new Date(ride.reservation_date).toLocaleString();
                    
                    const statusElement = document.getElementById('ride-status');
                    statusElement.textContent = ride.reservation_status.charAt(0).toUpperCase() + ride.reservation_status.slice(1).replace('_', ' ');
                    
                    document.getElementById('ride-pickup').textContent = ride.pickup_location;
                    document.getElementById('ride-dropoff').textContent = ride.dropoff_location;
                    document.getElementById('ride-cost').textContent = ride.ride_cost ? `DH ${parseFloat(ride.ride_cost).toFixed(2)}` : 'Not yet calculated';
                    
                    // Populate passenger info
                    const passenger = ride.passenger.user;
                    document.getElementById('passenger-name').textContent = passenger.name;
                    document.getElementById('passenger-phone').textContent = passenger.phone;
                    document.getElementById('passenger-email').textContent = passenger.email;
                    document.getElementById('passenger-rating').textContent = ride.passenger.rating ? ride.passenger.rating.toFixed(1) : 'N/A';
                    document.getElementById('passenger-rides').textContent = ride.passenger.total_rides;
                    
                    if (passenger.profile_picture) {
                        document.getElementById('passenger-image').src = passenger.profile_picture;
                    }
                    
                    // Populate driver info
                    const driver = ride.driver.user;
                    document.getElementById('driver-name').textContent = driver.name;
                    document.getElementById('driver-phone').textContent = driver.phone;
                    document.getElementById('driver-rating').textContent = ride.driver.rating ? ride.driver.rating.toFixed(1) : 'N/A';
                    document.getElementById('driver-rides').textContent = ride.driver.completed_rides;
                    
                    if (driver.profile_picture) {
                        document.getElementById('driver-image').src = driver.profile_picture;
                    }
                    
                    // Vehicle info
                    if (ride.driver.vehicle) {
                        const vehicle = ride.driver.vehicle;
                        document.getElementById('driver-vehicle').textContent = `${vehicle.make} ${vehicle.model} (${vehicle.color})`;
                        document.getElementById('driver-plate').textContent = vehicle.plate_number;
                    } else {
                        document.getElementById('driver-vehicle').textContent = 'N/A';
                        document.getElementById('driver-plate').textContent = 'N/A';
                    }
                    
                    // Set up action buttons
                    document.getElementById('approve-ride-btn').onclick = function() {
                        updateRideStatus(rideId, 'accepted');
                    };
                    
                    document.getElementById('reject-ride-btn').onclick = function() {
                        updateRideStatus(rideId, 'not_accepted');
                    };
                    
                    document.getElementById('close-modal-btn').onclick = closeRideModal;
                })
                .catch(function(error) {
                    console.error('Error fetching ride details:', error);
                    alert('Error loading ride details. Please try again.');
                    closeRideModal();
                });
        }

        // Function to close the ride modal
        function closeRideModal() {
            document.getElementById('ride-details-modal').classList.add('hidden');
            document.getElementById('ride-details-modal').classList.remove('flex');
            currentRideId = null;
        }

        // Function to update ride status
        function updateRideStatus(rideId, status) {
            if (!confirm(`Are you sure you want to ${status === 'accepted' ? 'approve' : 'reject'} this ride?`)) {
                return;
            }
            
            axios.patch(`/admin/rides/${rideId}/status`, {
                status: status
            })
            .then(function(response) {
                alert(`Ride ${status === 'accepted' ? 'approved' : 'rejected'} successfully`);
                
                // Close the modal if it's open
                closeRideModal();
                
                // Refresh the page to show updated data
                window.location.reload();
            })
            .catch(function(error) {
                console.error('Error updating ride status:', error);
                alert('Error updating ride status. Please try again.');
            });
        }
    </script>
</body>
</html>