<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Driver Availability Debug</h4>
                        <button class="btn btn-light btn-sm" onclick="window.location.reload()">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="alert alert-info">
                        <h5>Current User Information</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>User ID:</strong> {{ $debugInfo['user_id'] ?? 'N/A' }}</p>
                                <p><strong>Gender:</strong> {{ $debugInfo['gender'] ?? 'Not specified' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Women-only rides:</strong> {{ $debugInfo['women_only_rides'] ?? 'No' }}</p>
                                <p><strong>Passenger ID:</strong> {{ $debugInfo['passenger_id'] ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="locationInput">Enter Location (Latitude, Longitude)</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="locationInput" placeholder="e.g. 32.2603695, -9.2453128">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" onclick="useCustomLocation()">Check</button>
                                    </div>
                                </div>
                                <small class="form-text text-muted">
                                    Enter coordinates to check driver availability from a specific location.
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Actions</label>
                                <div class="d-flex">
                                    <button class="btn btn-success mr-2" onclick="useCurrentLocation()">
                                        <i class="fas fa-map-marker-alt"></i> Use My Location
                                    </button>
                                    <button class="btn btn-warning" onclick="clearSessionData()">
                                        <i class="fas fa-trash"></i> Clear Session Data
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h5>Available Drivers: {{ count(array_filter($results, function($driver) { return $driver['status'] === 'available'; })) }}</h5>
                    <h5>Total Drivers: {{ count($results) }}</h5>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Driver</th>
                                    <th>Status</th>
                                    <th>Vehicle Type</th>
                                    <th>Location Updated</th>
                                    <th>Reasons</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($results as $driver)
                                <tr class="{{ $driver['status'] === 'available' ? 'table-success' : 'table-danger' }}">
                                    <td>{{ $driver['id'] }}</td>
                                    <td>{{ $driver['name'] }}</td>
                                    <td>
                                        @if($driver['status'] === 'available')
                                            <span class="badge badge-success">Available</span>
                                        @else
                                            <span class="badge badge-danger">Unavailable</span>
                                        @endif
                                    </td>
                                    <td>{{ ucfirst($driver['vehicle_type']) }}</td>
                                    <td>{{ $driver['location_updated'] }}</td>
                                    <td>
                                        @if(!empty($driver['reasons']))
                                            <ul class="mb-0 pl-3">
                                                @foreach($driver['reasons'] as $reason)
                                                    <li>{{ $reason }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="text-success">Available for rides</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($driver['status'] === 'available')
                                            <a href="{{ route('passenger.select.driver', [
                                                'vehicle_type' => $driver['vehicle_type'],
                                                'driver_id' => $driver['id']
                                            ]) }}" class="btn btn-sm btn-primary">
                                                Select Driver
                                            </a>
                                        @else
                                            <button class="btn btn-sm btn-secondary" disabled>
                                                Not Available
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">No drivers found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function useCurrentLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                window.location.href = "{{ route('passenger.debug.drivers') }}?lat=" + lat + "&lng=" + lng;
            },
            function(error) {
                alert("Error getting your location: " + error.message);
            }
        );
    } else {
        alert("Geolocation is not supported by this browser.");
    }
}

function useCustomLocation() {
    const locationInput = document.getElementById('locationInput').value;
    const coords = locationInput.split(',').map(coord => coord.trim());
    
    if (coords.length !== 2 || isNaN(coords[0]) || isNaN(coords[1])) {
        alert("Please enter valid coordinates in format: latitude, longitude");
        return;
    }
    
    window.location.href = "{{ route('passenger.debug.drivers') }}?lat=" + coords[0] + "&lng=" + coords[1];
}


</script>