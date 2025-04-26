<!-- passenger/ridePayment.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>inTime - Payment</title>
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Stripe JS -->
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body class="bg-gray-50">
    <!-- Header/Navigation -->
    <header class="px-4 py-4 flex items-center justify-between bg-white shadow-sm">
        <div class="flex items-center space-x-8">
            <!-- Logo -->
            <a href="{{ route('passenger.dashboard') }}" class="text-2xl font-bold">inTime</a>
            
            <!-- Navigation Links -->
            <nav class="hidden md:flex space-x-6">
                <a href="{{ route('passenger.history') }}" class="font-medium">Ride History</a>
                <a href="{{ route('passenger.profile.private') }}" class="font-medium">My Profile</a>
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
        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <div class="max-w-xl mx-auto bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Payment Header -->
            <div class="bg-blue-600 px-6 py-4 text-white">
                <h1 class="text-xl font-bold">Payment for Your Ride</h1>
                <p class="text-sm opacity-90">Complete payment to finish your ride</p>
            </div>
            
            <!-- Ride Details -->
            <div class="border-b border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <div class="h-12 w-12 bg-gray-200 rounded-full overflow-hidden mr-4">
                            @if($ride->driver->user->profile_picture)
                                <img src="{{ asset('storage/' . $ride->driver->user->profile_picture) }}" alt="Driver" class="h-full w-full object-cover">
                            @else
                                <div class="h-full w-full flex items-center justify-center bg-gray-400 text-white">
                                    {{ strtoupper(substr($ride->driver->user->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <h3 class="font-medium">{{ $ride->driver->user->name }}</h3>
                            <div class="flex items-center text-sm text-gray-500">
                                <span>{{ $ride->driver->rating }} </span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-500 ml-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="block font-medium">{{ ucfirst($ride->vehicle_type) }}</span>
                        <span class="text-sm text-gray-500">{{ $ride->driver->vehicle->make }} {{ $ride->driver->vehicle->model }}</span>
                    </div>
                </div>
                
                <div class="space-y-2">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mt-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-gray-500">{{ $ride->dropoff_time->format('l, F j, Y') }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mt-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-gray-500">{{ $ride->pickup_time->format('g:i A') }} - {{ $ride->dropoff_time->format('g:i A') }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mt-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <circle cx="12" cy="12" r="8" stroke-width="2" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium">From</p>
                            <p class="text-sm text-gray-500">{{ $ride->pickup_location }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mt-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium">To</p>
                            <p class="text-sm text-gray-500">{{ $ride->dropoff_location }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Fare Details -->
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-medium mb-4">Fare Breakdown</h2>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Base fare</span>
                        <span>MAD {{ number_format($ride->base_fare, 2) }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600">Distance ({{ number_format($ride->distance_in_km, 1) }} km)</span>
                        <span>MAD {{ number_format($ride->distance_in_km * $ride->per_km_price, 2) }}</span>
                    </div>
                    
                    @if($ride->wait_time_minutes > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Waiting time ({{ $ride->wait_time_minutes }} min)</span>
                        <span>MAD {{ number_format($ride->wait_time_minutes * 0.5, 2) }}</span>
                    </div>
                    @endif
                    
                    @if($ride->surge_multiplier > 1)
                    <div class="flex justify-between text-amber-600">
                        <span>Surge pricing ({{ number_format($ride->surge_multiplier, 1) }}x)</span>
                        <span>+{{ number_format(($ride->surge_multiplier - 1) * 100, 0) }}%</span>
                    </div>
                    @endif
                    
                    <div class="border-t border-gray-200 pt-3 mt-3 flex justify-between font-medium">
                        <span>Total</span>
                        <span>MAD {{ number_format($ride->price, 2) }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Payment Methods -->
            <div class="p-6">
                <form id="payment-form" action="{{ route('passenger.ride.process-payment', $ride->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="payment_method_id" id="payment_method_id" value="">
                    <input type="hidden" name="setup_intent_id" id="setup_intent_id" value="">
                    <input type="hidden" id="setup-intent-route" value="{{ route('create-setup-intent') }}">
                    
                    <h2 class="text-lg font-medium mb-4">Payment Method</h2>
                    
                    <!-- Saved Cards Section -->
                    <div id="saved-cards-section" class="mb-6 {{ count($savedCards) ? '' : 'hidden' }}">
                        <h3 class="text-md font-medium mb-2">Your Cards</h3>
                        
                        <div class="space-y-2">
                            @foreach($savedCards as $card)
                            <label class="relative bg-white border rounded-lg p-3 cursor-pointer hover:border-blue-500 transition flex items-center">
                                <input type="radio" name="saved_card" value="{{ $card->stripe_payment_method_id }}" class="h-5 w-5 text-blue-600" {{ $loop->first ? 'checked' : '' }}>
                                <div class="ml-3 flex items-center">
                                    @if($card->brand == 'visa')
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5e/Visa_Inc._logo.svg/2560px-Visa_Inc._logo.svg.png" alt="Visa" class="h-7 mr-2">
                                    @elseif($card->brand == 'mastercard')
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2a/Mastercard-logo.svg/1280px-Mastercard-logo.svg.png" alt="Mastercard" class="h-7 mr-2">
                                    @elseif($card->brand == 'amex')
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/f/fa/American_Express_logo_%282018%29.svg/1280px-American_Express_logo_%282018%29.svg.png" alt="American Express" class="h-7 mr-2">
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                        </svg>
                                    @endif
                                    <span>•••• {{ $card->last4 }}</span>
                                    <span class="ml-2 text-gray-500 text-sm">Expires {{ $card->exp_month }}/{{ $card->exp_year }}</span>
                                </div>
                            </label>
                            @endforeach
                        </div>
                        
                        <div class="flex items-center my-4">
                            <hr class="flex-grow border-gray-200">
                            <span class="px-2 text-sm text-gray-500">or</span>
                            <hr class="flex-grow border-gray-200">
                        </div>
                    </div>
                    
                    <!-- New Card Section -->
                    <div id="new-card-section">
                        <h3 class="text-md font-medium mb-2">Add New Card</h3>
                        
                        <div class="space-y-4">
                            <div id="card-element" class="border rounded-md p-3 bg-white">
                                <!-- Stripe card element will be inserted here -->
                            </div>
                            
                            <div id="card-errors" class="text-red-600 text-sm" role="alert"></div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" id="save-card" name="save_card" class="h-4 w-4 text-blue-600 rounded">
                                <label for="save-card" class="ml-2 text-sm text-gray-700">Save card for future rides</label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="mt-8">
                        <div class="flex flex-col space-y-3">
                            <button type="submit" id="submit-button" class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                Pay MAD {{ number_format($ride->price, 2) }}
                            </button>
                            
                            <a href="{{ route('passenger.dashboard') }}" class="text-center w-full py-3 px-4 border border-gray-300 text-gray-700 font-medium rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                                Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <!-- Loading Overlay -->
    <div id="loading-overlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white p-5 rounded-lg shadow-lg">
            <div class="flex items-center space-x-3">
                <svg class="animate-spin h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-lg font-medium">Processing payment...</span>
            </div>
        </div>
    </div>

    <!-- Stripe Integration Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Stripe
            const stripe = Stripe('{{ config("services.stripe.key") }}');
            const elements = stripe.elements();
            
            // Create card element
            const cardElement = elements.create('card', {
                style: {
                    base: {
                        color: '#32325d',
                        fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                        fontSmoothing: 'antialiased',
                        fontSize: '16px',
                        '::placeholder': {
                            color: '#aab7c4'
                        }
                    },
                    invalid: {
                        color: '#fa755a',
                        iconColor: '#fa755a'
                    }
                }
            });
            
            // Mount the card element
            cardElement.mount('#card-element');
            
            // Handle real-time validation errors
            cardElement.on('change', function(event) {
                const displayError = document.getElementById('card-errors');
                if (event.error) {
                    displayError.textContent = event.error.message;
                } else {
                    displayError.textContent = '';
                }
            });
            
            // Form submission handler
            const form = document.getElementById('payment-form');
            const submitButton = document.getElementById('submit-button');
            const loadingOverlay = document.getElementById('loading-overlay');
            const saveCardCheckbox = document.getElementById('save-card');
            const setupIntentRoute = document.getElementById('setup-intent-route').value;
            
            form.addEventListener('submit', async function(event) {
                event.preventDefault();
                
                // Disable submit button and show loading
                submitButton.disabled = true;
                loadingOverlay.classList.remove('hidden');
                
                try {
                    // Check if using a saved card
                    const savedCardRadio = document.querySelector('input[name="saved_card"]:checked');
                    
                    if (savedCardRadio) {
                        // Using a saved card
                        document.getElementById('payment_method_id').value = savedCardRadio.value;
                        form.submit();
                    } else {
                        // Create Setup Intent if saving card
                        if (saveCardCheckbox.checked) {
                            const response = await fetch(setupIntentRoute, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                }
                            });
                            
                            const setupData = await response.json();
                            
                            if (setupData.error) {
                                throw new Error(setupData.error);
                            }
                            
                            const setupIntent = setupData.client_secret;
                            
                            // Create payment method and confirm setup intent
                            const result = await stripe.confirmCardSetup(setupIntent, {
                                payment_method: {
                                    card: cardElement,
                                    billing_details: {
                                        name: '{{ Auth::user()->name }}'
                                    }
                                }
                            });
                            
                            if (result.error) {
                                throw result.error;
                            }
                            
                            document.getElementById('payment_method_id').value = result.setupIntent.payment_method;
                            document.getElementById('setup_intent_id').value = result.setupIntent.id;
                        } else {
                            // Just create a payment method without saving
                            const { paymentMethod, error } = await stripe.createPaymentMethod({
                                type: 'card',
                                card: cardElement,
                                billing_details: {
                                    name: '{{ Auth::user()->name }}'
                                }
                            });
                            
                            if (error) {
                                throw error;
                            }
                            
                            document.getElementById('payment_method_id').value = paymentMethod.id;
                        }
                        
                        // Submit the form
                        form.submit();
                    }
                } catch (error) {
                    console.error('Payment error:', error);
                    
                    // Show error to user
                    const cardErrors = document.getElementById('card-errors');
                    cardErrors.textContent = error.message || 'An error occurred. Please try again.';
                    
                    // Reset UI
                    submitButton.disabled = false;
                    loadingOverlay.classList.add('hidden');
                }
            });
            
            // Handle saved card selection
            const savedCardInputs = document.querySelectorAll('input[name="saved_card"]');
            if (savedCardInputs.length > 0) {
                savedCardInputs.forEach(input => {
                    input.addEventListener('change', function() {
                        if (this.checked) {
                            // Hide card element if a saved card is selected
                            document.getElementById('new-card-section').style.display = 'none';
                        }
                    });
                });
                
                // Show "Add new card" option
                const newCardRadio = document.createElement('label');
                newCardRadio.className = 'relative bg-white border rounded-lg p-3 cursor-pointer hover:border-blue-500 transition flex items-center mt-2';
                newCardRadio.innerHTML = `
                    <input type="radio" name="use_new_card" class="h-5 w-5 text-blue-600">
                    <div class="ml-3 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        <span>Add a new card</span>
                    </div>
                `;
                
                document.querySelector('#saved-cards-section .space-y-2').appendChild(newCardRadio);
                
                // Hide new card section by default when saved cards exist
                document.getElementById('new-card-section').style.display = 'none';
                
                // Show card element when "Add new card" is selected
                newCardRadio.querySelector('input').addEventListener('change', function() {
                    if (this.checked) {
                        document.getElementById('new-card-section').style.display = 'block';
                        // Uncheck all saved cards
                        savedCardInputs.forEach(input => {
                            input.checked = false;
                        });
                    }
                });
            }
        });
    </script>
</body>
</html>