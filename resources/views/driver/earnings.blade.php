<!-- driver/earnings.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>inTime - Driver Earnings</title>
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Chart.js for earnings visualization -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                <a href="{{ route('driver.earnings') }}" class="font-medium text-blue-600 transition">Earnings</a>
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
                    <a href="{{ route('driver.profile.private') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile Settings</a>
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
                        <a href="{{ route('driver.earnings') }}" class="font-medium px-3 py-2 rounded-md bg-blue-50 text-blue-600">Earnings</a>
                        <a href="{{ route('driver.profile.private') }}" class="font-medium px-3 py-2 rounded-md hover:bg-gray-100">Profile Settings</a>
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
        
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Left Column - Earnings Summary -->
            <div class="w-full lg:w-1/3 flex flex-col gap-6">
                <!-- Earnings Overview Card -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold mb-6">Earnings Overview</h2>
                    
                    <div class="space-y-4">
                        <div class="border-b pb-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Today</span>
                                <span class="font-medium text-lg">MAD {{ number_format($stats['today'], 2) }}</span>
                            </div>
                            <div class="mt-1 text-xs text-gray-500 flex justify-end">
                                {{ $stats['completed_rides'] > 0 ? 'From ' . number_format($stats['today'] / $stats['completed_rides'], 2) . ' MAD per ride on average' : 'No rides today' }}
                            </div>
                        </div>
                        
                        <div class="border-b pb-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">This Week</span>
                                <span class="font-medium text-lg">MAD {{ number_format($stats['week'], 2) }}</span>
                            </div>
                        </div>
                        
                        <div class="border-b pb-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">This Month</span>
                                <span class="font-medium text-lg">MAD {{ number_format($stats['month'], 2) }}</span>
                            </div>
                        </div>
                        
                        <div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">All Time</span>
                                <span class="font-medium text-lg">MAD {{ number_format($stats['total'], 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Driver Stats Card -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold mb-4">Performance Summary</h2>
                    
                    <div class="flex flex-col space-y-4">
                        <div class="flex items-center">
                            <div class="bg-blue-100 p-3 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500">Completed Rides</p>
                                <p class="font-medium">{{ number_format($stats['completed_rides']) }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center">
                            <div class="bg-green-100 p-3 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500">Average Fare</p>
                                <p class="font-medium">
                                    MAD {{ $stats['completed_rides'] > 0 ? number_format($stats['total'] / $stats['completed_rides'], 2) : '0.00' }}
                                </p>
                            </div>
                        </div>
                        
                        <div class="flex items-center">
                            <div class="bg-yellow-100 p-3 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500">Rating</p>
                                <div class="flex items-center">
                                    <span class="font-medium mr-1">{{ number_format($stats['avg_rating'], 1) }}</span>
                                    <div class="flex">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= round($stats['avg_rating']))
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-300" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Rides Card -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold mb-4">Recent Rides</h2>
                    
                    @if(count($recentRides) === 0)
                        <div class="bg-gray-50 rounded-md p-6 text-center">
                            <p class="text-gray-500">No recent rides</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($recentRides as $ride)
                                <div class="border-b pb-3 last:border-0 last:pb-0">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-medium">{{ $ride->passenger->user->name }}</p>
                                            <p class="text-sm text-gray-500">{{ $ride->dropoff_time->format('M d, Y g:i A') }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-medium">MAD {{ number_format($ride->price ?? $ride->ride_cost ?? 0, 2) }}</p>
                                            <p class="text-sm text-gray-500">{{ number_format($ride->distance_in_km ?? 0, 1) }} km</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-4 text-center">
                            <a href="{{ route('driver.history') }}" class="text-blue-600 text-sm font-medium hover:underline">
                                View All Rides
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Right Column - Earnings Chart -->
            <div class="w-full lg:w-2/3 flex flex-col gap-6">
                <!-- Earnings Chart Card -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold">Earnings History</h2>
                        
                        <div>
                            <select id="chart-period" class="border rounded-md py-2 px-4 focus:ring-blue-500 focus:border-blue-500">
                                <option value="30">Last 30 Days</option>
                                <option value="7">Last 7 Days</option>
                                <option value="90">Last 3 Months</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Chart Container -->
                    <div>
                        <canvas id="earningsChart" height="300"></canvas>
                    </div>
                </div>
                
                <!-- Earnings by Day of Week -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold mb-6">Earnings by Day of Week</h2>
                    
                    <div>
                        <canvas id="weekdayChart" height="200"></canvas>
                    </div>
                </div>
                
                <!-- Earnings Distribution Card -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold mb-4">Earnings Distribution</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Vehicle Type Distribution -->
                        <div>
                            <h3 class="text-lg font-medium mb-3">By Vehicle Type</h3>
                            <canvas id="vehicleTypeChart" height="200"></canvas>
                        </div>
                        
                        <!-- Time of Day Distribution -->
                        <div>
                            <h3 class="text-lg font-medium mb-3">By Time of Day</h3>
                            <canvas id="timeOfDayChart" height="200"></canvas>
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
            
            // Daily earnings chart
            const dailyEarningsData = @json($dailyEarnings);
            const dates = dailyEarningsData.map(item => item.date);
            const earnings = dailyEarningsData.map(item => item.total);
            
            // Create daily earnings chart
            const earningsCtx = document.getElementById('earningsChart').getContext('2d');
            const earningsChart = new Chart(earningsCtx, {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: [{
                        label: 'Daily Earnings (MAD)',
                        data: earnings,
                        backgroundColor: 'rgba(59, 130, 246, 0.2)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 2,
                        pointBackgroundColor: 'rgba(59, 130, 246, 1)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 1,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        tension: 0.1,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.7)',
                            padding: 10,
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            cornerRadius: 4,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return `MAD ${context.parsed.y.toFixed(2)}`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                maxRotation: 45,
                                minRotation: 45
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'MAD ' + value;
                                }
                            }
                        }
                    }
                }
            });
            
            // Chart period selector
            const chartPeriodSelect = document.getElementById('chart-period');
            if (chartPeriodSelect) {
                chartPeriodSelect.addEventListener('change', function() {
                    const period = parseInt(this.value);
                    
                    // Filter data based on selected period
                    const filteredDates = dates.slice(-period);
                    const filteredEarnings = earnings.slice(-period);
                    
                    // Update chart data
                    earningsChart.data.labels = filteredDates;
                    earningsChart.data.datasets[0].data = filteredEarnings;
                    earningsChart.update();
                });
            }
            
            // Sample data for other charts (replace with actual data in production)
            
            // Weekday chart
            const weekdayCtx = document.getElementById('weekdayChart').getContext('2d');
            new Chart(weekdayCtx, {
                type: 'bar',
                data: {
                    labels: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                    datasets: [{
                        label: 'Average Earnings',
                        data: [350, 380, 400, 430, 500, 550, 420],
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.6)',
                            'rgba(59, 130, 246, 0.6)',
                            'rgba(59, 130, 246, 0.6)',
                            'rgba(59, 130, 246, 0.6)',
                            'rgba(59, 130, 246, 0.6)',
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(59, 130, 246, 0.7)'
                        ],
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `MAD ${context.parsed.y.toFixed(2)}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'MAD ' + value;
                                }
                            }
                        }
                    }
                }
            });
            
            // Vehicle type chart
            const vehicleTypeCtx = document.getElementById('vehicleTypeChart').getContext('2d');
            new Chart(vehicleTypeCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Comfort', 'Share', 'Women', 'WAV', 'Black'],
                    datasets: [{
                        data: [40, 25, 15, 10, 10],
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(16, 185, 129, 0.8)',
                            'rgba(236, 72, 153, 0.8)',
                            'rgba(245, 158, 11, 0.8)',
                            'rgba(31, 41, 55, 0.8)'
                        ],
                        borderColor: 'white',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
            
            // Time of day chart
            const timeOfDayCtx = document.getElementById('timeOfDayChart').getContext('2d');
            new Chart(timeOfDayCtx, {
                type: 'pie',
                data: {
                    labels: ['Morning (6AM-12PM)', 'Afternoon (12PM-6PM)', 'Evening (6PM-12AM)', 'Night (12AM-6AM)'],
                    datasets: [{
                        data: [30, 35, 25, 10],
                        backgroundColor: [
                            'rgba(251, 191, 36, 0.8)',
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(79, 70, 229, 0.8)',
                            'rgba(31, 41, 55, 0.8)'
                        ],
                        borderColor: 'white',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>