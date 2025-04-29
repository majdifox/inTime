<header class="px-4 py-4 flex items-center justify-between bg-white shadow-sm">
    <div class="flex items-center space-x-8">
        <!-- Logo -->
        <a href="{{ route('passenger.dashboard') }}" class="text-2xl font-bold">inTime</a>
        
        <!-- Navigation Links -->
        <nav class="hidden md:flex space-x-6">
            @auth
                @if(Auth::user()->role === 'passenger')
                    <a href="{{ route('passenger.dashboard') }}" class="font-medium {{ request()->routeIs('passenger.dashboard') ? 'text-blue-600' : '' }}">Dashboard</a>
                    <a href="{{ route('passenger.book') }}" class="font-medium {{ request()->routeIs('passenger.book') ? 'text-blue-600' : '' }}">Book Ride</a>
                    <a href="{{ route('passenger.history') }}" class="font-medium {{ request()->routeIs('passenger.history') ? 'text-blue-600' : '' }}">Ride History</a>
                    <a href="{{ route('passenger.profile.private') }}" class="font-medium {{ request()->routeIs('passenger.profile.*') ? 'text-blue-600' : '' }}">My Profile</a>
                @elseif(Auth::user()->role === 'driver')
                    <a href="{{ route('driver.dashboard') }}" class="font-medium {{ request()->routeIs('driver.dashboard') ? 'text-blue-600' : '' }}">Dashboard</a>
                    <a href="{{ route('driver.history') }}" class="font-medium {{ request()->routeIs('driver.history') ? 'text-blue-600' : '' }}">Ride History</a>
                    <a href="{{ route('driver.profile.private') }}" class="font-medium {{ request()->routeIs('driver.profile.*') ? 'text-blue-600' : '' }}">My Profile</a>
                @endif
            @endauth
        </nav>
    </div>
    
    <!-- Profile Dropdown -->
    @auth
        <div class="relative ml-3">
            <div>
                <button type="button" class="flex rounded-full bg-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2" id="profile-menu-button" aria-expanded="false" aria-haspopup="true">
                    <span class="sr-only">Open user menu</span>
                    <div class="h-10 w-10 rounded-full bg-gray-300 overflow-hidden">
                        @if(Auth::user()->profile_picture)
                            <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Profile" class="h-full w-full object-cover">
                        @else
                            <div class="h-full w-full flex items-center justify-center bg-gray-400 text-white">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                </button>
            </div>

            <!-- Dropdown menu with fixed positioning -->
            <div class="hidden fixed top-16 right-4 z-[1000] w-48 rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="profile-menu-button" tabindex="-1" id="profile-dropdown-menu">
                <!-- Active: "bg-gray-100", Not Active: "" -->
                @if(Auth::user()->role === 'passenger')
                    <a href="{{ route('passenger.profile.private') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">My Profile</a>
                @elseif(Auth::user()->role === 'driver')
                    <a href="{{ route('driver.profile.private') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">My Profile</a>
                @endif
                <form method="POST" action="{{ route('logout') }}" class="block">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    @else
        <div>
            <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900 mr-4">Login</a>
            <a href="{{ route('register') }}" class="bg-black text-white py-2 px-6 rounded-md font-medium hover:bg-gray-800 transition">Register</a>
        </div>
    @endauth
</header>
