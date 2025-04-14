<!-- resources/views/upcoming-rides.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>inTime - My Upcoming Rides</title>
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
        <div class="max-w-6xl mx-auto">
            <h1 class="text-2xl font-bold mb-6">My Upcoming Rides</h1>
            
            <!-- Rides Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Driver</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pick Up</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Drop Off</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Departure Time</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <!-- Sample Ride 1 (More than 1 hour before departure) -->
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">28/02/2025</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-gray-300 mr-2">
                                        <img src="/api/placeholder/32/32" alt="Driver" class="h-full w-full object-cover rounded-full">
                                    </div>
                                    <div class="text-sm font-medium text-gray-900">John Doe</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">City Center</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Airport</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">27/02/2025 14:30</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Accepted
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button class="text-red-600 hover:text-red-900">Cancel Ride</button>
                            </td>
                        </tr>
                        
                        <!-- Sample Ride 2 (Less than 1 hour before departure - can't cancel) -->
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">26/02/2025</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-gray-300 mr-2">
                                        <img src="/api/placeholder/32/32" alt="Driver" class="h-full w-full object-cover rounded-full">
                                    </div>
                                    <div class="text-sm font-medium text-gray-900">Jane Smith</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Hotel Plaza</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Shopping Mall</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">26/02/2025 15:15</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Accepted
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button class="text-gray-400 cursor-not-allowed" disabled>Cancel Ride</button>
                            </td>
                        </tr>
                        
                        <!-- Sample Ride 3 (Pending) -->
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">25/02/2025</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-gray-300 mr-2">
                                        <img src="/api/placeholder/32/32" alt="Driver" class="h-full w-full object-cover rounded-full">
                                    </div>
                                    <div class="text-sm font-medium text-gray-900">Michael Brown</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Residential Area</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">University</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">01/03/2025 08:00</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Pending
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button class="text-red-600 hover:text-red-900">Cancel Ride</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Empty State (When no rides) -->
            <div class="hidden bg-white rounded-lg shadow-md p-8 text-center mt-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No upcoming rides</h3>
                <p class="mt-1 text-gray-500">Book a ride to get started on your journey.</p>
                <button class="mt-4 px-4 py-2 bg-black text-white rounded-md">Book Now</button>
            </div>
        </div>
    </main>
    
    @vite('resources/js/app.js')
</body>
</html>