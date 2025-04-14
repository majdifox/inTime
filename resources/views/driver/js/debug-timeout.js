// Add this script to your dashboard page (temporarily) to debug request timeouts
// Save this as debug-timeout.js and include it in your dashboard
document.addEventListener('DOMContentLoaded', function() {
    // Override countdown timers to log information
    const originalCountdowns = document.querySelectorAll('.countdown');
    
    originalCountdowns.forEach(timer => {
        // Log the original timer data
        const requestedAt = new Date(timer.dataset.requested);
        const requestId = timer.dataset.requestId;
        console.log('Request ID:', requestId);
        console.log('Requested at:', requestedAt);
        console.log('Current time:', new Date());
        console.log('Time difference (ms):', new Date() - requestedAt);

        // Check server time synchronization
        fetch('/api/server-time')
            .then(response => response.json())
            .then(data => {
                const serverTime = new Date(data.server_time);
                console.log('Server time:', serverTime);
                console.log('Client/Server time difference (ms):', new Date() - serverTime);
            })
            .catch(err => console.error('Failed to fetch server time:', err));

        // Intercept status check requests
        const originalCheckStatus = window.checkRequestStatus;
        window.checkRequestStatus = function(reqId) {
            console.log(`Checking status for request ${reqId}`);
            return fetch(`/driver/request/${reqId}/status`)
                .then(response => response.json())
                .then(data => {
                    console.log(`Status for request ${reqId}:`, data);
                    return data;
                });
        };
        
        // Patch the timer update function to slow down expiration
        // This overrides the original updateTimer function
        function patchedUpdateTimer() {
            const now = new Date();
            // Original code calculates expiration as requestedAt + 15 seconds
            // Let's inspect this value
            const requestedAt = new Date(timer.dataset.requested);
            const expiresAt = new Date(requestedAt.getTime() + 15 * 1000);
            const remainingMs = expiresAt - now;
            
            console.log(`Request ${timer.dataset.requestId} timer:`, {
                requestedAt: requestedAt,
                expiresAt: expiresAt,
                now: now,
                remainingMs: remainingMs
            });
            
            // Check if request timestamps from server look correct
            fetch(`/driver/request/${timer.dataset.requestId}/status`)
                .then(response => response.json())
                .then(data => {
                    console.log(`Request ${timer.dataset.requestId} status:`, data);
                });
                
            // Normal timer logic remains the same
            if (remainingMs <= 0) {
                console.log(`Request ${timer.dataset.requestId} has expired naturally`);
                timer.textContent = 'Expired';
                timer.classList.remove('text-red-600');
                timer.classList.add('text-gray-400');
                
                // Don't disable buttons - this helps us test if the actual request is expired on server
                console.log("Request appears expired on client, but checking server status");
                
                // Instead, add a button to extend timer for testing
                const requestContainer = timer.closest('.border-l-4');
                if (requestContainer && !requestContainer.querySelector('.extend-timer')) {
                    const extendBtn = document.createElement('button');
                    extendBtn.textContent = 'Debug: Add 15s';
                    extendBtn.className = 'extend-timer bg-purple-500 text-white py-1 px-2 rounded text-xs mt-2';
                    extendBtn.onclick = function() {
                        // Reset the timer by updating the dataset
                        const newTime = new Date();
                        timer.dataset.requested = newTime.toISOString();
                        timer.textContent = '15s';
                        timer.classList.add('text-red-600');
                        timer.classList.remove('text-gray-400');
                        requestAnimationFrame(patchedUpdateTimer);
                        console.log(`Extended timer for request ${timer.dataset.requestId} to:`, newTime);
                    };
                    requestContainer.querySelector('.flex.space-x-3').appendChild(extendBtn);
                }
                
                return; // Stop updating
            }
            
            const remainingSec = Math.ceil(remainingMs / 1000);
            timer.textContent = remainingSec + 's';
            
            // Pulse effect when time is running out
            if (remainingSec <= 5) {
                timer.classList.add('animate-pulse');
            }
            
            requestAnimationFrame(patchedUpdateTimer);
        }
        
        // Replace the standard updateTimer with our patched version
        patchedUpdateTimer();
        
        // Find and intercept the accept/reject buttons for this request
        const requestContainer = timer.closest('.border-l-4');
        if (requestContainer) {
            const acceptBtn = requestContainer.querySelector('.accept-request');
            const rejectBtn = requestContainer.querySelector('.reject-request');
            
            if (acceptBtn) {
                const originalClick = acceptBtn.onclick;
                acceptBtn.onclick = function(e) {
                    e.preventDefault();
                    console.log(`Accept button clicked for request ${requestId}`);
                    
                    // Check the status before attempting to accept
                    fetch(`/driver/request/${requestId}/status`)
                        .then(response => response.json())
                        .then(data => {
                            console.log(`Pre-accept status check for ${requestId}:`, data);
                            if (data.status === 'pending') {
                                console.log('Request is still pending on server, proceeding with accept');
                                // Call the original click handler
                                if (originalClick) originalClick.call(this, e);
                                else this.click(); // Fallback
                            } else {
                                console.log('Request is not pending on server, cannot accept');
                                alert(`Request ${requestId} is ${data.status} on server. Cannot accept.`);
                            }
                        })
                        .catch(err => {
                            console.error('Error checking request status:', err);
                            alert('Failed to check request status. Try again.');
                        });
                };
            }
            
            if (rejectBtn) {
                const originalClick = rejectBtn.onclick;
                rejectBtn.onclick = function(e) {
                    e.preventDefault();
                    console.log(`Reject button clicked for request ${requestId}`);
                    // Same status check as for accept
                    fetch(`/driver/request/${requestId}/status`)
                        .then(response => response.json())
                        .then(data => {
                            console.log(`Pre-reject status check for ${requestId}:`, data);
                            if (data.status === 'pending') {
                                console.log('Request is still pending on server, proceeding with reject');
                                if (originalClick) originalClick.call(this, e);
                                else this.click();
                            } else {
                                console.log('Request is not pending on server, cannot reject');
                                alert(`Request ${requestId} is ${data.status} on server. Cannot reject.`);
                            }
                        })
                        .catch(err => {
                            console.error('Error checking request status:', err);
                            alert('Failed to check request status. Try again.');
                        });
                };
            }
        }
    });
});

// Add a server time endpoint to check time synchronization
// Note: You'll need to add this route to your web.php file:
/*
Route::get('/api/server-time', function() {
    return response()->json([
        'server_time' => now()->toIso8601String()
    ]);
});
*/