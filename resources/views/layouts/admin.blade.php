<!-- File: resources/views/layouts/admin.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Admin</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen">
        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 bg-white shadow-md max-h-screen w-60">
            <div class="flex flex-col justify-between h-full">
                <div class="flex-grow">
                    <div class="px-4 py-6 text-center border-b">
                        <h1 class="text-xl font-bold leading-none">
                            <a href="{{ route('admin.dashboard') }}" class="text-indigo-700">Admin Panel</a>
                        </h1>
                    </div>
                    <div class="p-4">
                        <ul class="space-y-1">
                            <li>
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center bg-indigo-50 hover:bg-indigo-100 rounded-md font-medium text-indigo-600 py-2 px-4">
                                    <i class="fas fa-tachometer-alt mr-3"></i>
                                    Dashboard
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.users.index') }}" class="flex items-center hover:bg-indigo-100 rounded-md font-medium text-gray-600 hover:text-indigo-600 py-2 px-4">
                                    <i class="fas fa-users mr-3"></i>
                                    Users
                                </a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center hover:bg-indigo-100 rounded-md font-medium text-gray-600 hover:text-indigo-600 py-2 px-4">
                                    <i class="fas fa-route mr-3"></i>
                                    Rides
                                </a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center hover:bg-indigo-100 rounded-md font-medium text-gray-600 hover:text-indigo-600 py-2 px-4">
                                    <i class="fas fa-chart-pie mr-3"></i>
                                    Reports
                                </a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center hover:bg-indigo-100 rounded-md font-medium text-gray-600 hover:text-indigo-600 py-2 px-4">
                                    <i class="fas fa-cog mr-3"></i>
                                    Settings
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="p-4 border-t">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center w-full rounded-md font-medium text-gray-600 hover:text-red-600 py-2 px-4">
                            <i class="fas fa-sign-out-alt mr-3"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main content -->
        <main class="ml-60 pt-16 max-h-screen overflow-auto">
            <div class="px-6 py-8">
                <!-- Navbar -->
                <nav class="bg-white shadow-md fixed top-0 left-60 right-0 z-10">
                    <div class="mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="flex justify-between h-16">
                            <div class="flex">
                                <div class="flex-shrink-0 flex items-center">
                                    <h1 class="text-lg font-semibold text-indigo-600">
                                        {{ config('app.name', 'Laravel') }} Admin
                                    </h1>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="relative">
                                    <div class="flex items-center">
                                        @if(auth()->user()->profile_picture)
                                            <img class="h-8 w-8 rounded-full" src="{{ asset('storage/' . auth()->user()->profile_picture) }}" alt="Profile">
                                        @else
                                            <div class="h-8 w-8 rounded-full bg-indigo-500 flex items-center justify-center">
                                                <span class="text-white">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                        <span class="ml-2 text-gray-700">{{ auth()->user()->name }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </nav>
                
                <!-- Page content -->
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>