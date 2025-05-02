@extends('admin.layouts.admin')

@section('content')
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
            <h1 class="text-2xl font-bold">Passenger Details</h1>
            <a href="{{ route('admin.users') }}" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-black">
                Back to Users
            </a>
        </div>
        
        <!-- Passenger Details Card -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <!-- User Basic Info Section -->
            <div class="p-6 mb-6">
                <h3 class="text-xl font-semibold mb-4 border-b pb-2">User Information</h3>
                <div class="flex flex-col md:flex-row gap-8">
                    <div class="md:w-1/4">
                        <div class="h-48 w-48 rounded-lg bg-gray-200 overflow-hidden">
                        @if($passenger->profile_picture)
                            <img src="{{ Storage::url($passenger->profile_picture) }}" alt="Profile" class="h-full w-full object-cover">
                        @else
                            <img src="/api/placeholder/192/192" alt="Profile" class="h-full w-full object-cover">
                        @endif
                        </div>
                    </div>
                    <div class="md:w-3/4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                        <p class="text-sm text-gray-500 mb-1">Name</p>
                            <p class="font-medium">{{ $passenger->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Email</p>
                            <p class="font-medium">{{ $passenger->email ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Phone</p>
                            <p class="font-medium">{{ $passenger->phone ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Birthday</p>
                            <p class="font-medium">{{ $passenger->birthday ? date('Y-m-d', strtotime($passenger->birthday)) : 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Gender</p>
                            <p class="font-medium">{{ $passenger->gender ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Joined Date</p>
                            <p class="font-medium">{{ $passenger->created_at ? date('Y-m-d', strtotime($passenger->created_at)) : 'N/A' }}</p>
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
                                $statusClass = $statusClasses[$passenger->account_status] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                {{ ucfirst($passenger->account_status ?? 'N/A') }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Rating</p>
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                <span class="ml-1">{{ $passenger->passenger && $passenger->passenger->rating ? number_format($passenger->passenger->rating, 1) : 'N/A' }}</span>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Total Rides</p>
                            <p class="font-medium">{{ $passenger->passenger ? ($passenger->passenger->total_rides ?? 0) : 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Passenger Preferences -->
            <div class="p-6 mb-6 border-t">
                <h3 class="text-xl font-semibold mb-4 border-b pb-2">Ride Preferences</h3>
                @if($passenger->passenger && $passenger->passenger->ride_preferences)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @php
                            $preferences = json_decode($passenger->passenger->ride_preferences, true) ?? [];
                        @endphp
                        
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Preferred Vehicle Type</p>
                            <p class="font-medium">{{ isset($preferences['vehicle_type']) ? ucfirst($preferences['vehicle_type']) : 'No preference' }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Music Preference</p>
                            <p class="font-medium">{{ isset($preferences['music']) ? ucfirst($preferences['music']) : 'No preference' }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-500 mb-1">AC Setting</p>
                            <p class="font-medium">{{ isset($preferences['ac']) ? ucfirst($preferences['ac']) : 'No preference' }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Luggage Space Required</p>
                            <p class="font-medium">{{ isset($preferences['luggage']) ? 'Yes' : 'No' }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Traveling with Pets</p>
                            <p class="font-medium">{{ isset($preferences['pet_friendly']) && $preferences['pet_friendly'] ? 'Yes' : 'No' }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Women-Only Rides</p>
                            <p class="font-medium">{{ $passenger->women_only_rides ? 'Yes' : 'No' }}</p>
                        </div>
                    </div>
                @else
                    <p class="text-gray-500">No preferences set.</p>
                @endif
            </div>
            
            <!-- Saved Locations -->
            <div class="p-6 mb-6 border-t">
                <h3 class="text-xl font-semibold mb-4 border-b pb-2">Saved Locations</h3>
                @if($passenger->passenger && $passenger->passenger->preferences)
                    <div class="grid grid-cols-1 gap-4">
                        @php
                            $savedLocations = json_decode($passenger->passenger->preferences, true)['saved_locations'] ?? [];
                        @endphp
                        
                        @if(count($savedLocations) > 0)
                            @foreach($savedLocations as $index => $location)
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p class="font-medium">{{ $location['name'] ?? 'Location '.($index+1) }}</p>
                                    <p class="text-sm text-gray-600">{{ $location['address'] }}</p>
                                    <div class="flex mt-2">
                                        <span class="text-xs text-gray-500">
                                            Lat: {{ $location['latitude'] }}, Lng: {{ $location['longitude'] }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-gray-500">No saved locations.</p>
                        @endif
                    </div>
                @else
                    <p class="text-gray-500">No saved locations.</p>
                @endif
            </div>
            
            <!-- Ride History Summary -->
            <div class="p-6 mb-6 border-t">
                <h3 class="text-xl font-semibold mb-4 border-b pb-2">Ride History Summary</h3>
                @if($passenger->passenger)
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Total Rides</p>
                            <p class="text-2xl font-bold">{{ $passenger->passenger->total_rides ?? 0 }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Average Rating</p>
                            <div class="flex items-center">
                                <span class="text-2xl font-bold mr-1">{{ $passenger->passenger->rating ? number_format($passenger->passenger->rating, 1) : 'N/A' }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Cancelled Rides</p>
                            <p class="text-2xl font-bold">{{ $passenger->passenger->cancelled_rides ?? 0 }}</p>
                        </div>
                    </div>
                @else
                    <p class="text-gray-500">No ride history available.</p>
                @endif
            </div>
            
            <!-- Account Status Actions -->
            <div class="p-6 flex justify-end space-x-3 border-t">
                <a href="{{ route('admin.user.status', ['id' => $passenger->id, 'status' => 'activated']) }}" 
                   class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"
                   onclick="return confirm('Are you sure you want to activate this passenger?')">
                    Activate
                </a>
                <a href="{{ route('admin.user.status', ['id' => $passenger->id, 'status' => 'deactivated']) }}" 
                   class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
                   onclick="return confirm('Are you sure you want to deactivate this passenger?')">
                    Deactivate
                </a>
                <a href="{{ route('admin.user.status', ['id' => $passenger->id, 'status' => 'suspended']) }}" 
                   class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700"
                   onclick="return confirm('Are you sure you want to suspend this passenger?')">
                    Suspend
                </a>
                <a href="{{ route('admin.user.delete', ['id' => $passenger->id]) }}" 
                   class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700"
                   onclick="return confirm('Are you sure you want to delete this passenger? This action cannot be undone.')">
                    Delete
                </a>
            </div>
        </div>
    </div>
</main>
@endsection