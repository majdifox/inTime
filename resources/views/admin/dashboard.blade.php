@extends('admin.layouts.admin')

@section('title', 'inTime - Admin Dashboard')

@section('content')
<div class="max-w-6xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">Dashboard</h1>

    <!-- Statistics Section -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="p-6">
            <h2 class="text-xl font-bold mb-6">Ride Statistics</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Completed Rides -->
                <div class="p-4">
                    <p class="text-sm text-gray-500 mb-1">Total Completed</p>
                    <p class="text-2xl font-bold mb-1">{{ $stats['total_completed'] }}</p>
                    <p class="text-sm {{ $percentChanges['total_completed_change'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $percentChanges['total_completed_change'] >= 0 ? '+' : '' }}{{ $percentChanges['total_completed_change'] }}% from last month
                    </p>
                </div>
                
                <!-- Cancelled Rides -->
                <div class="p-4">
                    <p class="text-sm text-gray-500 mb-1">Cancelled Rides</p>
                    <p class="text-2xl font-bold mb-1">{{ $stats['cancelled_rides'] }}</p>
                    <p class="text-sm {{ $percentChanges['cancelled_rides_change'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $percentChanges['cancelled_rides_change'] >= 0 ? '+' : '' }}{{ $percentChanges['cancelled_rides_change'] }}% from last month
                    </p>
                </div>
                
                <!-- General Income -->
                <div class="p-4">
                    <p class="text-sm text-gray-500 mb-1">General Income</p>
                    <p class="text-2xl font-bold mb-1">DH {{ number_format($stats['general_income'], 2) }}</p>
                    <p class="text-sm {{ $percentChanges['general_income_change'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $percentChanges['general_income_change'] >= 0 ? '+' : '' }}{{ $percentChanges['general_income_change'] }}% from last month
                    </p>
                </div>
                
                <!-- Average Rating -->
                <div class="p-4">
                    <p class="text-sm text-gray-500 mb-1">Average Rating</p>
                    <p class="text-2xl font-bold mb-1">{{ number_format($stats['average_rating'], 1) }}</p>
                    <p class="text-sm {{ $percentChanges['average_rating_change'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $percentChanges['average_rating_change'] >= 0 ? '+' : '' }}{{ $percentChanges['average_rating_change'] }} from last month
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Supervision Section -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="p-6">
            <h2 class="text-xl font-bold mb-6">Supervision</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Online Drivers -->
                <div class="border border-gray-100 rounded p-4">
                    <p class="text-sm text-gray-500 mb-1">Online Drivers</p>
                    <div class="flex items-center mb-3">
                        <p class="text-2xl font-bold mr-2">{{ $driverStats['online_drivers'] }}</p>
                        <span class="px-2 py-0.5 bg-green-100 text-green-800 rounded-full text-xs font-semibold">Active</span>
                    </div>
                    <div class="flex justify-between">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Available</p>
                            <p class="text-lg font-medium">{{ $driverStats['available_drivers'] }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">On Ride</p>
                            <p class="text-lg font-medium">{{ $driverStats['on_ride_drivers'] }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Total Drivers -->
                <div class="border border-gray-100 rounded p-4">
                    <p class="text-sm text-gray-500 mb-1">Total Drivers</p>
                    <p class="text-2xl font-bold mb-3">{{ $driverStats['total_drivers'] }}</p>
                    <div class="grid grid-cols-3 gap-2">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Active</p>
                            <p class="text-lg font-medium">{{ $driverStats['active_drivers'] }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Pending</p>
                            <p class="text-lg font-medium">{{ $driverStats['pending_drivers'] }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Suspended</p>
                            <p class="text-lg font-medium">{{ $driverStats['suspended_drivers'] }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Pending Rides -->
                <div class="border border-gray-100 rounded p-4">
                    <p class="text-sm text-gray-500 mb-1">Pending Rides</p>
                    <p class="text-2xl font-bold mb-3">{{ $pendingRides }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection