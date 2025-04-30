<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'inTime')</title>
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Optional: Additional styles -->
    @yield('styles')
    
    <!-- Optional: Scripts in head -->
    @yield('head-scripts')
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">
    <!-- Header/Navigation -->
    @include('passenger.partials.header')

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8 flex-grow">
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
        
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white py-6 border-t border-gray-200 mt-auto">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <p class="text-gray-600 text-sm">© {{ date('Y') }} inTime. All rights reserved.</p>
                </div>
                <div class="flex space-x-6">
                    <a href="#" class="text-gray-600 text-sm hover:text-gray-900">About</a>
                    <a href="#" class="text-gray-600 text-sm hover:text-gray-900">Privacy Policy</a>
                    <a href="#" class="text-gray-600 text-sm hover:text-gray-900">Terms of Service</a>
                    <a href="#" class="text-gray-600 text-sm hover:text-gray-900">Contact</a>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Optional: Modal content -->
    @yield('modals')
    
    <!-- Optional: Scripts at end of body -->
    @yield('scripts')
</body>
</html>