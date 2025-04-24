<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>inTime - Driver Details</title>
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
                <a href="{{ route('admin.dashboard') }}" class="font-medium text-gray-500 hover:text-black">Dashboard</a>
                <a href="{{ route('admin.users') }}" class="font-medium text-black">Users Management</a>
                <a href="{{ route('admin.rides.pending') }}" class="font-medium text-gray-500 hover:text-black">Rides Management</a>
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
        <div class="max-w-6xl mx-auto">
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

            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold">Driver Details</h1>
                <a href="{{ route('admin.users') }}" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-black">
                    Back to Users
                </a>
            </div>
            
            <!-- Driver Details Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <!-- User Basic Info Section -->
                <div class="p-6 mb-6">
                    <h3 class="text-xl font-semibold mb-4 border-b pb-2">User Information</h3>
                    <div class="flex flex-col md:flex-row gap-8">
                        <div class="md:w-1/4">
                            <div class="h-48 w-48 rounded-lg bg-gray-200 overflow-hidden">
                            @if($driver->profile_picture)
                                <img src="{{ Storage::url($driver->profile_picture) }}" alt="Profile" class="h-full w-full object-cover">
                            @else
                                <img src="/api/placeholder/192/192" alt="Profile" class="h-full w-full object-cover">
                            @endif
                            </div>
                        </div>
                        <div class="md:w-3/4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Name</p>
                                <p class="font-medium">{{ $driver->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Email</p>
                                <p class="font-medium">{{ $driver->email ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Phone</p>
                                <p class="font-medium">{{ $driver->phone ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Birthday</p>
                                <p class="font-medium">{{ $driver->birthday ? date('Y-m-d', strtotime($driver->birthday)) : 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Gender</p>
                                <p class="font-medium">{{ $driver->gender ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Joined Date</p>
                                <p class="font-medium">{{ $driver->created_at ? date('Y-m-d', strtotime($driver->created_at)) : 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Status</p>
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
                                    {{ ucfirst($driver->account_status ?? 'N/A') }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Rating</p>
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    <span class="ml-1">{{ $driver->driver && $driver->driver->rating ? number_format($driver->driver->rating, 1) : 'N/A' }}</span>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Completed Rides</p>
                                <p class="font-medium">{{ $driver->driver ? ($driver->driver->completed_rides ?? 0) : 0 }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Balance</p>
                                <p class="font-medium">DH {{ $driver->driver ? number_format($driver->driver->balance ?? 0, 2) : '0.00' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Online Status</p>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $driver->is_online ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $driver->is_online ? 'Online' : 'Offline' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Driver License Info -->
                <div class="p-6 mb-6 border-t">
                    <h3 class="text-xl font-semibold mb-4 border-b pb-2">License Information</h3>
                    @if($driver->driver)
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <p class="text-sm text-gray-500 mb-1">License Number</p>
                                <p class="font-medium">{{ $driver->driver->license_number ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">License Expiry</p>
                                <p class="font-medium">{{ $driver->driver->license_expiry ? date('Y-m-d', strtotime($driver->driver->license_expiry)) : 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Verification Status</p>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ ($driver->driver->is_verified ?? false) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ($driver->driver->is_verified ?? false) ? 'Verified' : 'Not Verified' }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <p class="text-sm text-gray-500 mb-2">License Photo</p>
                                <div class="h-48 w-full rounded-lg bg-gray-200 overflow-hidden">
                                @if($driver->driver && $driver->driver->license_photo)
                                    <img src="{{ Storage::url($driver->driver->license_photo) }}" alt="License" class="h-full w-full object-cover">
                                @else
                                    <div class="h-full w-full flex items-center justify-center bg-gray-200">
                                        <span class="text-gray-400">No photo available</span>
                                    </div>
                                @endif
                                </div>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500 mb-2">Insurance Document</p>
                                <div class="h-48 w-full rounded-lg bg-gray-200 overflow-hidden">
                                    @if($driver->driver && $driver->driver->insurance_document)
                                        <img src="{{ Storage::url($driver->driver->insurance_document) }}" alt="Insurance" class="h-full w-full object-cover">
                                    @else
                                        <div class="h-full w-full flex items-center justify-center bg-gray-200">
                                            <span class="text-gray-400">No document available</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500 mb-2">Good Conduct Certificate</p>
                                <div class="h-48 w-full rounded-lg bg-gray-200 overflow-hidden">
                                    @if($driver->driver && $driver->driver->good_conduct_certificate)
                                        <img src="{{ Storage::url($driver->driver->good_conduct_certificate) }}" alt="Certificate" class="h-full w-full object-cover">
                                    @else
                                        <div class="h-full w-full flex items-center justify-center bg-gray-200">
                                            <span class="text-gray-400">No certificate available</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-500">No driver license information available.</p>
                    @endif
                </div>
                
                <!-- Vehicle Information -->
                <div class="p-6 mb-6 border-t">
                    <h3 class="text-xl font-semibold mb-4 border-b pb-2">Vehicle Information</h3>
                    @if($driver->driver && $driver->driver->vehicle)
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Make & Model</p>
                                <p class="font-medium">{{ $driver->driver->vehicle->make ?? '' }} {{ $driver->driver->vehicle->model ?? '' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Year</p>
                                <p class="font-medium">{{ $driver->driver->vehicle->year ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Color</p>
                                <p class="font-medium">{{ $driver->driver->vehicle->color ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Plate Number</p>
                                <p class="font-medium">{{ $driver->driver->vehicle->plate_number ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Type</p>
                                <p class="font-medium">{{ $driver->driver->vehicle->type ? ucfirst($driver->driver->vehicle->type) : 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Capacity</p>
                                <p class="font-medium">{{ $driver->driver->vehicle->capacity ? $driver->driver->vehicle->capacity . ' passengers' : 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Insurance Expiry</p>
                                <p class="font-medium">{{ $driver->driver->vehicle->insurance_expiry ? date('Y-m-d', strtotime($driver->driver->vehicle->insurance_expiry)) : 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Registration Expiry</p>
                                <p class="font-medium">{{ $driver->driver->vehicle->registration_expiry ? date('Y-m-d', strtotime($driver->driver->vehicle->registration_expiry)) : 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Status</p>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $driver->driver->vehicle->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $driver->driver->vehicle->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-6">
                            <p class="text-sm text-gray-500 mb-2">Vehicle Photo</p>
                            <div class="h-64 w-full md:w-2/3 lg:w-1/2 rounded-lg bg-gray-200 overflow-hidden">
                                @if($driver->driver->vehicle && $driver->driver->vehicle->vehicle_photo)
                                    <img src="{{ Storage::url($driver->driver->vehicle->vehicle_photo) }}" alt="Vehicle" class="h-full w-full object-cover">
                                @else
                                    <div class="h-full w-full flex items-center justify-center bg-gray-200">
                                        <span class="text-gray-400">No vehicle photo available</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Vehicle Features -->
                        <div class="mt-6">
                            <p class="text-sm text-gray-500 mb-2">Vehicle Features</p>
                            
                            @if($driver->driver->vehicle->features && count($driver->driver->vehicle->features) > 0)
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                    @foreach($driver->driver->vehicle->features as $feature)
                                        <div class="flex items-center bg-blue-50 p-2 rounded">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="ml-2 text-sm">{{ $allFeatures[$feature->feature] ?? ucfirst(str_replace('_', ' ', $feature->feature)) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500">No features specified</p>
                            @endif
                        </div>
                    @else
                        <p class="text-gray-500">No vehicle information available.</p>
                    @endif
                </div>
                
                <!-- Account Status Actions -->
                <div class="p-6 flex justify-end space-x-3 border-t">
                    <a href="{{ route('admin.user.status', ['id' => $driver->id, 'status' => 'activated']) }}" 
                       class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"
                       onclick="return confirm('Are you sure you want to activate this driver? This will also verify them.')">
                        Activate
                    </a>
                    <a href="{{ route('admin.user.status', ['id' => $driver->id, 'status' => 'deactivated']) }}" 
                       class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
                       onclick="return confirm('Are you sure you want to deactivate this driver?')">
                        Deactivate
                    </a>
                    <a href="{{ route('admin.user.status', ['id' => $driver->id, 'status' => 'suspended']) }}" 
                       class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700"
                       onclick="return confirm('Are you sure you want to suspend this driver?')">
                        Suspend
                    </a>
                    <a href="{{ route('admin.user.delete', ['id' => $driver->id]) }}" 
                       class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700"
                       onclick="return confirm('Are you sure you want to delete this driver? This action cannot be undone.')">
                        Delete
                    </a>
                </div>
            </div>
        </div>
    </main>
    
    <!-- JavaScript -->
    <script>
        // Any specific JavaScript for driver details page can go here
    </script>
</body>
</html>