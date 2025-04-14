<!-- File: resources/views/admin/users/edit.blade.php -->
@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card bg-white rounded-lg shadow-md">
                <div class="card-header pb-0">
                    <div class="flex justify-between items-center">
                        <h4 class="text-lg font-semibold">Edit User: {{ $user->name }}</h4>
                        <a href="{{ route('admin.users.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm">Back to List</a>
                    </div>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="bg-red-50 text-red-600 p-4 rounded-md mb-6">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                            
                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                            
                            <!-- Phone -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                            
                            <!-- Birthday -->
                            <div>
                                <label for="birthday" class="block text-sm font-medium text-gray-700">Birthday</label>
                                <input type="date" name="birthday" id="birthday" value="{{ old('birthday', $user->birthday ? $user->birthday->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                            
                            <!-- Role -->
                            <div>
                                <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                                <select name="role" id="role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="passenger" {{ old('role', $user->role) == 'passenger' ? 'selected' : '' }}>Passenger</option>
                                    <option value="driver" {{ old('role', $user->role) == 'driver' ? 'selected' : '' }}>Driver</option>
                                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                                </select>
                            </div>
                            
                            <!-- Account Status -->
                            <div>
                                <label for="account_status" class="block text-sm font-medium text-gray-700">Account Status</label>
                                <select name="account_status" id="account_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="activated" {{ old('account_status', $user->account_status) == 'activated' ? 'selected' : '' }}>Activated</option>
                                    <option value="pending" {{ old('account_status', $user->account_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="deactivated" {{ old('account_status', $user->account_status) == 'deactivated' ? 'selected' : '' }}>Deactivated</option>
                                    <option value="suspended" {{ old('account_status', $user->account_status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                    <option value="deleted" {{ old('account_status', $user->account_status) == 'deleted' ? 'selected' : '' }}>Deleted</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Additional information section if user is driver -->
                        @if($user->role == 'driver' && $user->driver)
                        <div class="mt-8 border-t pt-6">
                            <h4 class="text-md font-medium text-gray-700 mb-4">Driver Information</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">License Number</label>
                                    <div class="mt-1 block w-full py-2 px-3 bg-gray-100 rounded-md">
                                        {{ $user->driver->license_number }}
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">License Expiry</label>
                                    <div class="mt-1 block w-full py-2 px-3 bg-gray-100 rounded-md">
                                        {{ $user->driver->license_expiry->format('M d, Y') }}
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Rating</label>
                                    <div class="mt-1 block w-full py-2 px-3 bg-gray-100 rounded-md">
                                        {{ $user->driver->rating ?? 'No ratings yet' }}
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Completed Rides</label>
                                    <div class="mt-1 block w-full py-2 px-3 bg-gray-100 rounded-md">
                                        {{ $user->driver->completed_rides }}
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Balance</label>
                                    <div class="mt-1 block w-full py-2 px-3 bg-gray-100 rounded-md">
                                        ${{ number_format($user->driver->balance, 2) }}
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Verification Status</label>
                                    <div class="mt-1 block w-full py-2 px-3 bg-gray-100 rounded-md">
                                        {{ $user->driver->is_verified ? 'Verified' : 'Not Verified' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <!-- Submit Button -->
                        <div class="flex justify-end mt-6">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                Update User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection