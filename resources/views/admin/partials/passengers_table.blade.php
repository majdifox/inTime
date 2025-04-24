<!-- resources/views/admin/partials/passengers_table.blade.php -->
@foreach($passengers as $passenger)
<tr class="bg-white border-b hover:bg-gray-50">
    <td class="px-6 py-4">{{ $passenger->created_at->format('Y-m-d') }}</td>
    <td class="px-6 py-4">
        <div class="h-10 w-10 rounded-full bg-gray-200 overflow-hidden">
            @if($passenger->profile_picture)
                <img src="{{ Storage::url($passenger->profile_picture) }}" alt="Profile" class="h-full w-full object-cover">
            @else
                <img src="/api/placeholder/40/40" alt="Profile" class="h-full w-full object-cover">
            @endif
        </div>
    </td>
    <td class="px-6 py-4 font-medium text-gray-900">{{ $passenger->name }}</td>
    <td class="px-6 py-4">{{ $passenger->birthday ? date('Y-m-d', strtotime($passenger->birthday)) : 'N/A' }}</td>
    <td class="px-6 py-4">{{ $passenger->email }}</td>
    <td class="px-6 py-4">{{ $passenger->phone }}</td>
    <td class="px-6 py-4">
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
    <td class="px-6 py-4">
        {{ $passenger->passenger ? ($passenger->passenger->total_rides ?? 0) : 0 }}
    </td>
    <td class="px-6 py-4">
        <div class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
            </svg>
            <span class="ml-1">{{ $passenger->passenger && $passenger->passenger->rating ? number_format($passenger->passenger->rating, 1) : 'N/A' }}</span>
        </div>
    </td>
    <td class="px-6 py-4">
        <div class="flex space-x-2">
            <a href="{{ route('admin.passenger.show', $passenger->id) }}" class="font-medium text-blue-600 hover:underline">
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
                        <a href="{{ route('admin.user.status', ['id' => $passenger->id, 'status' => 'activated']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Activate</a>
                        <a href="{{ route('admin.user.status', ['id' => $passenger->id, 'status' => 'deactivated']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Deactivate</a>
                        <a href="{{ route('admin.user.status', ['id' => $passenger->id, 'status' => 'suspended']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Suspend</a>
                        <a href="{{ route('admin.user.delete', ['id' => $passenger->id]) }}" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100" onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">Delete</a>
                    </div>
                </div>
            </div>
        </div>
    </td>
</tr>
@endforeach