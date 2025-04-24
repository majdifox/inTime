<!-- resources/views/admin/partials/drivers_table.blade.php -->
@foreach($drivers as $driver)
<tr class="bg-white border-b hover:bg-gray-50">
    <td class="px-6 py-4">{{ $driver->created_at->format('Y-m-d') }}</td>
    <td class="px-6 py-4">
        <div class="h-10 w-10 rounded-full bg-gray-200 overflow-hidden">
            @if($driver->profile_picture)
                <img src="{{ Storage::url($driver->profile_picture) }}" alt="Profile" class="h-full w-full object-cover">
            @else
                <img src="/api/placeholder/40/40" alt="Profile" class="h-full w-full object-cover">
            @endif
        </div>
    </td>
    <td class="px-6 py-4 font-medium text-gray-900">{{ $driver->name }}</td>
    <td class="px-6 py-4">{{ $driver->birthday ? date('Y-m-d', strtotime($driver->birthday)) : 'N/A' }}</td>
    <td class="px-6 py-4">{{ $driver->email }}</td>
    <td class="px-6 py-4">{{ $driver->phone }}</td>
    <td class="px-6 py-4">
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
    <td class="px-6 py-4">
        @if($driver->driver && $driver->driver->is_verified)
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                Verified
            </span>
        @else
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                Not Verified
            </span>
        @endif
    </td>
    <td class="px-6 py-4">
        DH {{ $driver->driver ? number_format($driver->driver->balance, 2) : '0.00' }}
    </td>
    <td class="px-6 py-4">
        <div class="flex space-x-2">
            <a href="{{ route('admin.driver.show', $driver->id) }}" class="font-medium text-blue-600 hover:underline">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </a>
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="font-medium text-gray-600 hover:text-gray-900">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                    </svg>
                </button>
                <div x-show="open" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                    <div class="py-1">
                        <a href="{{ route('admin.user.status', ['id' => $driver->id, 'status' => 'activated']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Activate</a>
                        <a href="{{ route('admin.user.status', ['id' => $driver->id, 'status' => 'deactivated']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Deactivate</a>
                        <a href="{{ route('admin.user.status', ['id' => $driver->id, 'status' => 'suspended']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Suspend</a>
                        <a href="{{ route('admin.user.delete', ['id' => $driver->id]) }}" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100" onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">Delete</a>
                    </div>
                </div>
            </div>
        </div>
    </td>
</tr>
@endforeach