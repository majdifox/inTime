<!-- resources/views/driver/profile/private.blade.php -->
@extends('driver.layouts.driver')

@section('title', 'My Driver Profile')

@section('content')
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
            <!-- Left Column - Navigation and User Info -->
            <div class="w-full lg:w-1/3 flex flex-col gap-6">
                <!-- User Info Card -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="h-16 w-16 rounded-full bg-gray-300 overflow-hidden">
                            @if(Auth::user()->profile_picture)
                                <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Profile" class="h-full w-full object-cover">
                            @else
                                <div class="h-full w-full flex items-center justify-center bg-gray-400 text-white">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <h2 class="text-xl font-bold">{{ Auth::user()->name }}</h2>
                            <p class="text-gray-600">{{ Auth::user()->email }}</p>
                            <p class="text-gray-600">{{ Auth::user()->phone }}</p>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Driver Since</span>
                            <span class="font-medium">{{ $driver->created_at->format('M Y') }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Account Status</span>
                            <span class="font-medium">
                                @if(Auth::user()->account_status == 'activated')
                                    <span class="text-green-600">Active</span>
                                @elseif(Auth::user()->account_status == 'suspended')
                                    <span class="text-red-600">Suspended</span>
                                @elseif(Auth::user()->account_status == 'pending')
                                    <span class="text-yellow-600">Pending Approval</span>
                                @else
                                    <span>{{ ucfirst(Auth::user()->account_status) }}</span>
                                @endif
                            </span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Online Status</span>
                            <span class="font-medium">
                                @if(Auth::user()->is_online)
                                    <span class="text-green-600">Online</span>
                                @else
                                    <span class="text-red-600">Offline</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Stats Card -->
                <div class="bg-white rounded-lg shadow-md p-6" id="stats-section">
                    <h2 class="text-xl font-bold mb-4">Stats Summary</h2>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center p-4 bg-gray-50 rounded-lg shadow-sm">
                            <p class="text-gray-500 text-sm">Total Rides</p>
                            <p class="text-2xl font-bold">{{ $driver->completed_rides ?? 0 }}</p>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg shadow-sm">
                            <p class="text-gray-500 text-sm">Rating</p>
                            <div class="flex items-center justify-center">
                                <p class="text-2xl font-bold">{{ number_format($driver->rating, 1) }}</p>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            </div>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg shadow-sm">
                            <p class="text-gray-500 text-sm">Total Earnings</p>
                            <p class="text-2xl font-bold">MAD {{ number_format($driver->balance, 2) }}</p>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg shadow-sm">
                            <p class="text-gray-500 text-sm">Response Rate</p>
                            <p class="text-2xl font-bold">{{ isset($driver->response_rate) ? number_format($driver->response_rate*100, 0) : '98' }}%</p>
                        </div>
                    </div>
                    
                    <div class="mt-4 text-center">
                        <a href="{{ route('driver.earnings') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            View Detailed Earnings
                        </a>
                    </div>
                </div>
                  <!-- Profile Navigation -->
                  <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold mb-4">Profile Settings</h2>
                    
                    <nav class="space-y-2">
                        <a href="#profile-section" class="block px-3 py-2 rounded-md bg-blue-50 text-blue-600 font-medium">
                            Driver Profile
                        </a>
                        <a href="#vehicle-section" class="block px-3 py-2 rounded-md hover:bg-gray-50 transition">
                            Vehicle Information
                        </a>
                        <a href="#stats-section" class="block px-3 py-2 rounded-md hover:bg-gray-50 transition">
                            Statistics & Earnings
                        </a>
                        <a href="#documents-section" class="block px-3 py-2 rounded-md hover:bg-gray-50 transition">
                            Documents & Verification
                        </a>
                        <a href="#password-section" class="block px-3 py-2 rounded-md hover:bg-gray-50 transition">
                            Change Password
                        </a>
                        <a href="{{ route('driver.public.profile', $driver->id) }}" class="block px-3 py-2 rounded-md hover:bg-gray-50 transition text-blue-600">
                            View Public Profile
                        </a>
                    </nav>
                </div>
                </div>
            
            <!-- Right Column - Settings Forms -->
            <div class="w-full lg:w-2/3">
                <!-- Driver Profile Section -->
                <div id="profile-section" class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl font-bold mb-4">Driver Profile</h2>
                    
                    <form action="{{ route('driver.profile.update') }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PATCH')
                        
                        <!-- Profile Information -->
                        <div>
                            <h3 class="text-lg font-medium mb-3">Basic Information</h3>
                            
                            <div class="space-y-4">
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                                        <div class="mt-1">
                                            <input type="text" id="name" name="name" value="{{ Auth::user()->name }}" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" disabled>
                                            <p class="mt-1 text-xs text-gray-500">To change your name, please contact support.</p>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                                        <div class="mt-1">
                                            <input type="email" id="email" name="email" value="{{ Auth::user()->email }}" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" disabled>
                                            <p class="mt-1 text-xs text-gray-500">To change your email, please contact support.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Women-Only Driver Setting (for female drivers only) -->
                        @if(Auth::user()->gender === 'female')
                            <div class="border-t border-gray-200 pt-6">
                                <h3 class="text-lg font-medium mb-3">Women-Only Driver Setting</h3>
                                
                                <div class="space-y-4">
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="women_only_driver" name="women_only_driver" type="checkbox" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded" {{ $driver->women_only_driver ? 'checked' : '' }}>
                                        </div>
                                        <div class="ml-3">
                                            <label for="women_only_driver" class="font-medium text-gray-700">Enable Women-Only Mode</label>
                                            <p class="text-gray-500 text-sm">When enabled, you'll only receive ride requests from female passengers.</p>
                                            
                                            @if($driver->vehicle && $driver->vehicle->type !== 'women' && $driver->women_only_driver)
                                                <p class="text-yellow-600 text-sm mt-2">
                                                    For consistent matching, consider updating your vehicle type to "Women" in vehicle settings.
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        
                    </form>
                </div>
                
                <!-- Vehicle Information Section -->
                <div id="vehicle-section" class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl font-bold mb-4">Vehicle Information</h2>
                    
                    <form action="{{ route('driver.vehicle.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PATCH')
                        
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label for="make" class="block text-sm font-medium text-gray-700">Make</label>
                                <input type="text" id="make" name="make" value="{{ $driver->vehicle->make ?? '' }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                            
                            <div>
                                <label for="model" class="block text-sm font-medium text-gray-700">Model</label>
                                <input type="text" id="model" name="model" value="{{ $driver->vehicle->model ?? '' }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <div>
                                <label for="year" class="block text-sm font-medium text-gray-700">Year</label>
                                <input type="number" id="year" name="year" value="{{ $driver->vehicle->year ?? '' }}" min="2000" max="{{ date('Y') }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                            
                            <div>
                                <label for="color" class="block text-sm font-medium text-gray-700">Color</label>
                                <input type="text" id="color" name="color" value="{{ $driver->vehicle->color ?? '' }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                            
                            <div>
                                <label for="plate_number" class="block text-sm font-medium text-gray-700">License Plate</label>
                                <input type="text" id="plate_number" name="plate_number" value="{{ $driver->vehicle->plate_number ?? '' }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                        
                        <div>
                            <label for="vehicle_type" class="block text-sm font-medium text-gray-700">Vehicle Type</label>
                            <select id="vehicle_type" name="type" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option value="basic" {{ isset($driver->vehicle) && $driver->vehicle->type == 'basic' ? 'selected' : '' }}>Basic</option>
                                <option value="comfort" {{ isset($driver->vehicle) && $driver->vehicle->type == 'comfort' ? 'selected' : '' }}>Comfort</option>
                                <option value="black" {{ isset($driver->vehicle) && $driver->vehicle->type == 'black' ? 'selected' : '' }}>Black</option>
                                <option value="wav" {{ isset($driver->vehicle) && $driver->vehicle->type == 'wav' ? 'selected' : '' }}>Wheelchair Accessible</option>
                                @if(Auth::user()->gender === 'female')
                                    <option value="women" {{ isset($driver->vehicle) && $driver->vehicle->type == 'women' ? 'selected' : '' }}>Women</option>
                                @endif
                            </select>
                        </div>
                        
                        <div>
                            <label for="capacity" class="block text-sm font-medium text-gray-700">Seating Capacity</label>
                            <select id="capacity" name="capacity" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                @for($i = 1; $i <= 6; $i++)
                                    <option value="{{ $i }}" {{ isset($driver->vehicle) && $driver->vehicle->capacity == $i ? 'selected' : '' }}>{{ $i }} {{ $i == 1 ? 'person' : 'people' }}</option>
                                @endfor
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Vehicle Photo</label>
                            <div class="mt-1 flex items-center">
                                @if(isset($driver->vehicle) && $driver->vehicle->vehicle_photo)
                                    <div class="mr-4">
                                        <img src="{{ asset('storage/' . $driver->vehicle->vehicle_photo) }}" alt="Vehicle" class="h-24 w-auto rounded">
                                    </div>
                                @endif
                                
                                <div class="flex-1">
                                    <input type="file" id="vehicle_photo" name="vehicle_photo" class="sr-only" accept="image/*">
                                    <label for="vehicle_photo" class="relative cursor-pointer rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                        <span>Upload a photo</span>
                                        <p class="text-xs text-gray-500">PNG, JPG up to 5MB</p>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Vehicle Features</label>
                            <div class="grid grid-cols-2 gap-3 md:grid-cols-3">
                                <div class="flex items-center">
                                    <input id="ac" name="features[]" value="ac" type="checkbox" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                        {{ isset($driver->vehicle) && $driver->vehicle->features->contains('feature', 'ac') ? 'checked' : '' }}>
                                    <label for="ac" class="ml-2 block text-sm text-gray-700">Air Conditioning</label>
                                </div>
                                <div class="flex items-center">
                                    <input id="wifi" name="features[]" value="wifi" type="checkbox" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                        {{ isset($driver->vehicle) && $driver->vehicle->features->contains('feature', 'wifi') ? 'checked' : '' }}>
                                    <label for="wifi" class="ml-2 block text-sm text-gray-700">WiFi</label>
                                </div>
                                <div class="flex items-center">
                                    <input id="child_seat" name="features[]" value="child_seat" type="checkbox" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                        {{ isset($driver->vehicle) && $driver->vehicle->features->contains('feature', 'child_seat') ? 'checked' : '' }}>
                                    <label for="child_seat" class="ml-2 block text-sm text-gray-700">Child Seat</label>
                                </div>
                                <div class="flex items-center">
                                    <input id="usb_charger" name="features[]" value="usb_charger" type="checkbox" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                        {{ isset($driver->vehicle) && $driver->vehicle->features->contains('feature', 'usb_charger') ? 'checked' : '' }}>
                                    <label for="usb_charger" class="ml-2 block text-sm text-gray-700">USB Charger</label>
                                </div>
                                <div class="flex items-center">
                                    <input id="pet_friendly" name="features[]" value="pet_friendly" type="checkbox" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                        {{ isset($driver->vehicle) && $driver->vehicle->features->contains('feature', 'pet_friendly') ? 'checked' : '' }}>
                                    <label for="pet_friendly" class="ml-2 block text-sm text-gray-700">Pet Friendly</label>
                                </div>
                                <div class="flex items-center">
                                    <input id="luggage_carrier" name="features[]" value="luggage_carrier" type="checkbox" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                        {{ isset($driver->vehicle) && $driver->vehicle->features->contains('feature', 'luggage_carrier') ? 'checked' : '' }}>
                                    <label for="luggage_carrier" class="ml-2 block text-sm text-gray-700">Luggage Carrier</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="pt-5">
                            <div class="flex justify-end">
                                <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Update Vehicle
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- Documents Section -->
                <div id="documents-section" class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl font-bold mb-4">Documents & Verification</h2>
                    
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-medium mb-3">License Information</h3>
                            
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex flex-col md:flex-row md:items-center justify-between">
                                    <div>
                                        <p class="text-gray-700"><span class="font-medium">License Number:</span> {{ $driver->license_number }}</p>
                                        <p class="text-gray-700"><span class="font-medium">Expiry Date:</span> {{ \Carbon\Carbon::parse($driver->license_expiry)->format('M d, Y') }}</p>
                                        
                                        @if($driver->license_expiry && \Carbon\Carbon::parse($driver->license_expiry)->lt(now()->addMonths(3)))
                                            <p class="text-red-600 text-sm mt-2">
                                                Your license will expire soon. Please update it before expiration.
                                            </p>
                                        @endif
                                    </div>
                                    
                                    @if($driver->license_photo)
                                        <div class="mt-4 md:mt-0">
                                            <img src="{{ asset('storage/' . $driver->license_photo) }}" alt="License Photo" class="h-16 w-auto rounded border border-gray-300">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium mb-3">Vehicle Registration & Insurance</h3>
                            
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <p class="font-medium">Registration</p>
                                        <p class="text-gray-700">Expiry: {{ \Carbon\Carbon::parse($driver->vehicle->registration_expiry ?? now())->format('M d, Y') }}</p>
                                        
                                        @if($driver->vehicle && $driver->vehicle->registration_expiry && \Carbon\Carbon::parse($driver->vehicle->registration_expiry)->lt(now()->addMonths(3)))
                                            <p class="text-red-600 text-sm mt-1">
                                                Expires soon. Please update.
                                            </p>
                                        @endif
                                    </div>
                                    
                                    <div>
                                        <p class="font-medium">Insurance</p>
                                        <p class="text-gray-700">Expiry: {{ \Carbon\Carbon::parse($driver->vehicle->insurance_expiry ?? now())->format('M d, Y') }}</p>
                                        
                                        @if($driver->vehicle && $driver->vehicle->insurance_expiry && \Carbon\Carbon::parse($driver->vehicle->insurance_expiry)->lt(now()->addMonths(3)))
                                            <p class="text-red-600 text-sm mt-1">
                                                Expires soon. Please update.
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4 text-sm text-gray-600">
                                <p>
                                    To update your license, insurance, or registration documents, please contact customer support.
                                </p>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium mb-3">Verification Status</h3>
                            
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center">
                                    @if($driver->is_verified)
                                        <div class="rounded-full bg-green-100 p-2 mr-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-medium text-green-600">Verified</p>
                                            <p class="text-gray-600 text-sm">Your driver account is fully verified</p>
                                        </div>
                                    @elseif(Auth::user()->account_status == 'pending')
                                        <div class="rounded-full bg-yellow-100 p-2 mr-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-medium text-yellow-600">Pending Verification</p>
                                            <p class="text-gray-600 text-sm">Your account is under review by our team</p>
                                        </div>
                                    @else
                                        <div class="rounded-full bg-red-100 p-2 mr-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-medium text-red-600">Not Verified</p>
                                            <p class="text-gray-600 text-sm">Contact support for assistance</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Change Password Section -->
                <div id="password-section" class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold mb-4">Change Password</h2>
                    
                    <form action="{{ route('driver.password.update') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                            <input type="password" id="current_password" name="current_password" required class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                        
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                            <input type="password" id="password" name="password" required class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                        
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" required class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                        
                        <div class="border-t border-gray-200 pt-6">
                            <button type="submit" class="w-full md:w-auto bg-blue-600 text-white py-2 px-6 rounded-md font-medium hover:bg-blue-700 transition">
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>


    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Smooth scroll to sections when links are clicked
            const navLinks = document.querySelectorAll('nav a[href^="#"]');
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const targetId = this.getAttribute('href').substring(1);
                    const targetElement = document.getElementById(targetId);
                    
                    if (targetElement) {
                        // Remove active class from all links
                        navLinks.forEach(navLink => {
                            navLink.classList.remove('bg-blue-50', 'text-blue-600');
                            navLink.classList.add('hover:bg-gray-50');
                        });
                        
                        // Add active class to clicked link
                        this.classList.add('bg-blue-50', 'text-blue-600');
                        this.classList.remove('hover:bg-gray-50');
                        
                        // Scroll to target element
                        window.scrollTo({
                            top: targetElement.offsetTop - 20,
                            behavior: 'smooth'
                        });
                    }
                });
            });
            
            // Handle file input change (for image preview)
            const vehiclePhotoInput = document.getElementById('vehicle_photo');
            if (vehiclePhotoInput) {
                vehiclePhotoInput.addEventListener('change', function(e) {
                    if (this.files && this.files[0]) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            // Find the image preview element or create one if it doesn't exist
                            let preview = document.querySelector('#vehicle-photo-preview');
                            if (!preview) {
                                preview = document.createElement('div');
                                preview.id = 'vehicle-photo-preview';
                                preview.className = 'mt-2';
                                preview.innerHTML = '<img src="" alt="Vehicle Photo Preview" class="h-24 w-auto rounded">';
                                vehiclePhotoInput.parentNode.appendChild(preview);
                            }
                            
                            // Update the image src
                            const img = preview.querySelector('img');
                            if (img) {
                                img.src = e.target.result;
                            }
                        };
                        reader.readAsDataURL(this.files[0]);
                    }
                });
            }
            
            // Show active section based on URL hash
            const handleHashChange = () => {
                const hash = window.location.hash || '#profile-section';
                const targetLink = document.querySelector(`nav a[href="${hash}"]`);
                
                if (targetLink) {
                    targetLink.click();
                }
            };
            
            // Initial check for hash
            handleHashChange();
            
            // Listen for hash changes
            window.addEventListener('hashchange', handleHashChange);
        });
    </script>
@endsection