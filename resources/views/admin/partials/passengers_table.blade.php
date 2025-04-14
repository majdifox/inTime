@foreach($passengers as $passenger)
<tr>
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
        {{ $passenger->created_at->format('Y-m-d') }}
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
        <div class="flex items-center">
            <div class="h-10 w-10 flex-shrink-0 mr-3">
                @if($passenger->profile_picture)
                    <img class="h-10 w-10 rounded-full" src="{{ Storage::url($passenger->profile_picture) }}" alt="">
                @else
                    <img class="h-10 w-10 rounded-full" src="/api/placeholder/40/40" alt="">
                @endif
            </div>
            <div>
                <div class="text-sm font-medium text-gray-900">
                    {{ $passenger->name }}
                </div>
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    <span class="text-xs text-gray-500 ml-1">{{ $passenger->passenger ? number_format($passenger->passenger->rating, 1) : 'N/A' }}</span>
                </div>
            </div>
        </div>
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
        {{ $passenger->birthday ? $passenger->birthday->format('Y-m-d') : 'N/A' }}
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
            Passenger
        </span>
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
        {{ $passenger->email }}
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
        {{ $passenger->phone }}
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
        @php
            $statusClasses = [
                'activated' => 'bg-green-100 text-green-800',
                'deactivated' => 'bg-red-100 text-red-800',
                'pending' => 'bg-yellow-100 text-yellow-800',
                'suspended' => 'bg-orange-100 text-orange-800',
                'deleted' => 'bg-gray-100 text-gray-800'
            ];
            $statusClass = $statusClasses[$passenger->account_status] ?? 'bg-gray-100 text-gray-800';
        @endphp
        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
            {{ ucfirst($passenger->account_status) }}
        </span>
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
        DH 0.00
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
        <div class="flex space-x-2">
            <button class="text-green-600 hover:text-green-900 user-status-btn" 
                    data-user-id="{{ $passenger->id }}" 
                    data-status="activated" 
                    title="Activate">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </button>
            <button class="text-red-600 hover:text-red-900 user-status-btn" 
                    data-user-id="{{ $passenger->id }}" 
                    data-status="deactivated" 
                    title="Deactivate">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <button class="text-yellow-600 hover:text-yellow-900 user-status-btn" 
                    data-user-id="{{ $passenger->id }}" 
                    data-status="suspended" 
                    title="Suspend">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </button>
            <button class="text-gray-600 hover:text-gray-900 delete-user-btn" 
                    data-user-id="{{ $passenger->id }}"
                    title="Delete">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
            <button class="text-blue-600 hover:text-blue-900 view-details-btn" 
                    data-user-id="{{ $passenger->id }}" 
                    data-user-type="passenger"
                    title="View Details">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </button>
        </div>
    </td>
</tr>
@endforeach