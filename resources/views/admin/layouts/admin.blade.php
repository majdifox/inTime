<!-- resources/views/admin/layouts/admin.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'inTime - Admin')</title>
    <!-- Include Tailwind CSS -->
    @vite('resources/css/app.css')
    @yield('styles')
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        main {
            flex: 1;
        }
        footer {
            position: sticky;
            bottom: 0;
            width: 100%;
        }
        .dropdown {
            position: relative;
            display: inline-block;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: #fff;
            min-width: 180px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            z-index: 1;
            border-radius: 0.375rem;
            margin-top: 0.5rem;
        }
        .show {
            display: block;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header/Navigation -->
    <header class="px-4 py-4 flex items-center justify-between bg-white shadow-sm">
        <div class="flex items-center space-x-8">
            <!-- Logo -->
            <a href="{{ route('admin.dashboard') }}" class="text-2xl font-bold">inTime</a>
            
            <!-- Navigation Links -->
            <nav class="hidden md:flex space-x-6">
                <a href="{{ route('admin.users') }}" class="font-medium {{ request()->routeIs('admin.users') ? 'text-black' : 'text-gray-500 hover:text-black' }}">User Management</a>
            </nav>
        </div>
        
        <!-- User Profile with Dropdown -->
        <div class="flex items-center space-x-4">
            <div class="dropdown">
                <div class="flex items-center space-x-2 cursor-pointer" onclick="toggleDropdown()">
                    <div class="h-10 w-10 rounded-full bg-gray-300 overflow-hidden">
                        @if(Auth::user()->profile_picture)
                            <img src="{{ Storage::url(Auth::user()->profile_picture) }}" alt="Profile" class="h-full w-full object-cover">
                        @else
                            <img src="/api/placeholder/40/40" alt="Profile" class="h-full w-full object-cover">
                        @endif
                    </div>
                    <div class="hidden md:block">
                        <p class="text-sm font-medium">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                    </div>
                    <!-- Dropdown Arrow -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </div>
                
                <!-- Dropdown Content -->
                <div id="profileDropdown" class="dropdown-content">
                   
                    <div class="border-t border-gray-100 my-1"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 001 1h12a1 1 0 001-1V7.414l-1-1V15H4V5h8.586l-1-1H4a1 1 0 00-1 1zm14.586-2l-2 2H20v14a2 2 0 01-2 2H2a2 2 0 01-2-2V3a2 2 0 012-2h12.586l3 3zM7 9a1 1 0 011-1h.01a1 1 0 110 2H8a1 1 0 01-1-1zm1 3a1 1 0 100 2h.01a1 1 0 100-2H8zm-1 5a1 1 0 011-1h.01a1 1 0 110 2H8a1 1 0 01-1-1z" clip-rule="evenodd" />
                                </svg>
                                Logout
                            </div>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">
        @yield('content')
    </main>

    <footer class="bg-white py-4 border-t border-gray-200">
        <div class="container mx-auto px-4">
            <p class="text-center text-gray-500 text-sm">
                &copy; {{ date('Y') }} inTime. All rights reserved.
            </p>
        </div>
    </footer>

    <!-- JavaScript for dropdown -->
    <script>
        // Function to toggle the dropdown menu
        function toggleDropdown() {
            document.getElementById("profileDropdown").classList.toggle("show");
        }

        // Close the dropdown if clicked outside
        window.onclick = function(event) {
            if (!event.target.matches('.dropdown, .dropdown *')) {
                var dropdowns = document.getElementsByClassName("dropdown-content");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }
    </script>

    @yield('scripts')
</body>
</html>