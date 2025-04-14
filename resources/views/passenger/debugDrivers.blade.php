
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Debug Available Drivers</div>

                <div class="card-body">
                    <form method="GET" action="{{ route('passenger.debug.drivers') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="lat">Pickup Latitude</label>
                                    <input type="text" class="form-control" id="lat" name="lat" value="{{ request('lat', '') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="lng">Pickup Longitude</label>
                                    <input type="text" class="form-control" id="lng" name="lng" value="{{ request('lng', '') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary mt-4">Filter by Location</button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Driver</th>
                                    <th>Status</th>
                                    <th>Vehicle Type</th>
                                    <th>Location Updated</th>
                                    <th>Reasons</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($results as $driver)
                                <tr class="{{ $driver['status'] === 'available' ? 'table-success' : 'table-danger' }}">
                                    <td>{{ $driver['id'] }}</td>
                                    <td>{{ $driver['name'] }}</td>
                                    <td>{{ ucfirst($driver['status']) }}</td>
                                    <td>{{ ucfirst($driver['vehicle_type']) }}</td>
                                    <td>{{ $driver['location_updated'] }}</td>
                                    <td>
                                        @if(count($driver['reasons']) > 0)
                                            <ul class="mb-0">
                                                @foreach($driver['reasons'] as $reason)
                                                    <li>{{ $reason }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="text-success">Available for rides</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
