<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>inTime - Driver Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css">
     <!-- Include Tailwind CSS -->
     @vite('resources/css/app.css')
</head>
<body class="bg-gray-50">
    <!-- Header/Navigation -->
    <header class="px-4 py-4 flex items-center justify-between bg-white shadow-sm">
        <div class="flex items-center space-x-8">
            <!-- Logo -->
            <a href="{{ route('driver.dashboard') }}" class="text-2xl font-bold">inTime</a>
            
            <!-- Navigation Links -->
            <nav class="hidden md:flex space-x-6">
                <a href="{{ route('driver.dashboard') }}" class="font-medium text-black">Create Ride</a>
                <a href="{{ route('driver.attention.needed') }}" class="font-medium">Attention Needed 
                @if(Auth::user()->driver && Auth::user()->driver->rides()->where('reservation_status', 'pending')->count() > 0)
                    <span class="bg-red-500 text-white rounded-full px-2 py-0.5 text-xs">
                        {{ Auth::user()->driver->rides()->where('reservation_status', 'pending')->count() }}
                    </span>
                @endif
                </a>
                <a href="{{ route('driver.awaiting.rides') }}" class="font-medium">Awaiting Rides</a>
                <a href="{{ route('driver.profile') }}" class="font-medium">My Profile</a>
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

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <div class="max-w-3xl mx-auto">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold">Create Ride Availability</h1>
            </div>
            
            <!-- Create Ride Form -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6">
                    <form action="{{ route('driver.store.ride') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <!-- Basic Ride Info -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium mb-4">Ride Details</h3>
                            
                            <!-- Date and Time Picker -->
                            <div class="mb-4">
                                <label for="departure_time" class="block text-sm font-medium text-gray-700 mb-1">Availability Date & Time</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <input type="text" id="departure_time" name="departure_time" placeholder="Select date and time" class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-md focus:ring-black focus:border-black @error('departure_time') border-red-500 @enderror">
                                    @error('departure_time')
                                        <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Pickup Location -->
                            <div class="mb-4">
                                <label for="pickup_location" class="block text-sm font-medium text-gray-700 mb-1">Your Starting Location</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <div class="h-2 w-2 rounded-full bg-green-500"></div>
                                    </div>
                                    <input type="text" id="pickup_location" name="pickup_location" placeholder="Enter your starting location" class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-md focus:ring-black focus:border-black @error('pickup_location') border-red-500 @enderror">
                                    @error('pickup_location')
                                        <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Available Seats -->
                            <div class="mb-4">
                                <label for="available_seats" class="block text-sm font-medium text-gray-700 mb-1">Available Seats</label>
                                <select id="available_seats" name="available_seats" class="block w-full py-3 px-3 border border-gray-300 rounded-md focus:ring-black focus:border-black @error('available_seats') border-red-500 @enderror">
                                    <option value="1">1 seat</option>
                                    <option value="2">2 seats</option>
                                    <option value="3">3 seats</option>
                                    <option value="4">4 seats</option>
                                </select>
                                @error('available_seats')
                                    <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Price -->
                            <div class="mb-4">
                                <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Price (DH)</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500">DH</span>
                                    </div>
                                    <input type="number" id="price" name="price" placeholder="Enter price" class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-md focus:ring-black focus:border-black @error('price') border-red-500 @enderror">
                                    @error('price')
                                        <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Vehicle Info -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium mb-4">Vehicle Information</h3>
                            
                            <!-- Select Vehicle if exists -->
                            @if(Auth::user()->driver && Auth::user()->driver->vehicles()->count() > 0)
                                <div class="mb-4">
                                    <label for="vehicle_id" class="block text-sm font-medium text-gray-700 mb-1">Select Vehicle</label>
                                    <select id="vehicle_id" name="vehicle_id" class="block w-full py-3 px-3 border border-gray-300 rounded-md focus:ring-black focus:border-black">
                                        @foreach(Auth::user()->driver->vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}">{{ $vehicle->make }} {{ $vehicle->model }} ({{ $vehicle->plate_number }})</option>
                                        @endforeach
                                        <option value="new">+ Add New Vehicle</option>
                                    </select>
                                </div>
                            @endif
                            
                            <div id="new-vehicle-section" class="{{ Auth::user()->driver && Auth::user()->driver->vehicles()->count() > 0 ? 'hidden' : '' }}">
                                    <!-- Vehicle form fields -->
                                </div>
                                    <label for="vehicle_type" class="block text-sm font-medium text-gray-700 mb-1">Vehicle Type</label>
                                    <select id="vehicle_type" name="vehicle_type" class="block w-full py-3 px-3 border border-gray-300 rounded-md focus:ring-black focus:border-black">
                                        <option value="share">Share</option>
                                        <option value="comfort">Comfort</option>
                                        <option value="Black">Black (Premium)</option>
                                        <option value="WAV">WAV (Wheelchair Accessible)</option>
                                    </select>
                                </div>
                                
                                <!-- Vehicle Photo -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Vehicle Photo</label>
                                    <div class="flex items-center justify-center w-full">
                                        <label class="flex flex-col w-full h-32 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                            <div class="flex flex-col items-center justify-center pt-7">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                                </svg>
                                                <p class="pt-1 text-sm text-gray-400">Upload a photo of your vehicle</p>
                                            </div>
                                            <input type="file" name="vehicle_photo" class="hidden">
                                        </label>
                                    </div>
                                </div>
                                
                                <!-- Vehicle Make and Model -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label for="make" class="block text-sm font-medium text-gray-700 mb-1">Make</label>
                                        <input type="text" id="make" name="make" placeholder="e.g. Toyota" class="block w-full px-3 py-3 border border-gray-300 rounded-md focus:ring-black focus:border-black">
                                    </div>
                                    <div>
                                        <label for="model" class="block text-sm font-medium text-gray-700 mb-1">Model</label>
                                        <input type="text" id="model" name="model" placeholder="e.g. Camry" class="block w-full px-3 py-3 border border-gray-300 rounded-md focus:ring-black focus:border-black">
                                    </div>
                                </div>
                                
                                <!-- Year and Color -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label for="year" class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                                        <input type="number" id="year" name="year" placeholder="e.g. 2020" class="block w-full px-3 py-3 border border-gray-300 rounded-md focus:ring-black focus:border-black">
                                    </div>
                                    <div>
                                        <label for="color" class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                                        <input type="text" id="color" name="color" placeholder="e.g. Black" class="block w-full px-3 py-3 border border-gray-300 rounded-md focus:ring-black focus:border-black">
                                    </div>
                                </div>
                                
                                <!-- Plate Number and Capacity -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label for="plate_number" class="block text-sm font-medium text-gray-700 mb-1">Plate Number</label>
                                        <input type="text" id="plate_number" name="plate_number" placeholder="e.g. ABC123" class="block w-full px-3 py-3 border border-gray-300 rounded-md focus:ring-black focus:border-black">
                                    </div>
                                    <div>
                                        <label for="capacity" class="block text-sm font-medium text-gray-700 mb-1">Capacity</label>
                                        <select id="capacity" name="capacity" class="block w-full py-3 px-3 border border-gray-300 rounded-md focus:ring-black focus:border-black">
                                            <option value="1">1 person</option>
                                            <option value="2">2 people</option>
                                            <option value="3">3 people</option>
                                            <option value="4">4 people</option>
                                            <option value="5">5 people</option>
                                            <option value="6">6 people</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <!-- Insurance and Registration Expiry -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label for="insurance_expiry" class="block text-sm font-medium text-gray-700 mb-1">Insurance Expiry</label>
                                        <input type="text" id="insurance_expiry" name="insurance_expiry" placeholder="Select date" class="block w-full px-3 py-3 border border-gray-300 rounded-md focus:ring-black focus:border-black">
                                    </div>
                                    <div>
                                        <label for="registration_expiry" class="block text-sm font-medium text-gray-700 mb-1">Registration Expiry</label>
                                        <input type="text" id="registration_expiry" name="registration_expiry" placeholder="Select date" class="block w-full px-3 py-3 border border-gray-300 rounded-md focus:ring-black focus:border-black">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Additional Notes -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium mb-4">Additional Notes</h3>
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes for Passengers</label>
                                <textarea id="notes" name="notes" rows="3" placeholder="Any additional information passengers should know..." class="block w-full px-3 py-3 border border-gray-300 rounded-md focus:ring-black focus:border-black"></textarea>
                            </div>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="text-right">
                            <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-black hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black">
                                Create Ride
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Footer -->
    <footer class="bg-white py-6 mt-8">
        <div class="container mx-auto px-4">
            <p class="text-center text-gray-500 text-sm">© 2025 inTime. All rights reserved.</p>
        </div>
    </footer>
    
    <!-- JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize date pickers
            flatpickr("#departure_time", {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                minDate: "today"
            });
            
            flatpickr("#insurance_expiry", {
                dateFormat: "Y-m-d",
                minDate: "today"
            });
            
            flatpickr("#registration_expiry", {
                dateFormat: "Y-m-d",
                minDate: "today"
            });
            
            // Handle vehicle selection toggle
            const vehicleSelect = document.getElementById('vehicle_id');
            const newVehicleSection = document.getElementById('new-vehicle-section');
            
            if(vehicleSelect) {
                vehicleSelect.addEventListener('change', function() {
                    if(this.value === 'new') {
                        newVehicleSection.classList.remove('hidden');
                    } else {
                        newVehicleSection.classList.add('hidden');
                    }
                });
            }
        });
    </script>
</body>
</html>