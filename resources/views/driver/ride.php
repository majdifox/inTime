<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>inTime - Driver Dashboard</title>
    
     <!-- Include Tailwind CSS -->
     @vite('resources/css/app.css')
    <!-- Include Flatpickr for date/time picker -->
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
                <a href="#" class="font-medium text-black">Create Ride</a>
                <a href="#" class="font-medium">Attention Needed <span class="bg-red-500 text-white rounded-full px-2 py-0.5 text-xs">3</span></a>
                <a href="#" class="font-medium">Awaiting Rides</a>
                <a href="#" class="font-medium">My Profile</a>
            </nav>
        </div>
        
        <!-- User Profile -->
        <div class="flex items-center space-x-4">
            <div class="h-10 w-10 rounded-full bg-gray-300 overflow-hidden">
                <img src="/api/placeholder/40/40" alt="Profile" class="h-full w-full object-cover">
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <div class="max-w-3xl mx-auto">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold">Create New Ride</h1>
            </div>
            
            <!-- Create Ride Form -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6">
                    <form>
                        <!-- Basic Ride Info -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium mb-4">Ride Details</h3>
                            
                            <!-- Date and Time Picker -->
                            <div class="mb-4">
                                <label for="departure_time" class="block text-sm font-medium text-gray-700 mb-1">Departure Date & Time</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <input type="text" id="departure_time" name="departure_time" placeholder="Select date and time" class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-md focus:ring-black focus:border-black">
                                </div>
                            </div>
                            
                            <!-- Pickup Location -->
                            <div class="mb-4">
                                <label for="pickup_location" class="block text-sm font-medium text-gray-700 mb-1">Pickup Location</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <div class="h-2 w-2 rounded-full bg-green-500"></div>
                                    </div>
                                    <input type="text" id="pickup_location" name="pickup_location" placeholder="Enter pickup location" class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-md focus:ring-black focus:border-black">
                                </div>
                            </div>
                            
                            <!-- Dropoff Location -->
                            <div class="mb-4">
                                <label for="dropoff_location" class="block text-sm font-medium text-gray-700 mb-1">Dropoff Location</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <div class="h-2 w-2 rounded-full bg-red-500"></div>
                                    </div>
                                    <input type="text" id="dropoff_location" name="dropoff_location" placeholder="Enter dropoff location" class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-md focus:ring-black focus:border-black">
                                </div>
                            </div>
                            
                            <!-- Available Seats -->
                            <div class="mb-4">
                                <label for="available_seats" class="block text-sm font-medium text-gray-700 mb-1">Available Seats</label>
                                <select id="available_seats" name="available_seats" class="block w-full py-3 px-3 border border-gray-300 rounded-md focus:ring-black focus:border-black">
                                    <option value="1">1 seat</option>
                                    <option value="2">2 seats</option>
                                    <option value="3">3 seats</option>
                                    <option value="4">4 seats</option>
                                </select>
                            </div>
                            
                            <!-- Price -->
                            <div class="mb-4">
                                <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Price (DH)</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500">DH</span>
                                    </div>
                                    <input type="number" id="price" name="price" placeholder="Enter price" class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-md focus:ring-black focus:border-black">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Vehicle Info -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium mb-4">Vehicle Information</h3>
                            
                            <!-- Vehicle Photo -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Vehicle Photo</label>
                                <div class="flex items-center justify-center w-full">
                                    <label class="flex flex-col w-full h-32 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                        <div class="flex flex-col items-center justify-center pt-7">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <p class="pt-1 text-sm tracking-wider text-gray-400 group-hover:text-gray-600">Upload vehicle photo</p>
                                        </div>
                                        <input type="file" class="opacity-0" />
                                    </label>
                                </div>
                            </div>
                            
                            <!-- Vehicle Details -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="vehicle_model" class="block text-sm font-medium text-gray-700 mb-1">Vehicle Model</label>
                                    <input type="text" id="vehicle_model" name="vehicle_model" placeholder="e.g. Toyota Camry" class="block w-full px-3 py-3 border border-gray-300 rounded-md focus:ring-black focus:border-black">
                                </div>
                                <div>
                                    <label for="license_plate" class="block text-sm font-medium text-gray-700 mb-1">License Plate</label>
                                    <input type="text" id="license_plate" name="license_plate" placeholder="e.g. ABC 123" class="block w-full px-3 py-3 border border-gray-300 rounded-md focus:ring-black focus:border-black">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Additional Notes -->
                        <div class="mb-6">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Additional Notes (Optional)</label>
                            <textarea id="notes" name="notes" rows="3" placeholder="Add any additional information about your ride" class="block w-full px-3 py-3 border border-gray-300 rounded-md focus:ring-black focus:border-black"></textarea>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="mt-8">
                            <button type="submit" class="w-full bg-black text-white py-3 px-6 rounded-md font-medium hover:bg-gray-800 transition">
                                Create Ride
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>
    <script>
        // Initialize Flatpickr date/time picker
        flatpickr("#departure_time", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            minDate: "today",
            maxDate: new Date().fp_incr(30), // Next 30 days
            defaultDate: new Date().fp_incr(1), // Tomorrow
            time_24hr: true,
            minuteIncrement: 5,
            allowInput: true,
            position: "auto"
        });
    </script>
</body>
</html>