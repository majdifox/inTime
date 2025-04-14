<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>inTime - Profile Settings</title>
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <!-- Header/Navigation -->
    <header class="px-4 py-4 flex items-center justify-between bg-white shadow-sm sticky top-0 z-50">
        <!-- Logo and navigation -->
        <div class="flex items-center space-x-8">
            <!-- Logo -->
            <a href="{{ route('driver.dashboard') }}" class="text-2xl font-bold">inTime</a>
            
            <!-- Navigation Links -->
            <nav class="hidden md:flex space-x-6">
                <a href="{{ route('driver.awaiting.rides') }}" class="font-medium hover:text-blue-600 transition">Awaiting Rides</a>
                <a href="{{ route('driver.active.rides') }}" class="font-medium hover:text-blue-600 transition">Active Rides</a>
                <a href="{{ route('driver.history') }}" class="font-medium hover:text-blue-600 transition">History</a>
                <a href="{{ route('driver.earnings') }}" class="font-medium hover:text-blue-600 transition">Earnings</a>
            </nav>
        </div>
        
        <!-- Mobile Menu Button -->
        <button type="button" class="md:hidden p-2 rounded-md text-gray-700 hover:bg-gray-100" id="mobile-menu-button">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        
        <!-- User Profile -->
        <div class="flex items-center space-x-4">
            <!-- Online/Offline Status -->
            <div class="flex items-center bg-gray-100 rounded-full px-3 py-1">
                <span id="status-indicator" class="w-3 h-3 rounded-full {{ Auth::user()->is_online ? 'bg-green-500' : 'bg-red-500' }} mr-2"></span>
                <span id="status-text" class="text-sm font-medium">{{ Auth::user()->is_online ? 'Online' : 'Offline' }}</span>
            </div>
            
            <div class="relative">
                <button type="button" class="h-10 w-10 rounded-full bg-gray-300 overflow-hidden" id="profile-button">
                    @if(Auth::user()->profile_picture)
                        <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Profile" class="h-full w-full object-cover">
                    @else
                        <img src="/api/placeholder/40/40" alt="Profile" class="h-full w-full object-cover">
                    @endif
                </button>
                
                <!-- Profile Dropdown -->
                <div class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50" id="profile-dropdown">
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-blue-600 bg-gray-100">Profile Settings</a>
                    <a href="{{ route('driver.reviews') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My Reviews</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                            {{ __('Log Out') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Mobile Navigation Menu (Hidden by default) -->
    <div class="fixed inset-0 flex z-40 md:hidden transform translate-x-full transition-transform duration-300 ease-in-out" id="mobile-menu">
        <div class="relative flex-1 flex flex-col max-w-xs w-full bg-white">
            <div class="px-4 pt-5 pb-4">
                <div class="flex items-center justify-between">
                    <div class="text-2xl font-bold">inTime</div>
                    <button type="button" class="rounded-md text-gray-400 hover:text-gray-500 focus:outline-none" id="close-mobile-menu">
                        <span class="sr-only">Close menu</span>
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <div class="mt-6">
                    <nav class="grid gap-y-4">
                        <a href="{{ route('driver.dashboard') }}" class="font-medium px-3 py-2 rounded-md hover:bg-gray-100">Dashboard</a>
                        <a href="{{ route('driver.awaiting.rides') }}" class="font-medium px-3 py-2 rounded-md hover:bg-gray-100">Awaiting Rides</a>
                        <a href="{{ route('driver.active.rides') }}" class="font-medium px-3 py-2 rounded-md hover:bg-gray-100">Active Rides</a>
                        <a href="{{ route('driver.history') }}" class="font-medium px-3 py-2 rounded-md hover:bg-gray-100">History</a>
                        <a href="{{ route('driver.earnings') }}" class="font-medium px-3 py-2 rounded-md hover:bg-gray-100">Earnings</a>
                        <a href="{{ route('profile.edit') }}" class="font-medium px-3 py-2 rounded-md bg-blue-50 text-blue-600">Profile Settings</a>
                        <a href="{{ route('driver.reviews') }}" class="font-medium px-3 py-2 rounded-md hover:bg-gray-100">My Reviews</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left font-medium px-3 py-2 rounded-md text-red-600 hover:bg-gray-100">
                                {{ __('Log Out') }}
                            </button>
                        </form>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
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
        
        <div class="max-w-4xl mx-auto">
            <h1 class="text-2xl font-bold mb-6">Profile Settings</h1>
            
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <!-- Profile Navigation Tabs -->
                <div class="bg-gray-50 px-6 border-b">
                    <nav class="flex -mb-px">
                        <button class="tab-button inline-flex items-center py-4 px-1 border-b-2 font-medium text-blue-600 border-blue-600 whitespace-nowrap" data-tab="personal-info">
                            Personal Information
                        </button>
                        <button class="tab-button inline-flex items-center py-4 px-1 border-b-2 font-medium text-gray-500 border-transparent hover:text-gray-700 hover:border-gray-300 whitespace-nowrap ml-8" data-tab="vehicle-info">
                            Vehicle Information
                        </button>
                        <button class="tab-button inline-flex items-center py-4 px-1 border-b-2 font-medium text-gray-500 border-transparent hover:text-gray-700 hover:border-gray-300 whitespace-nowrap ml-8" data-tab="account-security">
                            Account & Security
                        </button>
                    </nav>
                </div>
                
                <!-- Personal Information Tab -->
                <div id="personal-info-tab" class="tab-content p-6">
                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('patch')
                        
                        <div class="space-y-6">
                            <!-- Profile Picture -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Profile Picture</label>
                                <div class="flex items-center">
                                    <div class="h-20 w-20 rounded-full bg-gray-300 overflow-hidden">
                                        @if(Auth::user()->profile_picture)
                                            <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Profile" class="h-full w-full object-cover">
                                        @else
                                            <div class="h-full w-full flex items-center justify-center text-gray-500 bg-gray-300">
                                                {{ substr(Auth::user()->name, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-5">
                                        <input type="file" id="profile_picture" name="profile_picture" class="hidden" accept="image/*">
                                        <button type="button" id="upload-button" class="bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none">
                                            Change Photo
                                        </button>
                                        <p class="mt-1 text-xs text-gray-500">
                                            JPG, PNG or GIF. Max 2MB.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Name and Email -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                                    <input type="text" id="name" name="name" value="{{ Auth::user()->name }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                    <input type="email" id="email" name="email" value="{{ Auth::user()->email }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" disabled>
                                    <p class="mt-1 text-xs text-gray-500">Email cannot be changed</p>
                                </div>
                            </div>
                            
                            <!-- Phone and Birthday -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                    <input type="tel" id="phone" name="phone" value="{{ Auth::user()->phone }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                
                                <div>
                                    <label for="birthday" class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                                    <input type="date" id="birthday" name="birthday" value="{{ Auth::user()->birthday ? Auth::user()->birthday->format('Y-m-d') : '' }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" disabled>
                                    <p class="mt-1 text-xs text-gray-500">Birthday cannot be changed</p>
                                </div>
                            </div>
                            
                            <!-- Gender and Women-Only preference -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                                    <select id="gender" name="gender" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" disabled>
                                        <option value="male" {{ Auth::user()->gender === 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ Auth::user()->gender === 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ Auth::user()->gender === 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500">Gender cannot be changed</p>
                                </div>
                                
                                <div>
                                    @if(Auth::user()->driver)
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Women-Only Driver</label>
                                        <div class="flex items-center">
                                            <input type="checkbox" id="women_only_driver" name="women_only_driver" value="1" 
                                                {{ Auth::user()->driver->women_only_driver ? 'checked' : '' }}
                                                {{ Auth::user()->gender !== 'female' ? 'disabled' : '' }}
                                                class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                            <label for="women_only_driver" class="ml-2 block text-sm text-gray-700">
                                                I want to be a women-only driver
                                            </label>
                                        </div>
                                        @if(Auth::user()->gender !== 'female')
                                            <p class="mt-1 text-xs text-red-500">Only female drivers can enable this option</p>
                                        @endif
                                    @endif
                                </div>
                            </div>
                            
                            <div class="border-t pt-6 flex justify-end">
                                <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-md shadow-sm text-sm font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Vehicle Information Tab -->
                <div id="vehicle-info-tab" class="tab-content p-6 hidden">
                    @if(Auth::user()->driver && Auth::user()->driver->vehicle)
                        <form method="POST" action="{{ route('driver.vehicle.update') }}" enctype="multipart/form-data">
                            @csrf
                            @method('patch')
                            
                            <div class="space-y-6">
                                <!-- Vehicle Photo -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Vehicle Photo</label>
                                    <div class="flex items-center">
                                        <div class="h-24 w-36 bg-gray-200 rounded-md overflow-hidden">
                                            @if(Auth::user()->driver->vehicle->vehicle_photo)
                                                <img src="{{ asset('storage/' . Auth::user()->driver->vehicle->vehicle_photo) }}" alt="Vehicle" class="h-full w-full object-cover">
                                            @else
                                                <div class="h-full w-full flex items-center justify-center text-gray-400">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-5">
                                            <input type="file" id="vehicle_photo" name="vehicle_photo" class="hidden" accept="image/*">
                                            <button type="button" id="upload-vehicle-button" class="bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none">
                                                Change Photo
                                            </button>
                                            <p class="mt-1 text-xs text-gray-500">
                                                JPG, PNG or GIF. Max 2MB.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Vehicle Type and Capacity -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    <div>
                                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Vehicle Type</label>
                                        <select id="type" name="type" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                            <option value="share" {{ Auth::user()->driver->vehicle->type === 'share' ? 'selected' : '' }}>Share</option>
                                            <option value="comfort" {{ Auth::user()->driver->vehicle->type === 'comfort' ? 'selected' : '' }}>Comfort</option>
                                            <option value="women" {{ Auth::user()->driver->vehicle->type === 'women' ? 'selected' : '' }}>Women</option>
                                            <option value="wav" {{ Auth::user()->driver->vehicle->type === 'wav' ? 'selected' : '' }}>WAV</option>
                                            <option value="black" {{ Auth::user()->driver->vehicle->type === 'black' ? 'selected' : '' }}>Black</option>
                                        </select>
                                        @if(Auth::user()->gender !== 'female')
                                            <p class="mt-1 text-xs text-red-500">Note: Only female drivers can select 'Women' vehicle type</p>
                                        @endif
                                    </div>
                                    
                                    <div>
                                        <label for="capacity" class="block text-sm font-medium text-gray-700 mb-1">Seating Capacity</label>
                                        <input type="number" id="capacity" name="capacity" value="{{ Auth::user()->driver->vehicle->capacity }}" min="1" max="50" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>
                                
                                <!-- Make, Model, Year -->
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                                    <div>
                                        <label for="make" class="block text-sm font-medium text-gray-700 mb-1">Make</label>
                                        <input type="text" id="make" name="make" value="{{ Auth::user()->driver->vehicle->make }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    
                                    <div>
                                        <label for="model" class="block text-sm font-medium text-gray-700 mb-1">Model</label>
                                        <input type="text" id="model" name="model" value="{{ Auth::user()->driver->vehicle->model }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    
                                    <div>
                                        <label for="year" class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                                        <input type="number" id="year" name="year" value="{{ Auth::user()->driver->vehicle->year }}" min="2005" max="{{ date('Y') + 1 }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>
                                
                                <!-- Color and Plate -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    <div>
                                        <label for="color" class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                                        <input type="text" id="color" name="color" value="{{ Auth::user()->driver->vehicle->color }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    
                                    <div>
                                        <label for="plate_number" class="block text-sm font-medium text-gray-700 mb-1">License Plate</label>
                                        <input type="text" id="plate_number" name="plate_number" value="{{ Auth::user()->driver->vehicle->plate_number }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>
                                
                                <!-- Vehicle Features -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Vehicle Features</label>
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                        <div>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="features[]" value="ac" 
                                                       {{ Auth::user()->driver->vehicle->hasFeature('ac') ? 'checked' : '' }}
                                                       class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                                <span class="ml-2 text-sm text-gray-700">Air Conditioning</span>
                                            </label>
                                        </div>
                                        <div>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="features[]" value="wifi" 
                                                       {{ Auth::user()->driver->vehicle->hasFeature('wifi') ? 'checked' : '' }}
                                                       class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                                <span class="ml-2 text-sm text-gray-700">WiFi</span>
                                            </label>
                                        </div>
                                        <div>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="features[]" value="child_seat" 
                                                       {{ Auth::user()->driver->vehicle->hasFeature('child_seat') ? 'checked' : '' }}
                                                       class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                                <span class="ml-2 text-sm text-gray-700">Child Seat</span>
                                            </label>
                                        </div>
                                        <div>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="features[]" value="usb_charger" 
                                                       {{ Auth::user()->driver->vehicle->hasFeature('usb_charger') ? 'checked' : '' }}
                                                       class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                                <span class="ml-2 text-sm text-gray-700">USB Charger</span>
                                            </label>
                                        </div>
                                        <div>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="features[]" value="pet_friendly" 
                                                       {{ Auth::user()->driver->vehicle->hasFeature('pet_friendly') ? 'checked' : '' }}
                                                       class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                                <span class="ml-2 text-sm text-gray-700">Pet Friendly</span>
                                            </label>
                                        </div>
                                        <div>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="features[]" value="luggage_carrier" 
                                                       {{ Auth::user()->driver->vehicle->hasFeature('luggage_carrier') ? 'checked' : '' }}
                                                       class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                                <span class="ml-2 text-sm text-gray-700">Luggage Carrier</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="border-t pt-6 flex justify-end">
                                    <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-md shadow-sm text-sm font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Update Vehicle
                                    </button>
                                </div>
                            </div>
                        </form>
                    @else
                        <div class="bg-gray-50 rounded-md p-6 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <p class="text-gray-500 font-medium">No vehicle information found</p>
                            <p class="text-sm text-gray-400 mt-1">Please complete driver registration to add vehicle details</p>
                        </div>
                    @endif
                </div>
                
                <!-- Account & Security Tab -->
                <div id="account-security-tab" class="tab-content p-6 hidden">
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-medium">Password Reset</h3>
                                <p class="text-sm text-gray-500 mt-1">Update your password to keep your account secure</p>
                            </div>
                            
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                                <input type="password" id="current_password" name="current_password" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                                <input type="password" id="password" name="password" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <p class="mt-1 text-xs text-gray-500">Password must be at least 8 characters</p>
                            </div>
                            
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                                <input type="password" id="password_confirmation" name="password_confirmation" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <div class="border-t pt-6 flex justify-end">
                                <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-md shadow-sm text-sm font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Update Password
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    <div class="mt-12 pt-6 border-t border-gray-200">
                        <div>
                            <h3 class="text-lg font-medium text-red-600">Danger Zone</h3>
                            <p class="text-sm text-gray-500 mt-1">Permanently delete your account</p>
                        </div>
                        
                        <div class="mt-4">
                            <button type="button" id="delete-account-button" class="inline-flex items-center px-4 py-2 border border-red-600 text-sm font-medium rounded-md text-red-600 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                Delete Account
                            </button>
                        </div>
                        
                        <!-- Delete Account Confirmation Modal (Hidden by default) -->
                        <div id="delete-account-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
                            <div class="flex items-center justify-center min-h-screen p-4">
                                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                                
                                <div class="bg-white rounded-lg px-4 pt-5 pb-4 text-center overflow-hidden shadow-xl transform transition-all sm:max-w-sm sm:w-full sm:p-6">
                                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                                        <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    </div>
                                    <div class="mt-3 text-center sm:mt-5">
                                        <h3 class="text-lg leading-6 font-medium text-gray-900">Delete Account</h3>
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-500">
                                                Are you sure you want to delete your account? This action cannot be undone and all your data will be permanently removed.
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <form method="POST" action="{{ route('profile.destroy') }}" class="mt-5 sm:mt-6">
                                        @csrf
                                        @method('delete')
                                        
                                        <div class="mb-4">
                                            <label for="password_confirm_delete" class="block text-sm font-medium text-gray-700 mb-1 text-left">Confirm your password</label>
                                            <input type="password" id="password_confirm_delete" name="password" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500" required>
                                        </div>
                                        
                                        <div class="sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:col-start-2 sm:text-sm">
                                                Delete Account
                                            </button>
                                            <button type="button" id="cancel-delete-account" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                                                Cancel
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- JavaScript for functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile menu toggle
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            const closeMobileMenuButton = document.getElementById('close-mobile-menu');
            
            if (mobileMenuButton) {
                mobileMenuButton.addEventListener('click', function() {
                    mobileMenu.classList.remove('translate-x-full');
                });
            }
            
            if (closeMobileMenuButton) {
                closeMobileMenuButton.addEventListener('click', function() {
                    mobileMenu.classList.add('translate-x-full');
                });
            }
            
            // Profile dropdown toggle
            const profileButton = document.getElementById('profile-button');
            const profileDropdown = document.getElementById('profile-dropdown');
            
            if (profileButton) {
                profileButton.addEventListener('click', function() {
                    profileDropdown.classList.toggle('hidden');
                });
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function(event) {
                    if (!profileButton.contains(event.target) && !profileDropdown.contains(event.target)) {
                        profileDropdown.classList.add('hidden');
                    }
                });
            }
            
            // Tab switching
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');
            
            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const tabName = this.dataset.tab;
                    
                    // Update tab buttons
                    tabButtons.forEach(btn => {
                        if (btn.dataset.tab === tabName) {
                            btn.classList.add('text-blue-600', 'border-blue-600');
                            btn.classList.remove('text-gray-500', 'border-transparent', 'hover:text-gray-700', 'hover:border-gray-300');
                        } else {
                            btn.classList.remove('text-blue-600', 'border-blue-600');
                            btn.classList.add('text-gray-500', 'border-transparent', 'hover:text-gray-700', 'hover:border-gray-300');
                        }
                    });
                    
                    // Show selected tab content
                    tabContents.forEach(content => {
                        if (content.id === tabName + '-tab') {
                            content.classList.remove('hidden');
                        } else {
                            content.classList.add('hidden');
                        }
                    });
                });
            });
            
            // Profile picture upload
            const uploadButton = document.getElementById('upload-button');
            const profilePictureInput = document.getElementById('profile_picture');
            
            if (uploadButton && profilePictureInput) {
                uploadButton.addEventListener('click', function() {
                    profilePictureInput.click();
                });
            }
            
            // Vehicle photo upload
            const uploadVehicleButton = document.getElementById('upload-vehicle-button');
            const vehiclePhotoInput = document.getElementById('vehicle_photo');
            
            if (uploadVehicleButton && vehiclePhotoInput) {
                uploadVehicleButton.addEventListener('click', function() {
                    vehiclePhotoInput.click();
                });
            }
            
            // Delete account modal
            const deleteAccountButton = document.getElementById('delete-account-button');
            const deleteAccountModal = document.getElementById('delete-account-modal');
            const cancelDeleteAccount = document.getElementById('cancel-delete-account');
            
            if (deleteAccountButton && deleteAccountModal) {
                deleteAccountButton.addEventListener('click', function() {
                    deleteAccountModal.classList.remove('hidden');
                });
            }
            
            if (cancelDeleteAccount) {
                cancelDeleteAccount.addEventListener('click', function() {
                    deleteAccountModal.classList.add('hidden');
                });
            }
            
            // Vehicle type validation for women
            const vehicleTypeSelect = document.getElementById('type');
            if (vehicleTypeSelect) {
                vehicleTypeSelect.addEventListener('change', function() {
                    if (this.value === 'women') {
                        const gender = "{{ Auth::user()->gender }}";
                        const womenOnlyDriver = document.getElementById('women_only_driver')?.checked || false;
                        
                        if (gender !== 'female' || !womenOnlyDriver) {
                            alert('Only female drivers who have enabled the women-only driver option can select the Women vehicle type.');
                            this.value = "{{ Auth::user()->driver->vehicle->type }}";
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>