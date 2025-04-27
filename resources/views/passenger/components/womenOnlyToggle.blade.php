<!-- passenger/components/womenOnlyToggle.blade.php -->
@if(Auth::user()->gender === 'female')
<div class="mb-6 p-4 bg-white shadow rounded-lg">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-medium text-gray-900">Women-Only Rides</h3>
            <p class="text-sm text-gray-500 mt-1">When enabled, you'll only see female drivers who have women-only mode active</p>
        </div>
        <button type="button" id="women-only-toggle" class="relative inline-flex h-6 w-11 items-center rounded-full {{ Auth::user()->women_only_rides ? 'bg-pink-500' : 'bg-gray-300' }} transition-colors duration-300">
            <span id="women-only-circle" class="inline-block h-5 w-5 transform rounded-full bg-white shadow-md transition-transform duration-300 {{ Auth::user()->women_only_rides ? 'translate-x-5' : 'translate-x-1' }}"></span>
        </button>
    </div>
    
    <div class="mt-4 bg-pink-50 p-4 rounded-md">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-pink-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-pink-800">Safety First</h3>
                <div class="mt-2 text-sm text-pink-700">
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Only female drivers with women-only mode enabled will be visible</li>
                        <li>Drivers will have a pink icon next to their profile</li>
                        <li>This feature is designed to provide an additional layer of security</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
   document.addEventListener('DOMContentLoaded', function() {
    const toggleButton = document.getElementById('women-only-toggle');
    
    if (toggleButton) {
        toggleButton.addEventListener('click', function() {
            // Get CSRF token
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Get current state visually
            const isCurrentlyEnabled = toggleButton.classList.contains('bg-pink-500');
            
            // Update UI immediately
            if (isCurrentlyEnabled) {
                // Currently enabled, switching to disabled
                toggleButton.classList.remove('bg-pink-500');
                toggleButton.classList.add('bg-gray-300');
                document.getElementById('women-only-toggle-dot').classList.remove('translate-x-5');
                document.getElementById('women-only-toggle-dot').classList.add('translate-x-1');
                document.getElementById('women-only-status').innerHTML = 'Status: <span class="text-gray-600 font-medium">Disabled</span>';
            } else {
                // Currently disabled, switching to enabled
                toggleButton.classList.remove('bg-gray-300');
                toggleButton.classList.add('bg-pink-500');
                document.getElementById('women-only-toggle-dot').classList.remove('translate-x-1');
                document.getElementById('women-only-toggle-dot').classList.add('translate-x-5');
                document.getElementById('women-only-status').innerHTML = 'Status: <span class="text-pink-600 font-medium">Enabled</span>';
            }
            
            // Send the AJAX request using the simple route
            fetch('/simple-toggle-women-only', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({})
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Toggle successful:', data);
                // No need to update UI here since we already did it
            })
            .catch(error => {
                console.error('Error:', error);
                
                // Revert UI on error
                if (isCurrentlyEnabled) {
                    // Revert back to enabled
                    toggleButton.classList.add('bg-pink-500');
                    toggleButton.classList.remove('bg-gray-300');
                    document.getElementById('women-only-toggle-dot').classList.add('translate-x-5');
                    document.getElementById('women-only-toggle-dot').classList.remove('translate-x-1');
                    document.getElementById('women-only-status').innerHTML = 'Status: <span class="text-pink-600 font-medium">Enabled</span>';
                } else {
                    // Revert back to disabled
                    toggleButton.classList.add('bg-gray-300');
                    toggleButton.classList.remove('bg-pink-500');
                    document.getElementById('women-only-toggle-dot').classList.add('translate-x-1');
                    document.getElementById('women-only-toggle-dot').classList.remove('translate-x-5');
                    document.getElementById('women-only-status').innerHTML = 'Status: <span class="text-gray-600 font-medium">Disabled</span>';
                }
                
                // Show error message
                alert('Failed to update preference. Please try again.');
            });
        });
    }
});
</script>
@endif