@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<!-- Main Content -->
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
        <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>
        <p class="text-gray-500">Welcome back, {{ Auth::user()->name }}</p>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
        <!-- Quick Stats Cards -->
        <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Completed Rides -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Completed Rides
                            </dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_completed']) }}</div>
                                <div class="ml-2 flex items-baseline text-sm font-semibold {{ $percentChanges['total_completed_change'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    <svg class="self-center flex-shrink-0 h-5 w-5 {{ $percentChanges['total_completed_change'] >= 0 ? 'text-green-500' : 'text-red-500' }}" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M{{ $percentChanges['total_completed_change'] >= 0 ? '5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' : '14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z' }}" clip-rule="evenodd" />
                                    </svg>
                                    <span class="sr-only">{{ $percentChanges['total_completed_change'] >= 0 ? 'Increased' : 'Decreased' }} by</span>
                                    {{ abs($percentChanges['total_completed_change']) }}%
                                </div>
                            </dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Total Revenue
                            </dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">DH {{ number_format($stats['general_income'], 2) }}</div>
                                <div class="ml-2 flex items-baseline text-sm font-semibold {{ $percentChanges['general_income_change'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    <svg class="self-center flex-shrink-0 h-5 w-5 {{ $percentChanges['general_income_change'] >= 0 ? 'text-green-500' : 'text-red-500' }}" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M{{ $percentChanges['general_income_change'] >= 0 ? '5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' : '14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z' }}" clip-rule="evenodd" />
                                    </svg>
                                    <span class="sr-only">{{ $percentChanges['general_income_change'] >= 0 ? 'Increased' : 'Decreased' }} by</span>
                                    {{ abs($percentChanges['general_income_change']) }}%
                                </div>
                            </dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Drivers -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Active Drivers
                            </dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">{{ $driverStats['active_drivers'] }}</div>
                                <div class="ml-2 flex items-baseline text-sm font-semibold text-gray-600">
                                    <span>of {{ $driverStats['total_drivers'] }} total</span>
                                </div>
                            </dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Average Rating -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Average Rating
                            </dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">{{ number_format($stats['average_rating'], 1) }}</div>
                                <div class="ml-2 flex items-baseline text-sm font-semibold text-yellow-600">
                                    <div class="flex">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= round($stats['average_rating']))
                                                <svg class="h-4 w-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            @else
                                                <svg class="h-4 w-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                            </dd>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Two Column Layout -->
        <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Driver Verification Queue (Important Section) -->
            <div class="bg-white shadow rounded-lg lg:col-span-1">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Driver Verification Queue</h3>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        {{ $driverStats['pending_drivers'] }} Pending
                    </span>
                </div>
                <div class="divide-y divide-gray-200">
                    @if($driverStats['pending_drivers'] > 0)
                        @foreach(App\Models\User::where('role', 'driver')->where('account_status', 'pending')->with('driver')->take(5)->get() as $pendingDriver)
                            <div class="px-5 py-4 hover:bg-gray-50">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200 overflow-hidden">
                                        @if($pendingDriver->profile_picture)
                                            <img src="{{ Storage::url($pendingDriver->profile_picture) }}" alt="{{ $pendingDriver->name }}" class="h-10 w-10 object-cover">
                                        @else
                                            <div class="h-10 w-10 flex items-center justify-center bg-indigo-100 text-indigo-700 font-bold text-xl">
                                                {{ strtoupper(substr($pendingDriver->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <div class="text-sm font-medium text-gray-900">{{ $pendingDriver->name }}</div>
                                        <div class="text-sm text-gray-500">Submitted: {{ $pendingDriver->created_at->diffForHumans() }}</div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <a href="{{ route('admin.driver.show', $pendingDriver->id) }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            Review
                                        </a>
                                    </div>
                                </div>
                                @if($pendingDriver->driver)
                                <div class="mt-2 grid grid-cols-3 gap-2">
                                    <div class="flex items-center text-xs">
                                        <span class="inline-block w-2 h-2 rounded-full {{ $pendingDriver->driver->license_photo ? 'bg-green-400' : 'bg-gray-300' }} mr-1"></span>
                                        <span class="{{ $pendingDriver->driver->license_photo ? 'text-green-800' : 'text-gray-500' }}">License</span>
                                    </div>
                                    <div class="flex items-center text-xs">
                                        <span class="inline-block w-2 h-2 rounded-full {{ $pendingDriver->driver->insurance_document ? 'bg-green-400' : 'bg-gray-300' }} mr-1"></span>
                                        <span class="{{ $pendingDriver->driver->insurance_document ? 'text-green-800' : 'text-gray-500' }}">Insurance</span>
                                    </div>
                                    <div class="flex items-center text-xs">
                                        <span class="inline-block w-2 h-2 rounded-full {{ $pendingDriver->driver->vehicle && $pendingDriver->driver->vehicle->vehicle_photo ? 'bg-green-400' : 'bg-gray-300' }} mr-1"></span>
                                        <span class="{{ $pendingDriver->driver->vehicle && $pendingDriver->driver->vehicle->vehicle_photo ? 'text-green-800' : 'text-gray-500' }}">Vehicle</span>
                                    </div>
                                </div>
                                @endif
                            </div>
                        @endforeach
                        <div class="px-5 py-4 bg-gray-50 text-center">
                            <a href="{{ route('admin.driver.verifications') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                View all pending drivers
                            </a>
                        </div>
                    @else
                        <div class="px-5 py-8 text-center">
                            <svg class="mx-auto h-12 w-12 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">All caught up!</h3>
                            <p class="mt-1 text-sm text-gray-500">No pending driver verifications at this time.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Activity (bigger section) -->
            <div class="bg-white shadow rounded-lg lg:col-span-2">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Recent Activity</h3>
                    <a href="{{ route('admin.activity.log') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                        View all
                    </a>
                </div>
                <div class="divide-y divide-gray-200">
                    @if(!empty($recentActivity['rides']))
                        @foreach($recentActivity['rides'] as $ride)
                            <div class="px-5 py-4 hover:bg-gray-50">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="bg-green-100 rounded-full p-2">
                                            <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <div class="flex justify-between">
                                            <div class="text-sm font-medium text-gray-900">Ride Completed</div>
                                            <div class="text-sm text-gray-500">{{ $ride->dropoff_time ? Carbon\Carbon::parse($ride->dropoff_time)->diffForHumans() : 'N/A' }}</div>
                                        </div>
                                        <div class="mt-1 text-sm text-gray-500">
                                            {{ $ride->passenger && $ride->passenger->user ? $ride->passenger->user->name : 'Unknown passenger' }} 
                                            was driven by 
                                            {{ $ride->driver && $ride->driver->user ? $ride->driver->user->name : 'Unknown driver' }}
                                        </div>
                                        <div class="mt-2 flex">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mr-2">
                                                DH {{ number_format($ride->price, 2) }}
                                            </span>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ number_format($ride->distance_in_km, 1) }} km
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="px-5 py-4 text-center">
                            <p class="text-sm text-gray-500">No recent rides to show.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Driver Status Overview -->
        <div class="mt-8">
            <div class="bg-white shadow rounded-lg">
                <div class="px-5 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Driver Status Overview</h3>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="bg-indigo-50 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-sm font-medium text-indigo-800">Total Drivers</h4>
                                <div class="bg-indigo-100 text-indigo-800 p-1 rounded-md">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="text-3xl font-bold text-indigo-800">{{ $driverStats['total_drivers'] }}</div>
                            <div class="text-sm text-indigo-600 mt-1">
                                @if(isset($driverStats['onboarded_today']) && $driverStats['onboarded_today'] > 0) 
                                    +{{ $driverStats['onboarded_today'] }} today
                                @else
                                    No new drivers today
                                @endif
                            </div>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-sm font-medium text-green-800">Online Drivers</h4>
                                <div class="bg-green-100 text-green-800 p-1 rounded-md">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728m-9.9-2.829a5 5 0 010-7.07m7.072 0a5 5 0 010 7.07M13 12a1 1 0 11-2 0 1 1 0 012 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="text-3xl font-bold text-green-800">{{ $driverStats['online_drivers'] }}</div>
                            <div class="text-sm text-green-600 mt-1">
                                {{ $driverStats['online_drivers'] > 0 ? number_format(($driverStats['online_drivers'] / $driverStats['total_drivers']) * 100, 0) : 0 }}% of total drivers
                            </div>
                        </div>
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-sm font-medium text-blue-800">On Ride</h4>
                                <div class="bg-blue-100 text-blue-800 p-1 rounded-md">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                    </svg>
                                </div>
                            </div>
                            <div class="text-3xl font-bold text-blue-800">{{ $driverStats['on_ride_drivers'] }}</div>
                            <div class="text-sm text-blue-600 mt-1">
                                {{ $driverStats['online_drivers'] > 0 ? number_format(($driverStats['on_ride_drivers'] / $driverStats['online_drivers']) * 100, 0) : 0 }}% of online drivers
                            </div>
                        </div>
                        <div class="bg-yellow-50 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-sm font-medium text-yellow-800">Pending Verification</h4>
                                <div class="bg-yellow-100 text-yellow-800 p-1 rounded-md">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="text-3xl font-bold text-yellow-800">{{ $driverStats['pending_drivers'] }}</div>
                            <div class="text-sm text-yellow-600 mt-1">
                                <a href="{{ route('admin.driver.verifications') }}" class="underline">Review documents</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick links -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="bg-indigo-600 px-5 py-4">
                    <h3 class="text-lg font-medium text-white">Driver Management</h3>
                </div>
                <div class="p-5">
                    <ul class="space-y-3">
                        <li>
                            <a href="{{ route('admin.users', ['tab' => 'drivers']) }}" class="flex items-center text-indigo-600 hover:text-indigo-900">
                                <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                View all drivers
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.driver.verifications') }}" class="flex items-center text-indigo-600 hover:text-indigo-900">
                                <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                Review pending verifications
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.drivers.map') }}" class="flex items-center text-indigo-600 hover:text-indigo-900">
                                <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                </svg>
                                View drivers map
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="bg-green-600 px-5 py-4">
                    <h3 class="text-lg font-medium text-white">Ride Management</h3>
                </div>
                <div class="p-5">
                    <ul class="space-y-3">
                        <li>
                            <a href="{{ route('admin.rides.management') }}" class="flex items-center text-green-600 hover:text-green-900">
                                <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                                View all rides
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.rides.management', ['status' => 'pending']) }}" class="flex items-center text-green-600 hover:text-green-900">
                                <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                View pending rides
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.rides.management', ['status' => 'ongoing']) }}" class="flex items-center text-green-600 hover:text-green-900">
                                <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                Track active rides
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="bg-blue-600 px-5 py-4">
                    <h3 class="text-lg font-medium text-white">Financial Management</h3>
                </div>
                <div class="p-5">
                    <ul class="space-y-3">
                        <li>
                            <a href="{{ route('admin.earnings') }}" class="flex items-center text-blue-600 hover:text-blue-900">
                                <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                View earnings reports
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.driver.payouts') }}" class="flex items-center text-blue-600 hover:text-blue-900">
                                <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                Process driver payouts
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.settings') }}" class="flex items-center text-blue-600 hover:text-blue-900">
                                <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Update fare settings
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    // Initialize charts and other dashboard elements
    document.addEventListener('DOMContentLoaded', function() {
        // Add any JavaScript needed for the dashboard
    });
</script>
@endsection