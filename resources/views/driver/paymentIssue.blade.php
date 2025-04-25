<!-- driver/paymentIssue.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>inTime - Report Payment Issue</title>
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <!-- Header/Navigation -->
    <header class="px-4 py-4 flex items-center justify-between bg-white shadow-sm">
        <div class="flex items-center space-x-8">
            <!-- Logo -->
            <a href="{{ route('driver.dashboard') }}" class="text-2xl font-bold">inTime</a>
            
            <!-- Navigation Links -->
            <nav class="hidden md:flex space-x-6">
                <a href="{{ route('driver.active.rides') }}" class="font-medium">Active Rides</a>
                <a href="{{ route('driver.history') }}" class="font-medium">History</a>
                <a href="{{ route('driver.earnings') }}" class="font-medium">Earnings</a>
            </nav>
        </div>
        
        <div class="h-10 w-10 rounded-full bg-gray-300 overflow-hidden">
            @if(Auth::user()->profile_picture)
                <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Profile" class="h-full w-full object-cover">
            @else
                <img src="/api/placeholder/40/40" alt="Profile" class="h-full w-full object-cover">
            @endif
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Header -->
            <div class="bg-red-600 px-6 py-4 text-white">
                <h1 class="text-xl font-bold">Report Payment Issue</h1>
                <p class="text-sm opacity-90">Report a problem with cash payment</p>
            </div>
            
            <!-- Passenger Details -->
            <div class="border-b border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <div class="h-12 w-12 bg-gray-200 rounded-full overflow-hidden mr-4">
                            @if($ride->passenger->user->profile_picture)
                                <img src="{{ asset('storage/' . $ride->passenger->user->profile_picture) }}" alt="Passenger" class="h-full w-full object-cover">
                            @else
                                <div class="h-full w-full flex items-center justify-center bg-gray-400 text-white">
                                    {{ strtoupper(substr($ride->passenger->user->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <h3 class="font-medium">{{ $ride->passenger->user->name }}</h3>
                            <div class="flex items-center text-sm text-gray-500">
                                <span>{{ $ride->passenger->rating ?? '5.0' }} </span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-500 ml-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-3 p-4 bg-red-50 border border-red-100 rounded-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Payment Issue</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <p>You are about to report an issue with the cash payment of <strong>MAD {{ number_format($ride->price, 2) }}</strong> for this ride. Our support team will review this case.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Report Form -->
            <div class="p-6">
                <form action="{{ route('driver.submit.payment.issue', $ride->id) }}" method="POST">
                    @csrf
                    
                    <div class="space-y-6">
                        <div>
                            <label for="issue_type" class="block text-sm font-medium text-gray-700 mb-1">Issue Type</label>
                            <select id="issue_type" name="issue_type" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="no_payment">No payment received</option>
                                <option value="partial_payment">Partial payment received</option>
                                <option value="incorrect_amount">Incorrect amount</option>
                                <option value="counterfeit">Suspected counterfeit</option>
                                <option value="other">Other issue</option>
                            </select>
                        </div>
                        
                        <div id="partial_payment_amount_container" class="hidden">
                            <label for="amount_received" class="block text-sm font-medium text-gray-700 mb-1">Amount Received (MAD)</label>
                            <input type="number" name="amount_received" id="amount_received" step="0.01" min="0" max="{{ $ride->price }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea id="description" name="description" rows="4" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Please provide details about the payment issue"></textarea>
                        </div>
                        
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="contacted_passenger" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">I have already tried to resolve this issue with the passenger</span>
                            </label>
                        </div>
                        
                        <div class="flex flex-col space-y-3">
                            <button type="submit" class="w-full py-3 px-4 bg-red-600 hover:bg-red-700 text-white font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                                Submit Report
                            </button>
                            
                            <a href="{{ route('driver.confirm.cash.payment', $ride->id) }}" class="text-center w-full py-3 px-4 border border-gray-300 text-gray-700 font-medium rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                                Go Back
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const issueTypeSelect = document.getElementById('issue_type');
            const partialPaymentContainer = document.getElementById('partial_payment_amount_container');
            
            // Show/hide partial payment amount field based on issue type
            issueTypeSelect.addEventListener('change', function() {
                if (this.value === 'partial_payment') {
                    partialPaymentContainer.classList.remove('hidden');
                } else {
                    partialPaymentContainer.classList.add('hidden');
                }
            });
        });
    </script>
</body>
</html>