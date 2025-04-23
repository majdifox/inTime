@extends('layouts.admin')

@section('title', 'Document Verification')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Document Verification</h1>
                <p class="mt-1 text-sm text-gray-500">{{ ucfirst($documentType) }} for {{ $driver->user->name }}</p>
            </div>
            <div>
                <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                    </svg>
                    Back
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8 mt-5">
        <!-- Document viewer and verification section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Document display (larger section) -->
            <div class="bg-white shadow rounded-lg lg:col-span-2 overflow-hidden">
                <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">{{ $documentType }}</h3>
                    <div class="flex items-center space-x-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $isVerified ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $isVerified ? 'Verified' : 'Pending Verification' }}
                        </span>
                        <button id="full-screen-btn" class="inline-flex items-center p-1 border border-transparent rounded-full shadow-sm text-gray-500 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="p-6 bg-gray-50 flex justify-center">
                    <div id="document-container" class="relative">
                        <img id="document-image" src="{{ Storage::url($documentPath) }}" alt="{{ $documentType }}" class="max-w-full max-h-[600px] object-contain">
                        <div id="zoom-controls" class="absolute bottom-4 right-4 flex space-x-2">
                            <button id="zoom-in" class="p-2 bg-white rounded-full shadow-md text-gray-600 hover:text-gray-800 focus:outline-none">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </button>
                            <button id="zoom-out" class="p-2 bg-white rounded-full shadow-md text-gray-600 hover:text-gray-800 focus:outline-none">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6" />
                                </svg>
                            </button>
                            <button id="rotate" class="p-2 bg-white rounded-full shadow-md text-gray-600 hover:text-gray-800 focus:outline-none">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Verification actions section -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Verification Actions</h3>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                    <!-- Driver info summary -->
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-500">Driver Information</h4>
                        <div class="mt-2 flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200">
                                @if($driver->user->profile_picture)
                                    <img class="h-10 w-10 rounded-full" src="{{ Storage::url($driver->user->profile_picture) }}" alt="{{ $driver->user->name }}">
                                @else
                                    <span class="h-10 w-10 rounded-full overflow-hidden bg-gray-100 flex items-center justify-center">
                                        <span class="font-medium text-gray-600">{{ strtoupper(substr($driver->user->name, 0, 1)) }}</span>
                                    </span>
                                @endif
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ $driver->user->name }}</p>
                                <p class="text-sm text-gray-500">{{ $driver->user->email }}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mt-4">
                            <div>
                                <p class="text-xs text-gray-500">License #</p>
                                <p class="text-sm font-medium">{{ $driver->license_number }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Expiry Date</p>
                                <p class="text-sm font-medium">{{ $driver->license_expiry ? date('Y-m-d', strtotime($driver->license_expiry)) : 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Phone</p>
                                <p class="text-sm font-medium">{{ $driver->user->phone }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Gender</p>
                                <p class="text-sm font-medium">{{ ucfirst($driver->user->gender ?? 'Not specified') }}</p>
                            </div>
                            </div>

<!-- Vehicle info (if applicable) -->
@if($document === 'vehicle' && $driver->vehicle)
    <div class="mb-6 border-t border-gray-200 pt-6">
        <h4 class="text-sm font-medium text-gray-500">Vehicle Information</h4>
        <div class="mt-2 grid grid-cols-2 gap-4">
            <div>
                <p class="text-xs text-gray-500">Make & Model</p>
                <p class="text-sm font-medium">{{ $driver->vehicle->make }} {{ $driver->vehicle->model }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Year</p>
                <p class="text-sm font-medium">{{ $driver->vehicle->year }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Color</p>
                <p class="text-sm font-medium">{{ $driver->vehicle->color }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Plate Number</p>
                <p class="text-sm font-medium">{{ $driver->vehicle->plate_number }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Vehicle Type</p>
                <p class="text-sm font-medium">{{ ucfirst($driver->vehicle->type) }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Capacity</p>
                <p class="text-sm font-medium">{{ $driver->vehicle->capacity }} passengers</p>
            </div>
        </div>
    </div>
@endif

<!-- License Details (if applicable) -->
@if($document === 'license')
    <div class="mb-6 border-t border-gray-200 pt-6">
        <h4 class="text-sm font-medium text-gray-500">License Details</h4>
        <div class="mt-2 grid grid-cols-2 gap-4">
            <div>
                <p class="text-xs text-gray-500">License Number</p>
                <p class="text-sm font-medium">{{ $driver->license_number }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">License Expiry</p>
                <p class="text-sm font-medium">{{ $driver->license_expiry ? date('Y-m-d', strtotime($driver->license_expiry)) : 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Status</p>
                <p class="text-sm font-medium">
                    <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-medium rounded-full {{ $driver->license_verified ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ $driver->license_verified ? 'Verified' : 'Pending Verification' }}
                    </span>
                </p>
            </div>
        </div>
    </div>
@endif

<!-- Insurance Details (if applicable) -->
@if($document === 'insurance')
    <div class="mb-6 border-t border-gray-200 pt-6">
        <h4 class="text-sm font-medium text-gray-500">Insurance Details</h4>
        <div class="mt-2 grid grid-cols-2 gap-4">
            <div>
                <p class="text-xs text-gray-500">Status</p>
                <p class="text-sm font-medium">
                    <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-medium rounded-full {{ $driver->insurance_verified ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ $driver->insurance_verified ? 'Verified' : 'Pending Verification' }}
                    </span>
                </p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Uploaded</p>
                <p class="text-sm font-medium">{{ $driver->created_at ? $driver->created_at->format('Y-m-d') : 'Unknown' }}</p>
            </div>
        </div>
    </div>
@endif

<!-- Verification form -->
<form action="{{ route('admin.verify.document', $driver->id) }}" method="POST" class="mb-6 border-t border-gray-200 pt-6">
    @csrf
    <input type="hidden" name="document_type" value="{{ $document }}">
    
    <div class="mb-4">
        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Verification Status</label>
        <select id="status" name="status" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
            <option value="verified" {{ $isVerified ? 'selected' : '' }}>Verified</option>
            <option value="rejected" {{ !$isVerified ? 'selected' : '' }}>Rejected</option>
        </select>
    </div>
    
    <div class="mb-4">
        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
        <textarea id="notes" name="notes" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Add verification notes or rejection reason...">{{ $driver->license_verification_notes ?? '' }}</textarea>
    </div>
    
    <div class="flex justify-between">
        <button type="submit" name="action" value="verify" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Save Verification Status
        </button>
        
        <a href="{{ route('admin.driver.show', $driver->user_id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
            View Full Profile
        </a>
    </div>
</form>

<!-- Quick actions -->
<div class="border-t border-gray-200 pt-6">
    <h4 class="text-sm font-medium text-gray-500 mb-4">Quick Actions</h4>
    <div class="grid grid-cols-1 gap-3">
        <a href="{{ route('admin.process.driver.verification', $driver->user_id) }}?action=approve" 
           onclick="return confirm('Are you sure you want to approve this driver?')"
           class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Approve Driver Application
        </a>
        
        <button type="button" 
                onclick="openRejectModal()"
                class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Reject Driver Application
        </button>
        
        @if($driver->user && $driver->user->account_status === 'pending')
            <a href="{{ route('admin.send.reminder', $driver->user_id) }}" 
               class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                Send Reminder to Driver
            </a>
        @endif
    </div>
</div>
</div>
</div>
</div>

<!-- Other documents from this driver -->
<div class="mt-6 bg-white shadow rounded-lg overflow-hidden">
<div class="px-4 py-5 sm:px-6">
<h2 class="text-lg leading-6 font-medium text-gray-900">Other Documents</h2>
<p class="mt-1 max-w-2xl text-sm text-gray-500">View other documents submitted by this driver</p>
</div>
<div class="border-t border-gray-200">
<ul role="list" class="divide-y divide-gray-200">
@php
    $documents = [
        'license' => [
            'name' => 'Driver License',
            'path' => $driver->license_photo,
            'verified' => $driver->license_verified,
        ],
        'insurance' => [
            'name' => 'Insurance Document',
            'path' => $driver->insurance_document,
            'verified' => $driver->insurance_verified,
        ],
        'vehicle' => [
            'name' => 'Vehicle Photo',
            'path' => $driver->vehicle ? $driver->vehicle->vehicle_photo : null,
            'verified' => $driver->vehicle ? $driver->vehicle->registration_verified : false,
        ],
        'good_conduct' => [
            'name' => 'Good Conduct Certificate',
            'path' => $driver->good_conduct_certificate,
            'verified' => $driver->good_conduct_verified,
        ],
    ];
@endphp

@foreach($documents as $key => $doc)
    @if($doc['path'] && $key !== $document)
        <li class="px-4 py-4 sm:px-6 hover:bg-gray-50">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">{{ $doc['name'] }}</p>
                        <p class="text-sm text-gray-500">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $doc['verified'] ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $doc['verified'] ? 'Verified' : 'Pending Verification' }}
                            </span>
                        </p>
                    </div>
                </div>
                <div>
                    <a href="{{ route('admin.view.driver.document', ['id' => $driver->id, 'document' => $key]) }}" 
                       class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="-ml-0.5 mr-2 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        View Document
                    </a>
                </div>
            </div>
        </li>
    @endif
@endforeach
</ul>
</div>
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
<form action="{{ route('admin.process.driver.verification', $driver->user_id) }}" method="POST" class="mt-5">
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

@endsection

@section('scripts')
<script>
// Document zoom and rotation functionality
let zoomLevel = 1;
let rotation = 0;
const img = document.getElementById('document-image');

document.getElementById('zoom-in').addEventListener('click', function() {
zoomLevel += 0.25;
if (zoomLevel > 3) zoomLevel = 3;
updateTransform();
});

document.getElementById('zoom-out').addEventListener('click', function() {
zoomLevel -= 0.25;
if (zoomLevel < 0.5) zoomLevel = 0.5;
updateTransform();
});

document.getElementById('rotate').addEventListener('click', function() {
rotation += 90;
if (rotation >= 360) rotation = 0;
updateTransform();
});

document.getElementById('full-screen-btn').addEventListener('click', function() {
if (img.requestFullscreen) {
img.requestFullscreen();
} else if (img.webkitRequestFullscreen) { /* Safari */
img.webkitRequestFullscreen();
} else if (img.msRequestFullscreen) { /* IE11 */
img.msRequestFullscreen();
}
});

function updateTransform() {
img.style.transform = `scale(${zoomLevel}) rotate(${rotation}deg)`;
}

// Modal functionality
function openRejectModal() {
document.getElementById('reject-modal').classList.remove('hidden');
}

function closeRejectModal() {
document.getElementById('reject-modal').classList.add('hidden');
}
</script>
@endsection