<!-- driver/components/womenOnlyToggle.blade.php -->
@if(Auth::user()->gender === 'female')
<div class="mb-6 p-4 bg-white shadow rounded-lg">
    <div class="flex flex-col space-y-4">
        <div>
            <h2 class="text-lg font-medium text-gray-900">Women-Only Driver Mode</h2>
            <p class="text-gray-600">When enabled, you'll only be matched with female passengers</p>
        </div>
        
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <button id="toggle-women-only" class="relative inline-flex h-6 w-11 items-center rounded-full {{ $driver->women_only_driver ? 'bg-pink-500' : 'bg-gray-300' }} transition-colors duration-300">
                    <span id="women-only-circle" class="inline-block h-5 w-5 transform rounded-full bg-white shadow-md transition-transform duration-300 {{ $driver->women_only_driver ? 'translate-x-5' : 'translate-x-1' }}"></span>
                </button>
                <span id="women-only-text" class="ml-2 font-medium">{{ $driver->women_only_driver ? 'Women-Only Mode On' : 'Women-Only Mode Off' }}</span>
            </div>
            
            <div class="px-2 py-1 rounded bg-pink-100 text-pink-800 text-xs">
                <span>For female drivers only</span>
            </div>
        </div>
    </div>
    
    <div class="mt-4 bg-gray-50 p-4 rounded-md">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-gray-800">How women-only mode works</h3>
                <div class="mt-2 text-sm text-gray-600">
                    <ul class="list-disc pl-5 space-y-1">
                        <li>You'll only be visible to female passengers</li>
                        <li>A pink icon will appear next to your profile</li>
                        <li>This mode is recommended if you have a "Women" vehicle type</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    @if($driver->vehicle && $driver->vehicle->type !== 'women' && $driver->women_only_driver)
    <div class="mt-4 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-md">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700">
                    For consistent matching, consider updating your vehicle type to "Women" in vehicle settings.
                </p>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle women-only mode
        const toggleWomenOnlyBtn = document.getElementById('toggle-women-only');
        const womenOnlyCircle = document.getElementById('women-only-circle');
        const womenOnlyText = document.getElementById('women-only-text');
        
        if (toggleWomenOnlyBtn) {
            toggleWomenOnlyBtn.addEventListener('click', function() {
                const isCurrentlyEnabled = toggleWomenOnlyBtn.classList.contains('bg-pink-500');
                const newState = !isCurrentlyEnabled;
                
                // Update UI immediately for responsiveness
                womenOnlyCircle.classList.toggle('translate-x-1', !newState);
                womenOnlyCircle.classList.toggle('translate-x-5', newState);
                toggleWomenOnlyBtn.classList.toggle('bg-gray-300', !newState);
                toggleWomenOnlyBtn.classList.toggle('bg-pink-500', newState);
                womenOnlyText.textContent = newState ? 'Women-Only Mode On' : 'Women-Only Mode Off';
                
                // Send AJAX request to update mode
                fetch('{{ route("driver.toggle.women.only") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        women_only_driver: newState
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                       
                        // If the update failed, revert the UI changes
                        womenOnlyCircle.classList.toggle('translate-x-1', isCurrentlyEnabled);
                        womenOnlyCircle.classList.toggle('translate-x-5', !isCurrentlyEnabled);
                        toggleWomenOnlyBtn.classList.toggle('bg-gray-300', isCurrentlyEnabled);
                        toggleWomenOnlyBtn.classList.toggle('bg-pink-500', !isCurrentlyEnabled);
                        womenOnlyText.textContent = isCurrentlyEnabled ? 'Women-Only Mode On' : 'Women-Only Mode Off';
                        
                        alert('Failed to update mode. Please try again.');
                    }
                })
                .catch(error => {
                    // If there was an error, revert the UI changes
                    womenOnlyCircle.classList.toggle('translate-x-1', isCurrentlyEnabled);
                    womenOnlyCircle.classList.toggle('translate-x-5', !isCurrentlyEnabled);
                    toggleWomenOnlyBtn.classList.toggle('bg-gray-300', isCurrentlyEnabled);
                    toggleWomenOnlyBtn.classList.toggle('bg-pink-500', !isCurrentlyEnabled);
                    womenOnlyText.textContent = isCurrentlyEnabled ? 'Women-Only Mode On' : 'Women-Only Mode Off';
                    
                    console.error('Error updating mode:', error);
                    alert('An error occurred while updating your mode');
                });
            });
        }
    });
</script>
@endif