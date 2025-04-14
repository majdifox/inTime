<!-- resources/views/active-ride.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>inTime - My Ride</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-50">
    <!-- Header/Navigation -->
    <header class="px-4 py-4 flex items-center justify-between bg-white shadow-sm">
        <div class="flex items-center space-x-8">
            <!-- Logo -->
            <a href="#" class="text-2xl font-bold">inTime</a>
            
            <!-- Navigation Links -->
            <nav class="hidden md:flex space-x-6">
                <a href="#" class="font-medium">Ride</a>
                <a href="#" class="font-medium">Drive</a>
                <a href="#" class="font-medium text-black">My Rides</a>
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
                <h1 class="text-2xl font-bold">My Ride</h1>
                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">In Progress</span>
            </div>
            
            <!-- Ride Info Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                <div class="p-6">
                    <!-- Driver Info -->
                    <div class="flex items-center mb-6">
                        <div class="h-16 w-16 rounded-full bg-gray-300 overflow-hidden mr-4">
                            <img src="/api/placeholder/64/64" alt="Driver" class="h-full w-full object-cover">
                        </div>
                        <div>
                            <h3 class="text-lg font-medium">John Doe</h3>
                            <p class="text-gray-500">Toyota Camry • ABC 123</p>
                            <div class="flex items-center mt-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                <span class="text-sm text-gray-600 ml-1">4.8</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Ride Progress Tracker -->
                    <div class="mb-8">
                        <div class="relative">
                            <!-- Progress Bar -->
                            <div class="overflow-hidden h-2 mb-2 text-xs flex rounded bg-gray-200">
                                <div class="w-1/2 shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-black"></div>
                            </div>
                            
                            <!-- Progress Steps -->
                            <div class="flex justify-between text-xs text-gray-600">
                                <div class="w-1/3 text-left">
                                    <div class="bg-black h-4 w-4 rounded-full mb-1 mx-auto"></div>
                                    <span>Pick Up</span>
                                </div>
                                <div class="w-1/3 text-center">
                                    <div class="bg-black h-4 w-4 rounded-full mb-1 mx-auto"></div>
                                    <span>In Progress</span>
                                </div>
                                <div class="w-1/3 text-right">
                                    <div class="bg-gray-300 h-4 w-4 rounded-full mb-1 mx-auto"></div>
                                    <span>Drop Off</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Ride Details -->
                    <div class="space-y-4">
                        <div class="flex">
                            <div class="mr-3">
                                <div class="h-6 w-6 rounded-full bg-green-100 flex items-center justify-center">
                                    <div class="h-2 w-2 rounded-full bg-green-500"></div>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Pickup Location</p>
                                <p class="font-medium">City Center, Main Street</p>
                            </div>
                        </div>
                        
                        <div class="flex">
                            <div class="mr-3">
                                <div class="h-6 w-6 rounded-full bg-red-100 flex items-center justify-center">
                                    <div class="h-2 w-2 rounded-full bg-red-500"></div>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Dropoff Location</p>
                                <p class="font-medium">Airport Terminal 2</p>
                            </div>
                        </div>
                        
                        <div class="flex">
                            <div class="mr-3">
                                <div class="h-6 w-6 rounded-full bg-blue-100 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Estimated Time of Arrival</p>
                                <p class="font-medium">15 minutes</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Map Preview -->
                <div class="h-48 bg-blue-200">
                    <div class="h-full flex items-center justify-center">
                        <span class="text-blue-800 font-medium">Map Preview</span>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex border-t border-gray-200">
                    <button class="flex-1 py-4 text-center border-r border-gray-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mx-auto mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <span class="text-sm">Call Driver</span>
                    </button>
                    <button class="flex-1 py-4 text-center border-r border-gray-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mx-auto mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <span class="text-sm">Message</span>
                    </button>
                    <button class="flex-1 py-4 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mx-auto mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm">Help</span>
                    </button>
                </div>
            </div>
        </div>
    </main>
    
    @vite('resources/js/app.js')
</body>
</html>