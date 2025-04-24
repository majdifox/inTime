@if(count($drivers) > 0)
    @foreach($drivers as $driver)
    <tr>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            {{ $driver->created_at ? date('Y-m-d', strtotime($driver->created_at)) : 'N/A' }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200 overflow-hidden">
                    @if($driver->profile_picture)
                        <img src="{{ Storage::url($driver->profile_picture) }}" alt="Profile" class="h-full w-full object-cover">
                    @else
                        <img src="/api/placeholder/40/40" alt="Profile" class="h-full w-full object-cover">
                    @endif
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">{{ $driver->name }}</div>
                </div>
            </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            {{ $driver->birthday ? date('Y-m-d', strtotime($driver->birthday)) : 'N/A' }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            {{ $driver->email }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            {{ $driver->phone }}
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
                $statusClass = $statusClasses[$driver->account_status] ?? 'bg-gray-100 text-gray-800';
            @endphp
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                {{ ucfirst($driver->account_status) }}
            </span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $driver->driver && $driver->driver->is_verified ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                {{ $driver->driver && $driver->driver->is_verified ? 'Verified' : 'Not Verified' }}
            </span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            DH {{ $driver->driver ? number_format($driver->driver->balance, 2) : '0.00' }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
            <div class="flex space-x-2">
                <a href="{{ route('admin.driver.show', $driver->id) }}" class="text-blue-600 hover:text-blue-900 view-details" data-user-id="{{ $driver->id }}" data-user-role="driver">
                    <span class="sr-only">View</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                    </svg>
                </a>
                
                @if($driver->account_status != 'activated')
                <a href="#" class="text-green-600 hover:text-green-900 update-status" data-user-id="{{ $driver->id }}" data-status="activated" data-user-name="{{ $driver->name }}">
                    <span class="sr-only">Activate</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </a>
                @endif
                
                @if($driver->account_status != 'deactivated')
                <a href="#" class="text-red-600 hover:text-red-900 update-status" data-user-id="{{ $driver->id }}" data-status="deactivated" data-user-name="{{ $driver->name }}">
                    <span class="sr-only">Deactivate</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </a>
                @endif
                
                @if($driver->account_status != 'suspended')
                <a href="#" class="text-yellow-600 hover:text-yellow-900 update-status" data-user-id="{{ $driver->id }}" data-status="suspended" data-user-name="{{ $driver->name }}">
                    <span class="sr-only">Suspend</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                    </svg>
                </a>
                @endif
                
                <a href="#" class="text-gray-600 hover:text-gray-900 delete-user" data-user-id="{{ $driver->id }}" data-user-name="{{ $driver->name }}">
                    <span class="sr-only">Delete</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </a>
            </div>
        </td>
    </tr>
    @endforeach
@else
    <tr>
        <td colspan="9" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
            No drivers found
        </td>
    </tr>
@endif