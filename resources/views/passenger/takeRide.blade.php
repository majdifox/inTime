<!-- Laravel Blade template with Tailwind CSS classes -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>inTime - Get there faster</title>
    <!-- Include Tailwind CSS -->
    @vite('resources/css/app.css')
    <!-- Include Flatpickr for date/time picker -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>


<body class="bg-white">
    <!-- Header/Navigation -->
    <header class="px-4 py-4 flex items-center justify-between bg-white">
        <div class="flex items-center space-x-8">
            <!-- Logo -->
            <a href="#" class="text-2xl font-bold">inTime</a>
            
            <!-- Navigation Links -->
            <nav class="hidden md:flex space-x-6">
                <a href="route('passenger.activeRide')" class="font-medium">My Active Ride</a>
                <a href="#" class="font-medium">My upcoming Ride</a>
                <div class="relative">
                    <a href="#" class="font-medium flex items-center">
                        About
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </a>
                </div>
            </nav>
        </div>
        
        <!-- Right Side Navigation -->
        <div class="flex items-center space-x-4">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                </svg>
                <span>EN</span>
            </div>
            <a href="#" class="font-medium">Help</a>
              <!-- Settings Dropdown -->
              <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="font-medium flex items-center">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="py-1 bg-white rounded-md shadow-lg">
                            <x-dropdown-link :href="route('profile.edit')" class="block px-4 py-2 text-sm text-black hover:bg-black hover:bg-opacity-5">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();"
                                        class="block px-4 py-2 text-sm text-red-600 hover:bg-black hover:bg-opacity-5">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
    </header>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
    </header>

    <!-- Main Content -->
    <main class="px-4 py-6">
        <div class="flex flex-col md:flex-row">
            <!-- Left Side Form (Increased width) -->
            <div class="w-full md:w-3/4 px-4 py-10">
                <div class="max-w-2xl">
                    <h1 class="text-5xl font-bold mb-4">Get there faster with inTime</h1>
                    
                    <!-- Search Bar -->
                    <div class="mt-6 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" placeholder="Search for destinations, places, or addresses" class="block w-full pl-10 pr-3 py-3 bg-gray-100 border-0 rounded-md focus:ring-0 focus:outline-none">
                    </div>
                    
                    <!-- Ride Form -->
                    <div class="mt-10 space-y-4">
                        <!-- Pickup Location -->
                        <div class="relative bg-gray-100 rounded-md">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <div class="h-2 w-2 rounded-full bg-black"></div>
                            </div>
                            <input type="text" placeholder="Pickup location" class="block w-full pl-10 pr-12 py-4 bg-gray-100 border-0 rounded-md focus:ring-0 focus:outline-none">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                        
                        <!-- Dropoff Location -->
                        <div class="relative bg-gray-100 rounded-md">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <div class="h-4 w-1 rounded-sm bg-black"></div>
                            </div>
                            <input type="text" placeholder="Dropoff location" class="block w-full pl-10 pr-12 py-4 bg-gray-100 border-0 rounded-md focus:ring-0 focus:outline-none">
                        </div>
                        
                        <!-- Enhanced Date and Time with Calendar -->
                        <div class="flex space-x-4">
                            <div class="relative w-full bg-gray-100 rounded-md">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <input type="text" id="datetimePicker" placeholder="Select date and time" class="block w-full pl-10 pr-3 py-4 bg-gray-100 border-0 rounded-md focus:ring-0 focus:outline-none">
                            </div>
                        </div>
                        
                        <!-- See Prices Button -->
                        <div>
                            <button type="button" id="seePricesBtn" class="w-40 bg-black text-white py-3 px-6 rounded-md font-medium">
                                See prices
                            </button>
                        </div>
                    </div>
                    
                    <!-- Small Map for Mobile/Tablet View (Hidden on Desktop) -->
                    <div class="md:hidden mt-6">
                        <div class="h-48 bg-blue-200 rounded-lg relative">
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="text-lg font-bold">Morocco</span>
                            </div>
                            
                            <!-- Zoom controls -->
                            <div class="absolute bottom-2 right-2 flex flex-col">
                                <button class="bg-white p-1 rounded-t-md border border-gray-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                </button>
                                <button class="bg-white p-1 rounded-b-md border-t-0 border border-gray-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Results Container (initially hidden) -->
                    <div id="resultsContainer" class="mt-8 hidden">
                        <h2 class="text-xl font-bold mb-4">Available Rides</h2>
                        
                        <!-- Results List -->
                        <div class="space-y-4">
                            <!-- Result Item 1 -->
                            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition">
                                <div class="flex items-center">
                                    <!-- Driver Info -->
                                    <div class="flex-shrink-0 mr-4">
                                        <img src="/api/placeholder/48/48" alt="Driver" class="w-12 h-12 rounded-full object-cover">
                                    </div>
                                    
                                    <!-- Trip Details -->
                                    <div class="flex-grow">
                                        <div class="flex justify-between">
                                            <h3 class="font-semibold">Ahmed M.</h3>
                                            <span class="font-bold">150 DH</span>
                                        </div>
                                        <div class="text-sm text-gray-600">
                                            <div class="flex items-center mt-1">
                                                <div class="h-2 w-2 rounded-full bg-black mr-2"></div>
                                                <span>Pickup: Avenue Mohammed V</span>
                                            </div>
                                            <div class="flex items-center mt-1">
                                                <div class="h-2 w-2 rounded-full bg-gray-400 mr-2"></div>
                                                <span>Dropoff: Marrakech Train Station</span>
                                            </div>
                                        </div>
                                        <div class="flex items-center mt-2">
                                            <div class="text-sm text-gray-600">
                                                <span>⭐ 4.8</span> • <span>10:30 AM</span> • <span>20 min</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button class="w-full mt-3 bg-black text-white py-2 rounded-md font-medium">Let's Go</button>
                            </div>
                            
                            <!-- Result Item 2 -->
                            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition">
                                <div class="flex items-center">
                                    <!-- Driver Info -->
                                    <div class="flex-shrink-0 mr-4">
                                        <img src="/api/placeholder/48/48" alt="Driver" class="w-12 h-12 rounded-full object-cover">
                                    </div>
                                    
                                    <!-- Trip Details -->
                                    <div class="flex-grow">
                                        <div class="flex justify-between">
                                            <h3 class="font-semibold">Fatima L.</h3>
                                            <span class="font-bold">120 DH</span>
                                        </div>
                                        <div class="text-sm text-gray-600">
                                            <div class="flex items-center mt-1">
                                                <div class="h-2 w-2 rounded-full bg-black mr-2"></div>
                                                <span>Pickup: Avenue Mohammed V</span>
                                            </div>
                                            <div class="flex items-center mt-1">
                                                <div class="h-2 w-2 rounded-full bg-gray-400 mr-2"></div>
                                                <span>Dropoff: Marrakech Train Station</span>
                                            </div>
                                        </div>
                                        <div class="flex items-center mt-2">
                                            <div class="text-sm text-gray-600">
                                                <span>⭐ 4.9</span> • <span>10:45 AM</span> • <span>25 min</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button class="w-full mt-3 bg-black text-white py-2 rounded-md font-medium">Let's Go</button>
                            </div>
                            
                            <!-- Result Item 3 -->
                            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition">
                                <div class="flex items-center">
                                    <!-- Driver Info -->
                                    <div class="flex-shrink-0 mr-4">
                                        <img src="/api/placeholder/48/48" alt="Driver" class="w-12 h-12 rounded-full object-cover">
                                    </div>
                                    
                                    <!-- Trip Details -->
                                    <div class="flex-grow">
                                        <div class="flex justify-between">
                                            <h3 class="font-semibold">Youssef K.</h3>
                                            <span class="font-bold">180 DH</span>
                                        </div>
                                        <div class="text-sm text-gray-600">
                                            <div class="flex items-center mt-1">
                                                <div class="h-2 w-2 rounded-full bg-black mr-2"></div>
                                                <span>Pickup: Avenue Mohammed V</span>
                                            </div>
                                            <div class="flex items-center mt-1">
                                                <div class="h-2 w-2 rounded-full bg-gray-400 mr-2"></div>
                                                <span>Dropoff: Marrakech Train Station</span>
                                            </div>
                                        </div>
                                        <div class="flex items-center mt-2">
                                            <div class="text-sm text-gray-600">
                                                <span>⭐ 4.7</span> • <span>10:15 AM</span> • <span>18 min</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button class="w-full mt-3 bg-black text-white py-2 rounded-md font-medium">Let's Go</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Side Small Map (1/4 of the page, only visible on desktop) -->
            <div class="hidden md:block w-1/4 self-start sticky top-4">
                <div class="h-64 bg-blue-200 rounded-lg relative">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-lg font-bold">Morocco</span>
                    </div>
                    
                    <!-- Zoom controls -->
                    <div class="absolute bottom-4 right-4 flex flex-col">
                        <button class="bg-white p-2 rounded-t-md border border-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                        </button>
                        <button class="bg-white p-2 rounded-b-md border-t-0 border border-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Scripts -->
    @vite('resources/js/app.js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>
    
    <script>
        // Initialize Flatpickr date/time picker
        flatpickr("#datetimePicker", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            minDate: "today",
            maxDate: new Date().fp_incr(30), // Next 30 days
            defaultDate: "today",
            time_24hr: true,
            minuteIncrement: 5,
            allowInput: true,
            position: "auto",
            onChange: function(selectedDates, dateStr, instance) {
                // You can add custom logic here when date/time is selected
                console.log(dateStr);
            }
        });
        
        // Add event listener to the See Prices button
        document.getElementById('seePricesBtn').addEventListener('click', function() {
            // Show the results container
            document.getElementById('resultsContainer').classList.remove('hidden');
            
            // In a real application, you would make an AJAX call to your backend to fetch available rides
            // based on the pickup/dropoff locations and time selected by the user
            
            // Example of how you would fetch data from your Laravel backend:
            /*
            fetch('/api/rides', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    pickup: document.querySelector('input[placeholder="Pickup location"]').value,
                    dropoff: document.querySelector('input[placeholder="Dropoff location"]').value,
                    datetime: document.getElementById('datetimePicker').value
                })
            })
            .then(response => response.json())
            .then(data => {
                // Populate the results container with the fetched data
                // This would involve creating HTML elements for each ride
            });
            */
        });
    </script>
</body>
</html>