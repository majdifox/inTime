<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css">
     <!-- Include Tailwind CSS -->
     @vite('resources/css/app.css')
<div class="container-fluid">
    <!-- Dashboard Stats -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Drivers</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_drivers'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-car fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Passengers</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_passengers'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Drivers</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending_drivers'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Completed Rides</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['completed_rides'] }}/{{ $stats['total_rides'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-route fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs for Drivers and Passengers -->
    <ul class="nav nav-tabs mb-4" id="userTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="drivers-tab" data-bs-toggle="tab" data-bs-target="#drivers" type="button" role="tab" aria-controls="drivers" aria-selected="true">Drivers</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="passengers-tab" data-bs-toggle="tab" data-bs-target="#passengers" type="button" role="tab" aria-controls="passengers" aria-selected="false">Passengers</button>
        </li>
    </ul>

    <div class="tab-content" id="userTabsContent">
        <!-- Drivers Tab -->
        <div class="tab-pane fade show active" id="drivers" role="tabpanel" aria-labelledby="drivers-tab">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Drivers Management</h6>
                    <div class="input-group" style="width: 250px;">
                        <input type="text" class="form-control" id="driverSearch" placeholder="Search...">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="driversTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Rating</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($drivers as $driver)
                                <tr data-user-id="{{ $driver->id }}">
                                    <td>{{ $driver->id }}</td>
                                    <td>{{ $driver->name }}</td>
                                    <td>{{ $driver->email }}</td>
                                    <td>{{ $driver->phone }}</td>
                                    <td>
                                        <span class="badge 
                                            @if($driver->account_status == 'activated') bg-success 
                                            @elseif($driver->account_status == 'deactivated') bg-secondary 
                                            @elseif($driver->account_status == 'pending') bg-warning 
                                            @elseif($driver->account_status == 'suspended') bg-danger 
                                            @else bg-dark @endif">
                                            {{ ucfirst($driver->account_status) }}
                                        </span>
                                    </td>
                                    <td>{{ $driver->driver->rating ?? 'N/A' }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-info dropdown-toggle" data-bs-toggle="dropdown">
                                                Status
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item status-action" href="#" data-action="activated">
                                                    <i class="fas fa-check-circle text-success"></i> Activate
                                                </a>
                                                <a class="dropdown-item status-action" href="#" data-action="deactivated">
                                                    <i class="fas fa-times-circle text-secondary"></i> Deactivate
                                                </a>
                                                <a class="dropdown-item status-action" href="#" data-action="suspended">
                                                    <i class="fas fa-ban text-danger"></i> Suspend
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item status-action" href="#" data-action="deleted">
                                                    <i class="fas fa-trash text-danger"></i> Delete
                                                </a>
                                            </div>
                                        </div>
                                        
                                        @if($driver->driver)
                                        <button class="btn btn-sm btn-primary view-docs-btn" data-driver-id="{{ $driver->driver->id }}">
                                            <i class="fas fa-file-alt"></i>
                                        </button>
                                        
                                        @if(!$driver->driver->is_verified)
                                        <button class="btn btn-sm btn-success verify-driver-btn" data-driver-id="{{ $driver->driver->id }}">
                                            <i class="fas fa-user-check"></i>
                                        </button>
                                        @endif
                                        @endif
                                        
                                        <button class="btn btn-sm btn-warning edit-user-btn">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        
                                        <button class="btn btn-sm btn-danger delete-user-btn">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $drivers->links() }}
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Passengers Tab -->
        <div class="tab-pane fade" id="passengers" role="tabpanel" aria-labelledby="passengers-tab">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Passengers Management</h6>
                    <div class="input-group" style="width: 250px;">
                        <input type="text" class="form-control" id="passengerSearch" placeholder="Search...">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="passengersTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Rides</th>
                                    <th>Rating</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($passengers as $passenger)
                                <tr data-user-id="{{ $passenger->id }}">
                                    <td>{{ $passenger->id }}</td>
                                    <td>{{ $passenger->name }}</td>
                                    <td>{{ $passenger->email }}</td>
                                    <td>{{ $passenger->phone }}</td>
                                    <td>
                                        <span class="badge 
                                            @if($passenger->account_status == 'activated') bg-success 
                                            @elseif($passenger->account_status == 'deactivated') bg-secondary 
                                            @elseif($passenger->account_status == 'pending') bg-warning 
                                            @elseif($passenger->account_status == 'suspended') bg-danger 
                                            @else bg-dark @endif">
                                            {{ ucfirst($passenger->account_status) }}
                                        </span>
                                    </td>
                                    <td>{{ $passenger->passenger->total_rides ?? 'N/A' }}</td>
                                    <td>{{ $passenger->passenger->rating ?? 'N/A' }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-info dropdown-toggle" data-bs-toggle="dropdown">
                                                Status
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item status-action" href="#" data-action="activated">
                                                    <i class="fas fa-check-circle text-success"></i> Activate
                                                </a>
                                                <a class="dropdown-item status-action" href="#" data-action="deactivated">
                                                    <i class="fas fa-times-circle text-secondary"></i> Deactivate
                                                </a>
                                                <a class="dropdown-item status-action" href="#" data-action="suspended">
                                                    <i class="fas fa-ban text-danger"></i> Suspend
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item status-action" href="#" data-action="deleted">
                                                    <i class="fas fa-trash text-danger"></i> Delete
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <button class="btn btn-sm btn-warning edit-user-btn">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        
                                        <button class="btn btn-sm btn-danger delete-user-btn">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $passengers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Document View Modal -->
<div class="modal fade" id="documentModal" tabindex="-1" aria-labelledby="documentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="documentModalLabel">Driver Documents</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header bg-info text-white">
                                <h6 class="m-0">Driver Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-sm-4 fw-bold">Name:</div>
                                    <div class="col-sm-8" id="driver-name"></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 fw-bold">Email:</div>
                                    <div class="col-sm-8" id="driver-email"></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 fw-bold">Phone:</div>
                                    <div class="col-sm-8" id="driver-phone"></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 fw-bold">License:</div>
                                    <div class="col-sm-8" id="driver-license"></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 fw-bold">Expiry:</div>
                                    <div class="col-sm-8" id="driver-license-expiry"></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 fw-bold">Status:</div>
                                    <div class="col-sm-8" id="driver-status"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header bg-info text-white">
                                <h6 class="m-0">Vehicle Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-sm-4 fw-bold">Make:</div>
                                    <div class="col-sm-8" id="vehicle-make"></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 fw-bold">Model:</div>
                                    <div class="col-sm-8" id="vehicle-model"></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 fw-bold">Year:</div>
                                    <div class="col-sm-8" id="vehicle-year"></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 fw-bold">Color:</div>
                                    <div class="col-sm-8" id="vehicle-color"></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 fw-bold">Plate:</div>
                                    <div class="col-sm-8" id="vehicle-plate"></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 fw-bold">Type:</div>
                                    <div class="col-sm-8" id="vehicle-type"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h6 class="m-0">Driver License</h6>
                            </div>
                            <div class="card-body text-center">
                                <img id="license-image" src="" alt="Driver License" class="img-fluid img-thumbnail" style="max-height: 300px;">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h6 class="m-0">Vehicle Photo</h6>
                            </div>
                            <div class="card-body text-center">
                                <img id="vehicle-image" src="" alt="Vehicle Photo" class="img-fluid img-thumbnail" style="max-height: 300px;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="verifyDriverBtn">Verify Driver</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editUserForm">
                <div class="modal-body">
                    <input type="hidden" id="edit-user-id" name="user_id">
                    <div class="mb-3">
                        <label for="edit-name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="edit-name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="edit-email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="edit-phone" name="phone" required>
                    </div>
                    <!-- Driver-specific fields (will be shown/hidden dynamically) -->
                    <div id="driver-specific-fields" style="display: none;">
                        <hr>
                        <h6>Driver Details</h6>
                        <div class="mb-3">
                            <label for="edit-license-number" class="form-label">License Number</label>
                            <input type="text" class="form-control" id="edit-license-number" name="license_number">
                        </div>
                        <div class="mb-3">
                            <label for="edit-license-expiry" class="form-label">License Expiry</label>
                            <input type="date" class="form-control" id="edit-license-expiry" name="license_expiry">
                        </div>
                        <div class="mb-3">
                            <label for="edit-license-photo" class="form-label">License Photo</label>
                            <input type="file" class="form-control" id="edit-license-photo" name="license_photo">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteUserModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this user? This action cannot be undone.</p>
                <p><strong>User:</strong> <span id="delete-user-name"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>



@section('scripts')
<script>
    $(document).ready(function() {
        // Status action buttons
        $('.status-action').on('click', function(e) {
            e.preventDefault();
            
            const action = $(this).data('action');
            const userId = $(this).closest('tr').data('user-id');
            
            // Confirm before deleting
            if (action === 'deleted' && !confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                return;
            }
            
            $.ajax({
                url: "{{ route('admin.user.status', ['user' => ':userId']) }}".replace(':userId', userId),
                type: 'POST',
                data: {
                    status: action,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        // Update the badge in the table
                        let badgeClass = '';
                        
                        switch (action) {
                            case 'activated':
                                badgeClass = 'bg-success';
                                break;
                            case 'deactivated':
                                badgeClass = 'bg-secondary';
                                break;
                            case 'pending':
                                badgeClass = 'bg-warning';
                                break;
                            case 'suspended':
                                badgeClass = 'bg-danger';
                                break;
                            case 'deleted':
                                badgeClass = 'bg-dark';
                                break;
                        }
                        
                        $(`tr[data-user-id="${userId}"] td:nth-child(5) span`).removeClass().addClass(`badge ${badgeClass}`).text(action.charAt(0).toUpperCase() + action.slice(1));
                        
                        // Show success toast
                        toastr.success('User status updated successfully');
                        
                        // If deleted, you might want to remove the row or refresh the page
                        if (action === 'deleted') {
                            setTimeout(function() {
                                window.location.reload();
                            }, 1500);
                        }
                    }
                },
                error: function() {
                    toastr.error('Failed to update user status');
                }
            });
        });
        
        // View Driver Documents
        $('.view-docs-btn').on('click', function() {
            const driverId = $(this).data('driver-id');
            
            // Reset modal content
            $('#driver-name, #driver-email, #driver-phone, #driver-license, #driver-license-expiry, #driver-status').text('Loading...');
            $('#vehicle-make, #vehicle-model, #vehicle-year, #vehicle-color, #vehicle-plate, #vehicle-type').text('Loading...');
            $('#license-image, #vehicle-image').attr('src', '');
            
            // Store current driver ID for verify button
            $('#verifyDriverBtn').data('driver-id', driverId);
            
            // Show/hide verify button based on driver verification status
            const isVerified = !$(this).siblings('.verify-driver-btn').length;
            $('#verifyDriverBtn').toggle(!isVerified);
            
            $.ajax({
                url: "{{ route('admin.driver.documents', ['driver' => ':driverId']) }}".replace(':driverId', driverId),
                type: 'GET',
                success: function(response) {
                    // Fill driver info
                    $('#driver-name').text(response.user.name);
                    $('#driver-email').text(response.user.email);
                    $('#driver-phone').text(response.user.phone);
                    $('#driver-license').text(response.driver.license_number);
                    $('#driver-license-expiry').text(new Date(response.driver.license_expiry).toLocaleDateString());
                    
                    // Set status with badge
                    let statusBadge = '';
                    switch(response.user.account_status) {
                        case 'activated':
                            statusBadge = '<span class="badge bg-success">Activated</span>';
                            break;
                        case 'deactivated':
                            statusBadge = '<span class="badge bg-secondary">Deactivated</span>';
                            break;
                        case 'pending':
                            statusBadge = '<span class="badge bg-warning">Pending</span>';
                            break;
                        case 'suspended':
                            statusBadge = '<span class="badge bg-danger">Suspended</span>';
                            break;
                        default:
                            statusBadge = '<span class="badge bg-dark">Unknown</span>';
                    }
                    $('#driver-status').html(statusBadge);
                    
                    // Fill vehicle info if available
                    if (response.vehicle) {
                        $('#vehicle-make').text(response.vehicle.make);
                        $('#vehicle-model').text(response.vehicle.model);
                        $('#vehicle-year').text(response.vehicle.year);
                        $('#vehicle-color').text(response.vehicle.color);
                        $('#vehicle-plate').text(response.vehicle.plate_number);
                        $('#vehicle-type').text(response.vehicle.type);
                    } else {
                        $('#vehicle-make, #vehicle-model, #vehicle-year, #vehicle-color, #vehicle-plate, #vehicle-type').text('N/A');
                    }
                    
                    // Set images
                    if (response.license_photo_url) {
                        $('#license-image').attr('src', response.license_photo_url);
                    } else {
                        $('#license-image').attr('src', 'https://via.placeholder.com/400x300?text=No+License+Photo');
                    }
                    
                    if (response.vehicle_photo_url) {
                        $('#vehicle-image').attr('src', response.vehicle_photo_url);
                    } else {
                        $('#vehicle-image').attr('src', 'https://via.placeholder.com/400x300?text=No+Vehicle+Photo');
                    }
                    
                    // Show the modal
                    $('#documentModal').modal('show');
                },
                error: function() {
                    toastr.error('Failed to load driver documents');
                }
            });
        });
        
        // Verify Driver
        $('#verifyDriverBtn').on('click', function() {
            const driverId = $(this).data('driver-id');
            
            $.ajax({
                url: "{{ route('admin.driver.verify', ['driver' => ':driverId']) }}".replace(':driverId', driverId),
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success('Driver verified successfully');
                        
                        // Hide the verify button in the modal
                        $('#verifyDriverBtn').hide();
                        
                        // Hide the verify button in the table and update status
                        const $row = $(`.view-docs-btn[data-driver-id="${driverId}"]`).closest('tr');
                        $row.find('.verify-driver-btn').remove();
                        $row.find('td:nth-child(5) span').removeClass().addClass('badge bg-success').text('Activated');
                        
                        // Optional: close the modal
                        // $('#documentModal').modal('hide');
                    }
                },
                error: function() {
                    toastr.error('Failed to verify driver');
                }
            });
        });
        
        // Edit User
        $('.edit-user-btn').on('click', function() {
            const $row = $(this).closest('tr');
            const userId = $row.data('user-id');
            const name = $row.find('td:nth-child(2)').text();
            const email = $row.find('td:nth-child(3)').text();
            const phone = $row.find('td:nth-child(4)').text();
            
            // Set values in the form
            $('#edit-user-id').val(userId);
            $('#edit-name').val(name);
            $('#edit-email').val(email);
            $('#edit-phone').val(phone);
            
            // Check if driver or passenger
            const isDriver = $('#drivers-tab').hasClass('active');
            $('#driver-specific-fields').toggle(isDriver);
            
            // If it's a driver, we need to get driver details
            if (isDriver) {
                const driverId = $row.find('.view-docs-btn').data('driver-id');
                
                if (driverId) {
                    $.ajax({
                        url: "{{ route('admin.driver.documents', ['driver' => ':driverId']) }}".replace(':driverId', driverId),
                        type: 'GET',
                        success: function(response) {
                            $('#edit-license-number').val(response.driver.license_number);
                            $('#edit-license-expiry').val(response.driver.license_expiry);
                        }
                    });
                }
            }
            
            $('#editUserModal').modal('show');
        });
        
        // Submit Edit Form
        $('#editUserForm').on('submit', function(e) {
            e.preventDefault();
            
            const userId = $('#edit-user-id').val();
            const formData = new FormData(this);
            formData.append('_token', "{{ csrf_token() }}");
            
            $.ajax({
                url: "{{ route('admin.user.edit', ['user' => ':userId']) }}".replace(':userId', userId),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        // Update the table row
                        const $row = $(`tr[data-user-id="${userId}"]`);
                        $row.find('td:nth-child(2)').text(formData.get('name'));
                        $row.find('td:nth-child(3)').text(formData.get('email'));
                        $row.find('td:nth-child(4)').text(formData.get('phone'));
                        
                        // Close modal
                        $('#editUserModal').modal('hide');
                        
                        // Show success message
                        toastr.success('User updated successfully');
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'Failed to update user';
                    
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMsg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    }
                    
                    toastr.error(errorMsg);
                }
            });
        });
        
        // Delete User Button
        $('.delete-user-btn').on('click', function() {
            const $row = $(this).closest('tr');
            const userId = $row.data('user-id');
            const name = $row.find('td:nth-child(2)').text();
            
            $('#delete-user-name').text(name);
            $('#confirmDeleteBtn').data('user-id', userId);
            
            $('#deleteUserModal').modal('show');
        });
        
        // Confirm Delete
        $('#confirmDeleteBtn').on('click', function() {
            const userId = $(this).data('user-id');
            
            $.ajax({
                url: "{{ route('admin.user.delete', ['user' => ':userId']) }}".replace(':userId', userId),
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        // Remove the row or update the status
                        $(`tr[data-user-id="${userId}"]`).find('td:nth-child(5) span').removeClass().addClass('badge bg-dark').text('Deleted');
                        
                        // Close modal
                        $('#deleteUserModal').modal('hide');
                        
                        // Show success message
                        toastr.success('User deleted successfully');
                        
                        // Optional: refresh page after a short delay
                        setTimeout(function() {
                            window.location.reload();
                        }, 1500);
                    }
                },
                error: function() {
                    toastr.error('Failed to delete user');
                }
            });
        });
        
        // Search functionality
        $('#driverSearch').on('keyup', function() {
            const value = $(this).val().toLowerCase();
            $('#driversTable tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
        
        $('#passengerSearch').on('keyup', function() {
            const value = $(this).val().toLowerCase();
            $('#passengersTable tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
    });
</script>
@endsection