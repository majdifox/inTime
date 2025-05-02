<!-- resources/views/driver/rideHistory.blade.php -->
@extends('driver.layouts.driver')

@section('title', 'Ride History')

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
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Ride History</h1>
                
                <!-- Search and Filter Controls -->
                <div class="flex space-x-4">
                    <div class="relative">
                        <input type="text" id="search-input" placeholder="Search by location" class="border rounded-md py-2 px-4 focus:ring-blue-500 focus:border-blue-500 w-full sm:w-60">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    
                    <select id="filter-select" class="border rounded-md py-2 px-4 focus:ring-blue-500 focus:border-blue-500">
                        <option value="all">All Time</option>
                        <option value="today">Today</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                    </select>
                </div>
            </div>
            
            @if(count($completedRides) === 0)
                <div class="bg-gray-50 rounded-md p-8 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-gray-500 font-medium">You don't have any completed rides yet</p>
                    <p class="text-sm text-gray-400 mt-1">Your ride history will appear here once you complete rides</p>
                </div>
            @else
                <!-- Ride History Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date & Time
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Passenger
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Trip Details
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Distance
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Fare
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($completedRides as $ride)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $ride->dropoff_time->format('M d, Y') }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $ride->dropoff_time->format('g:i A') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 rounded-full bg-gray-200 overflow-hidden">
                                                @if($ride->passenger->user->profile_picture)
                                                    <img src="{{ asset('storage/' . $ride->passenger->user->profile_picture) }}" alt="Passenger" class="h-full w-full object-cover">
                                                @else
                                                    <div class="h-full w-full flex items-center justify-center text-gray-500 bg-gray-300">
                                                        {{ substr($ride->passenger->user->name, 0, 1) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $ride->passenger->user->name }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    @if($ride->passenger->rating)
                                                        <div class="flex items-center">
                                                            <span class="mr-1">{{ number_format($ride->passenger->rating, 1) }}</span>
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                            </svg>
                                                        </div>
                                                    @else
                                                        No rating
                                                    @endif
                                                    <a href="{{ route('passenger.public.profile', $ride->passenger->id) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                                        View Profile
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 max-w-xs">
                                            <div class="flex items-start mb-1">
                                                <div class="min-w-[20px] mr-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <circle cx="12" cy="12" r="8" stroke-width="2" />
                                                    </svg>
                                                </div>
                                                <div class="truncate">
                                                    {{ $ride->pickup_location }}
                                                </div>
                                            </div>
                                            <div class="flex items-start">
                                                <div class="min-w-[20px] mr-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    </svg>
                                                </div>
                                                <div class="truncate">
                                                    {{ $ride->dropoff_location }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            @if($ride->pickup_time && $ride->dropoff_time)
                                                {{ $ride->pickup_time->diffInMinutes($ride->dropoff_time) }} mins trip duration
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            @if(isset($ride->distance_in_km))
                                                {{ number_format($ride->distance_in_km, 1) }} km
                                            @else
                                                N/A
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            @if($ride->vehicle_type)
                                                {{ ucfirst($ride->vehicle_type) }}
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            MAD {{ number_format($ride->price ?? $ride->ride_cost ?? 0, 2) }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            @if($ride->surge_multiplier > 1)
                                                {{ number_format($ride->surge_multiplier, 1) }}x surge
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Completed
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="mt-6">
                    {{ $completedRides->links() }}
                </div>
            @endif
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
            
\
            
            // Search functionality
            const searchInput = document.getElementById('search-input');
            const rideRows = document.querySelectorAll('tbody tr');
            
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    
                    rideRows.forEach(row => {
                        const location = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                        const passenger = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                        
                        if (location.includes(searchTerm) || passenger.includes(searchTerm)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            }
            
            // Filter functionality
            const filterSelect = document.getElementById('filter-select');
            
            if (filterSelect) {
                filterSelect.addEventListener('change', function() {
                    const filterValue = this.value;
                    const now = new Date();
                    
                    rideRows.forEach(row => {
                        const dateText = row.querySelector('td:first-child').textContent;
                        const rideDate = new Date(dateText);
                        
                        let display = true;
                        
                        if (filterValue === 'today') {
                            display = rideDate.toDateString() === now.toDateString();
                        } else if (filterValue === 'week') {
                            const startOfWeek = new Date(now);
                            startOfWeek.setDate(now.getDate() - now.getDay());
                            display = rideDate >= startOfWeek;
                        } else if (filterValue === 'month') {
                            display = rideDate.getMonth() === now.getMonth() && 
                                    rideDate.getFullYear() === now.getFullYear();
                        }
                        
                        row.style.display = display ? '' : 'none';
                    });
                });
            }
        });
    </script>
@endsection