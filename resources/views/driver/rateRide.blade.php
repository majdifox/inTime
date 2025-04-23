<!-- resources/views/driver/rateRide.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>inTime - Rate Your Passenger</title>
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <!-- Header/Navigation -->


    <!-- Main Content -->
    <main class="container mx-auto px-4 py-20">
        <div class="max-w-xl mx-auto">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h1 class="text-2xl font-bold mb-6">Rate Your Passenger</h1>
                
                <!-- Passenger info section -->
                <div class="flex items-center space-x-4 mb-6">
                    <div class="h-16 w-16 rounded-full bg-gray-200 overflow-hidden">
                        @if($ride->passenger->user->profile_picture)
                            <img src="{{ asset('storage/' . $ride->passenger->user->profile_picture) }}" alt="Passenger" class="h-full w-full object-cover">
                        @else
                            <div class="h-full w-full flex items-center justify-center bg-gray-400 text-white">
                                {{ strtoupper(substr($ride->passenger->user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <h2 class="text-lg font-medium">{{ $ride->passenger->user->name }}</h2>
                        <div class="flex items-center">
                            <span class="text-sm text-gray-600 mr-2">{{ number_format($ride->passenger->rating ?? 5.0, 1) }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        </div>
                    </div>
                </div>
                
                <!-- Ride details section -->
                <div class="border-t border-gray-200 pt-4 mb-6">
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="flex-shrink-0 text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">{{ $ride->pickup_location }} → {{ $ride->dropoff_location }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="flex-shrink-0 text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V5z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">{{ $ride->dropoff_time->diffForHumans($ride->pickup_time, ['short' => true]) }} ride duration</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0 text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">MAD {{ number_format($ride->price, 2) }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Rating form -->
                <form action="{{ route('driver.submit.rating', $ride->id) }}" method="POST" id="rating-form">
                    @csrf
                    
                    <div class="mb-6">
                        <div class="text-center mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">How was your experience with this passenger?</label>
                            <p class="text-sm text-gray-500">You must submit a rating to continue</p>
                        </div>
                        <div class="flex items-center justify-center space-x-2">
                            <input type="hidden" name="rating" id="rating-value" value="5">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button" class="rating-star" data-value="{{ $i }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                </button>
                            @endfor
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">Comments (Optional)</label>
                        <textarea id="comment" name="comment" rows="4" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Share your experience with this passenger..."></textarea>
                    </div>
                    
                    <div class="flex justify-between">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2.5 rounded-md font-medium hover:bg-blue-700 transition w-full">Submit Rating</button>
                    </div>
                </form>
            </div>

            <div class="mt-4 text-center">
                <p class="text-sm text-gray-600">Your rating helps other drivers and is visible on the passenger's profile.</p>
                <p class="text-sm text-red-600 font-medium">You must submit a rating to continue using inTime</p>
            </div>
        </div>
    </main>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ratingStars = document.querySelectorAll('.rating-star');
            const ratingValue = document.getElementById('rating-value');
            const ratingForm = document.getElementById('rating-form');
            
            // Prevent navigation away from page
            window.addEventListener('beforeunload', function(e) {
                e.preventDefault();
                e.returnValue = 'You must rate your passenger before leaving this page. Your rating helps other drivers.';
            });
            
            // Star rating functionality
            if (ratingStars && ratingValue) {
                ratingStars.forEach(star => {
                    star.addEventListener('click', function() {
                        const value = this.getAttribute('data-value');
                        ratingValue.value = value;
                        
                        // Update star visuals
                        ratingStars.forEach(s => {
                            const starValue = s.getAttribute('data-value');
                            const svg = s.querySelector('svg');
                            if (starValue <= value) {
                                svg.classList.add('text-yellow-400');
                                svg.classList.remove('text-gray-300');
                            } else {
                                svg.classList.add('text-gray-300');
                                svg.classList.remove('text-yellow-400');
                            }
                        });
                    });
                });
            }

            // Form submission
            if (ratingForm) {
                ratingForm.addEventListener('submit', function() {
                    // Remove the navigation warning once the form is submitted
                    window.removeEventListener('beforeunload', function() {});
                });
            }
        });
    </script>
</body>
</html>