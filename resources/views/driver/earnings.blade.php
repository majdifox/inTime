<!-- resources/views/driver/earnings.blade.php -->
@extends('driver.layouts.driver')

@section('title', 'Driver Earnings')

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
        </div>
    </div>
</main>
@endsection
@section('scripts')
<!-- Chart.js for earnings visualization -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>



    <!-- JavaScript for functionality with performance optimizations -->
    <script>
// Wait for document to be fully loaded before initializing charts
document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle - delegated event handlers
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
    

    
    // Chart initialization using requestAnimationFrame for better performance
    requestAnimationFrame(() => {
        initializeCharts();
    });
    
    function initializeCharts() {
        // Daily earnings chart
        const dailyEarningsData = @json($dailyEarnings);
        const earningsCtx = document.getElementById('earningsChart');
        
        if (!earningsCtx) return; // Guard clause if element doesn't exist
        
        // Process data once
        const dates = dailyEarningsData.map(item => item.date);
        const earnings = dailyEarningsData.map(item => item.total);
        
        // Create daily earnings chart with simplified options
        const earningsChart = new Chart(earningsCtx.getContext('2d'), {
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
                    pointRadius: 3,           // Reduced from 4
                    pointHoverRadius: 5,      // Reduced from 6
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 500 // Reduced animation time
                },
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
                            minRotation: 45,
                            font: {
                                size: 10 // Smaller font size
                            },
                            maxTicksLimit: 15 // Limit the number of ticks
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'MAD ' + value;
                            },
                            font: {
                                size: 10 // Smaller font size
                            }
                        }
                    }
                }
            }
        });
        
        // Chart period selector with debounced handling
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
        
        // Create other charts with requestAnimationFrame for better performance
        requestAnimationFrame(() => {
            createWeekdayChart();
            
            // Lazy load the remaining charts when the user scrolls near them
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        createDistributionCharts();
                        observer.disconnect(); // Only need to create these once
                    }
                });
            }, {
                rootMargin: '100px' // Load when within 100px of viewport
            });
            
            const distributionSection = document.querySelector('.grid.grid-cols-1.md\\:grid-cols-2');
            if (distributionSection) {
                observer.observe(distributionSection);
            }
        });
    }
    
    function createWeekdayChart() {
        const weekdayCtx = document.getElementById('weekdayChart');
        if (!weekdayCtx) return;
        
        // Use actual data from backend or sample data for now
        const weekdayData = {
            labels: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
            values: [350, 380, 400, 430, 500, 550, 420]
        };
        
        new Chart(weekdayCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: weekdayData.labels,
                datasets: [{
                    label: 'Average Earnings',
                    data: weekdayData.values,
                    backgroundColor: 'rgba(59, 130, 246, 0.6)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 500 // Reduced animation time
                },
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
                            },
                            font: {
                                size: 10 // Smaller font
                            }
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 10 // Smaller font
                            }
                        }
                    }
                }
            }
        });
    }
    
    
    
    // Preload Chart.js to ensure it's ready
    if (typeof Chart === 'undefined') {
        const chartScript = document.createElement('script');
        chartScript.src = 'https://cdn.jsdelivr.net/npm/chart.js';
        chartScript.onload = initializeCharts;
        document.head.appendChild(chartScript);
    }
});
                    </script>
@endsection