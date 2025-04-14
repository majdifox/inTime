{{-- resources/views/passenger/nearbyDrivers.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Nearby Drivers</span>
                        <a href="{{ route('passenger.dashboard') }}" class="btn btn-sm btn-secondary">Back</a>
                    </div>
                </div>

                <div class="card-body">
                    @if(!$has_coordinates)
                        <div class="alert alert-info">
                            Please set your pickup location to see nearby drivers.
                        </div>
                        
                        <form method="GET" action="{{ route('passenger.nearby.drivers') }}">
                            <div class="form-group">
                                <label for="latitude">Latitude</label>
                                <input type="text" class="form-control" id="latitude" name="latitude" required>
                            </div>
                            <div class="form-group">
                                <label for="longitude">Longitude</label>
                                <input type="text" class="form-control" id="longitude" name="longitude" required>
                            </div>
                            <div class="form-group">
                                <label for="vehicle_type">Vehicle Type</label>
                                <select class="form-control" id="vehicle_type" name="vehicle_type">
                                    @foreach($vehicleTypes as $type)
                                        <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Search</button>
                        </form>
                    @else
                        <div class="mb-4">
                            <form method="GET" action="{{ route('passenger.nearby.drivers') }}" class="form-inline">
                                <input type="hidden" name="latitude" value="{{ $latitude }}">
                                <input type="hidden" name="longitude" value="{{ $longitude }}">
                                <div class="form-group mr-2">
                                    <label for="vehicle_type" class="mr-2">Vehicle Type:</label>
                                    <select class="form-control" id="vehicle_type" name="vehicle_type" onchange="this.form.submit()">
                                        @foreach($vehicleTypes as $type)
                                            <option value="{{ $type }}" {{ $vehicle_type == $type ? 'selected' : '' }}>
                                                {{ ucfirst($type) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="button" class="btn btn-primary ml-2" id="refreshDrivers">
                                    <i class="fas fa-sync-alt"></i> Refresh
                                </button>
                            </form>
                        </div>
                        
                        <div class="alert alert-info">
                            <strong>Showing drivers near:</strong> {{ $latitude }}, {{ $longitude }}
                        </div>
                        
                        @if(count($drivers) > 0)
                            <div class="row" id="drivers-container">
                                @foreach($drivers as $driver)
                                    <div class="col-md-6 mb-4">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="mr-3">
                                                        @if($driver['profile_picture'])
                                                            <img src="{{ asset('storage/' . $driver['profile_picture']) }}" 
                                                                alt="{{ $driver['name'] }}" class="rounded-circle" 
                                                                style="width: 60px; height: 60px; object-fit: cover;">
                                                        @else
                                                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center"
                                                                style="width: 60px; height: 60px;">
                                                                {{ strtoupper(substr($driver['name'], 0, 1)) }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <h5 class="mb-0">{{ $driver['name'] }}</h5>
                                                        <div class="text-muted small">
                                                            <span class="text-warning">
                                                                @for($i = 0; $i < 5; $i++)
                                                                    @if($i < floor($driver['rating']))
                                                                        <i class="fas fa-star-half-alt"></i>
                                                                    @else
                                                                        <i class="far fa-star"></i>
                                                                    @endif
                                                                @endfor
                                                                {{ number_format($driver['rating'], 1) }}
                                                            </span>
                                                            • {{ $driver['completed_rides'] }} rides
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="driver-details mb-3">
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <p class="mb-1"><strong>Vehicle:</strong></p>
                                                            <p class="mb-1">{{ $driver['vehicle']['make'] }} {{ $driver['vehicle']['model'] }}</p>
                                                            <p class="mb-0">{{ $driver['vehicle']['color'] }} • {{ $driver['vehicle']['plate_number'] }}</p>
                                                        </div>
                                                        <div class="col-6">
                                                            <p class="mb-1"><strong>Distance:</strong></p>
                                                            <p class="mb-1">{{ number_format($driver['distance_km'], 1) }} km away</p>
                                                            <p class="mb-0">ETA: {{ $driver['eta_minutes'] }} mins</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                @if($driver['women_only_driver'])
                                                    <div class="badge badge-info mb-3">Women Only Driver</div>
                                                @endif
                                                
                                                <a href="{{ route('passenger.book') }}?driver_id={{ $driver['id'] }}&vehicle_type={{ $driver['vehicle']['type'] }}" 
                                                   class="btn btn-primary btn-block">
                                                    Request This Driver
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <h5><i class="fas fa-exclamation-triangle mr-2"></i> No drivers available</h5>
                                <p class="mb-0">There are no available drivers of type <strong>{{ ucfirst($vehicle_type) }}</strong> in your area right now. 
                                Try a different vehicle type or check back later.</p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if($has_coordinates)
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle refresh button
    document.getElementById('refreshDrivers').addEventListener('click', function() {
        window.location.reload();
    });
    
    // Auto-refresh every 30 seconds
    setTimeout(function() {
        window.location.reload();
    }, 30000);
});
</script>
@endif
@endsection-star"></i>
                                                                    @elseif($i < $driver['rating'])
                                                                        <i class="fas fa-star"></i>
                                                                    @else   
                                                                        <i class="far fa-star"></i>
                                                                    @endif                                                          