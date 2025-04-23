@extends('layouts.admin')

@section('title', 'Driver Verifications')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Driver Verifications</h1>
                <p class="mt-1 text-sm text-gray-500">Review and approve driver documents and applications</p>
            </div>
            <div>
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                    </svg>
                    Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8 mt-5">
        <!-- Filters -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <form action="{{ route('admin.driver.verifications') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                        <div class="mt-1">
                            <input type="text" name="search" id="search" value="{{ request('search') }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Name, email, phone...">
                        </div>
                    </div>
                    <div>
                        <label for="document_type" class="block text-sm font-medium text-gray-700">Document Type</label>
                        <select id="document_type" name="document_type" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="">All Documents</option>
                            @foreach($documentTypes as $value => $label)
                                <option value="{{ $value }}" {{ request('document_type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="date_range" class="block text-sm font-medium text-gray-700">Date Range</label>
                        <select id="date_range" name="date_range" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="">All Time</option>
                            <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
                            <option value="yesterday" {{ request('date_range') == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                            <option value="week" {{ request('date_range') == 'week' ? 'selected' : '' }}>This Week</option>
                            <option value="month" {{ request('date_range') == 'month' ? 'selected' : '' }}>This Month</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Driver Applications -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Pending Driver Applications</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Displaying {{ $pendingDrivers->firstItem() ?? 0 }} - {{ $pendingDrivers->lastItem() ?? 0 }} of {{ $pendingDrivers->total() ?? 0 }} pending applications</p>
                </div>
                <div class="flex space-x-3">
                    <button id="bulk-approve" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50" disabled>
                        Approve Selected
                    </button>
                    <button id="bulk-reject" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50" disabled>
                        Reject Selected
                    </button>
                </div>
            </div>
            
            @if($pendingDrivers->count() > 0)
                <div class="border-t border-gray-200 divide-y divide-gray-200">
                    @foreach($pendingDrivers as $driver)
                        <div class="px-4 py-5 sm:p-6 hover:bg-gray-50 driver-application" data-id="{{ $driver->id }}">
                            <div class="md:flex md:justify-between md:items-center">
                                <!-- Driver Info Section -->
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="h-12 w-12 rounded-full overflow-hidden bg-gray-100">
                                            @if($driver->profile_picture)
                                                <img src="{{ Storage::url($driver->profile_picture) }}" alt="{{ $driver->name }}" class="h-12 w-12 object-cover">
                                            @else
                                                <div class="h-12 w-12 flex items-center justify-center bg-indigo-100 text-indigo-700 font-bold text-xl">
                                                    {{ strtoupper(substr($driver->name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="text-lg font-semibold">{{ $driver->name }}</h4>
                                        <div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6">
                                            <div class="mt-2 flex items-center text-sm text-gray-500">
                                                <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                                {{ $driver->email }}
                                            </div>
                                            <div class="mt-2 flex items-center text-sm text-gray-500">
                                                <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                </svg>
                                                {{ $driver->phone }}
                                            </div>
                                            <div class="mt-2 flex items-center text-sm text-gray-500">
                                                <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                Applied {{ $driver->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Document Status Section -->
                                <div class="mt-4 md:mt-0 flex flex-wrap gap-2">
                                    @if($driver->driver)
                                        <!-- License -->
                                        <a href="{{ route('admin.view.driver.document', ['id' => $driver->driver->id, 'document' => 'license']) }}" 
                                           class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                                           {{ $driver->driver->license_photo ? ($driver->driver->license_verified ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800') : 'bg-gray-100 text-gray-800' }}">
                                            <svg class="mr-1 h-4 w-4 {{ $driver->driver->license_photo ? ($driver->driver->license_verified ? 'text-green-500' : 'text-yellow-500') : 'text-gray-500' }}" 
                                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                      d="{{ $driver->driver->license_verified ? 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' : 
                                                            ($driver->driver->license_photo ? 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z' : 
                                                            'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z') }}" />
                                            </svg>
                                            License
                                        </a>
                                        
                                        <!-- Insurance -->
                                        <a href="{{ route('admin.view.driver.document', ['id' => $driver->driver->id, 'document' => 'insurance']) }}" 
                                           class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                                           {{ $driver->driver->insurance_document ? ($driver->driver->insurance_verified ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800') : 'bg-gray-100 text-gray-800' }}">
                                            <svg class="mr-1 h-4 w-4 {{ $driver->driver->insurance_document ? ($driver->driver->insurance_verified ? 'text-green-500' : 'text-yellow-500') : 'text-gray-500' }}" 
                                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                      d="{{ $driver->driver->insurance_verified ? 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' : 
                                                            ($driver->driver->insurance_document ? 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z' : 
                                                            'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z') }}" />
                                            </svg>
                                            Insurance
                                        </a>
                                        
                                        <!-- Vehicle -->
                                        @if($driver->driver->vehicle)
                                            <a href="{{ route('admin.view.driver.document', ['id' => $driver->driver->id, 'document' => 'vehicle']) }}" 
                                               class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                                               {{ $driver->driver->vehicle->vehicle_photo ? ($driver->driver->vehicle->registration_verified ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800') : 'bg-gray-100 text-gray-800' }}">
                                                <svg class="mr-1 h-4 w-4 {{ $driver->driver->vehicle->vehicle_photo ? ($driver->driver->vehicle->registration_verified ? 'text-green-500' : 'text-yellow-500') : 'text-gray-500' }}" 
                                                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                          d="{{ $driver->driver->vehicle->registration_verified ? 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' : 
                                                                ($driver->driver->vehicle->vehicle_photo ? 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z' : 
                                                                'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z') }}" />
                                                </svg>
                                                Vehicle
                                            </a>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <svg class="mr-1 h-4 w-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                </svg>
                                                No Vehicle
                                            </span>
                                        @endif
                                        
                                        <!-- Good Conduct Certificate -->
                                        @if($driver->driver->good_conduct_certificate)
                                            <a href="{{ route('admin.view.driver.document', ['id' => $driver->driver->id, 'document' => 'good_conduct']) }}" 
                                               class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                                               {{ $driver->driver->good_conduct_verified ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                <svg class="mr-1 h-4 w-4 {{ $driver->driver->good_conduct_verified ? 'text-green-500' : 'text-yellow-500' }}" 
                                                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                          d="{{ $driver->driver->good_conduct_verified ? 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' : 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z' }}" />
                                                </svg>
                                                Good Conduct
                                            </a>
                                        @endif
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <svg class="mr-1 h-4 w-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                            No Driver Profile
                                        </span>
                                    @endif
                                </div>
                                
                                <!-- Actions -->
                                <div class="mt-4 md:mt-0 flex space-x-3">
                                    <a href="{{ route('admin.driver.show', $driver->id) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="-ml-0.5 mr-2 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        View Details
                                    </a>
                                    <div class="flex">
                                        <form action="{{ route('admin.process.driver.verification', $driver->id) }}" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="action" value="approve">
                                            <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                <svg class="-ml-0.5 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Approve
                                            </button>
                                        </form>
                                        
                                        <button type="button" onclick="openRejectModal('{{ $driver->id }}')" class="ml-2 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                            <svg class="-ml-0.5 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            Reject
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Vehicle Details (if available) -->
                            @if($driver->driver && $driver->driver->vehicle)
                                <div class="mt-4 border-t pt-4">
                                    <div class="flex items-center">
                                        <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span class="text-sm text-gray-500">
                                            <span class="font-medium text-gray-700">Vehicle Info:</span>
                                            {{ $driver->driver->vehicle->year }} {{ $driver->driver->vehicle->make }} {{ $driver->driver->vehicle->model }} ({{ $driver->driver->vehicle->color }}) 
                                            • License Plate: {{ $driver->driver->vehicle->plate_number }}
                                            • Type: {{ ucfirst($driver->driver->vehicle->type) }}
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <a href="{{ $pendingDrivers->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Previous
                        </a>
                        <a href="{{ $pendingDrivers->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Next
                        </a>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Showing <span class="font-medium">{{ $pendingDrivers->firstItem() ?? 0 }}</span> to <span class="font-medium">{{ $pendingDrivers->lastItem() ?? 0 }}</span> of <span class="font-medium">{{ $pendingDrivers->total() ?? 0 }}</span> results
                            </p>
                        </div>
                        <div>
                            {{ $pendingDrivers->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No pending applications</h3>
                    <p class="mt-1 text-sm text-gray-500">There are no driver applications waiting for verification at this time.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="reject-modal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div>
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="mt-3 text-center sm:mt-5">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        Reject Driver Application
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">
                            Are you sure you want to reject this driver application? This action will change the driver's status to "deactivated" and they will need to reapply.
                        </p>
                    </div>
                </div>
            </div>
            <form id="reject-form" action="" method="POST" class="mt-5">
                @csrf
                <input type="hidden" name="action" value="reject">
                <div>
                    <label for="verification_notes" class="block text-sm font-medium text-gray-700">Reason for rejection (optional)</label>
                    <div class="mt-1">
                        <textarea id="verification_notes" name="verification_notes" rows="3" class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Provide a reason for rejecting this application..."></textarea>
                    </div>
                </div>
                <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:col-start-2 sm:text-sm">
                        Confirm Rejection
                    </button>
                    <button type="button" onclick="closeRejectModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Document Viewer Modal -->
<div id="document-modal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="document-modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="document-modal-title">
                            Document Viewer
                        </h3>
                        <div class="mt-4 border-b border-gray-200 pb-3 flex justify-between items-center">
                            <div>
                                <p id="document-info" class="text-sm text-gray-500"></p>
                            </div>
                            <div class="flex space-x-3">
                                <button id="approve-document" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    <svg class="-ml-0.5 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Verify Document
                                </button>
                                <button id="reject-document" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    <svg class="-ml-0.5 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Reject Document
                                </button>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div id="document-container" class="bg-gray-100 rounded-lg p-4 flex justify-center">
                                <img id="document-image" src="" alt="Document" class="max-h-96 object-contain">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="closeDocumentModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function openRejectModal(driverId) {
        document.getElementById('reject-form').action = `/admin/driver/${driverId}/verification`;
        document.getElementById('reject-modal').classList.remove('hidden');
    }
    
    function closeRejectModal() {
        document.getElementById('reject-modal').classList.add('hidden');
    }
    
    function openDocumentModal(driverId, documentType, documentPath) {
        const modal = document.getElementById('document-modal');
        const title = document.getElementById('document-modal-title');
        const info = document.getElementById('document-info');
        const image = document.getElementById('document-image');
        
        // Set modal content
        title.textContent = `View ${documentType} Document`;
        info.textContent = `Driver ID: ${driverId}`;
        image.src = documentPath;
        
        // Set up approve/reject buttons
        document.getElementById('approve-document').onclick = function() {
            verifyDocument(driverId, documentType, 'verified');
        };
        
        document.getElementById('reject-document').onclick = function() {
            verifyDocument(driverId, documentType, 'rejected');
        };
        
        // Show modal
        modal.classList.remove('hidden');
    }
    
    function closeDocumentModal() {
        document.getElementById('document-modal').classList.add('hidden');
    }
    
    function verifyDocument(driverId, documentType, status) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/driver/${driverId}/verify-document`;
        
        // CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfToken);
        
        // Document type
        const docTypeInput = document.createElement('input');
        docTypeInput.type = 'hidden';
        docTypeInput.name = 'document_type';
        docTypeInput.value = documentType;
        form.appendChild(docTypeInput);
        
        // Status
        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'status';
        statusInput.value = status;
        form.appendChild(statusInput);
        
        document.body.appendChild(form);
        form.submit();
    }
    
    // Bulk selection functionality
    document.addEventListener('DOMContentLoaded', function() {
        const driverApplications = document.querySelectorAll('.driver-application');
        let selectedDrivers = [];
        
        driverApplications.forEach(app => {
            app.addEventListener('click', function(e) {
                // Don't select if clicking on a button or link
                if (e.target.tagName === 'BUTTON' || e.target.tagName === 'A' || 
                    e.target.closest('button') || e.target.closest('a')) {
                    return;
                }
                
                const driverId = this.dataset.id;
                const index = selectedDrivers.indexOf(driverId);
                
                if (index === -1) {
                    // Add to selection
                    selectedDrivers.push(driverId);
                    this.classList.add('bg-indigo-50', 'border-l-4', 'border-indigo-500');
                } else {
                    // Remove from selection
                    selectedDrivers.splice(index, 1);
                    this.classList.remove('bg-indigo-50', 'border-l-4', 'border-indigo-500');
                }
                
                // Update bulk action buttons
                const bulkApprove = document.getElementById('bulk-approve');
                const bulkReject = document.getElementById('bulk-reject');
                
                if (selectedDrivers.length > 0) {
                    bulkApprove.disabled = false;
                    bulkReject.disabled = false;
                } else {
                    bulkApprove.disabled = true;
                    bulkReject.disabled = true;
                }
            });
        });
        
        // Handle bulk approve
        document.getElementById('bulk-approve').addEventListener('click', function() {
            if (selectedDrivers.length === 0) return;
            
            if (confirm(`Are you sure you want to approve ${selectedDrivers.length} driver application(s)?`)) {
                // Create and submit form with selected IDs
                processSelectedDrivers('approve');
            }
        });
        
        // Handle bulk reject
        document.getElementById('bulk-reject').addEventListener('click', function() {
            if (selectedDrivers.length === 0) return;
            
            if (confirm(`Are you sure you want to reject ${selectedDrivers.length} driver application(s)?`)) {
                // Create and submit form with selected IDs
                processSelectedDrivers('reject');
            }
        });
        
        function processSelectedDrivers(action) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/admin/driver/bulk-verification';
            
            // CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            form.appendChild(csrfToken);
            
            // Action
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = action;
            form.appendChild(actionInput);
            
            // Driver IDs
            selectedDrivers.forEach(id => {
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'driver_ids[]';
                idInput.value = id;
                form.appendChild(idInput);
            });
            
            document.body.appendChild(form);
            form.submit();
        }
    });
</script>
@endsection