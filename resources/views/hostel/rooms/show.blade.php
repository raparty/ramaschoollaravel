@extends('layouts.app')

@section('title', 'Room Details - School ERP')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>🚪 Room {{ $room->room_number }}</h2>
        <p class="text-muted mb-0">
            <span class="badge bg-info">{{ $room->room_type }}</span>
            @if($room->is_active)
                <span class="badge bg-success">Active</span>
            @else
                <span class="badge bg-secondary">Inactive</span>
            @endif
        </p>
    </div>
    <div class="btn-group">
        <a href="{{ route('hostel.rooms.edit', $room) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <form method="POST" 
              action="{{ route('hostel.rooms.destroy', $room) }}" 
              class="d-inline"
              onsubmit="return confirm('Are you sure you want to delete this room?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="bi bi-trash"></i> Delete
            </button>
        </form>
        <a href="{{ route('hostel.rooms.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-muted">Total Beds</h5>
                <h2 class="text-primary">{{ $stats['total_beds'] ?? 0 }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-muted">Occupied Beds</h5>
                <h2 class="text-warning">{{ $stats['occupied_beds'] ?? 0 }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-muted">Available Beds</h5>
                <h2 class="text-success">{{ $stats['available_beds'] ?? 0 }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-muted">Total Lockers</h5>
                <h2 class="text-info">{{ $stats['total_lockers'] ?? 0 }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Room Information Card -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Room Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tbody>
                        <tr>
                            <th width="180">Room Number:</th>
                            <td><strong>{{ $room->room_number }}</strong></td>
                        </tr>
                        <tr>
                            <th>Room Type:</th>
                            <td><span class="badge bg-info">{{ $room->room_type }}</span></td>
                        </tr>
                        <tr>
                            <th>Max Strength:</th>
                            <td>{{ $room->max_strength }} students</td>
                        </tr>
                        <tr>
                            <th>Current Occupancy:</th>
                            <td>
                                <span class="badge bg-warning text-dark">{{ $stats['occupied_beds'] ?? 0 }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Available Beds:</th>
                            <td>
                                <span class="badge bg-success">{{ $stats['available_beds'] ?? 0 }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Occupancy Rate:</th>
                            <td>
                                <div class="progress" style="width: 200px;">
                                    <div class="progress-bar {{ $stats['progress_bar_class'] ?? 'bg-success' }}" 
                                         role="progressbar" 
                                         style="width: {{ $stats['occupancy_rate'] ?? 0 }}%;"
                                         aria-valuenow="{{ $stats['occupancy_rate'] ?? 0 }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        {{ number_format($stats['occupancy_rate'] ?? 0, 1) }}%
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>Area:</th>
                            <td>{{ $room->area_sqft ? number_format($room->area_sqft, 2) . ' sqft' : 'Not Specified' }}</td>
                        </tr>
                        <tr>
                            <th>Attached Bathroom:</th>
                            <td>
                                @if($room->has_attached_bathroom)
                                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> Yes</span>
                                @else
                                    <span class="badge bg-secondary"><i class="bi bi-x-circle"></i> No</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                @if($room->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        @if($room->description)
                        <tr>
                            <th>Description:</th>
                            <td>{{ $room->description }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Location Information -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Location</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tbody>
                        @if($room->floor && $room->floor->block && $room->floor->block->hostel)
                            <tr>
                                <th width="180">Hostel:</th>
                                <td><strong>{{ $room->floor->block->hostel->name }}</strong></td>
                            </tr>
                            <tr>
                                <th>Block:</th>
                                <td>{{ $room->floor->block->name }}</td>
                            </tr>
                            <tr>
                                <th>Floor Number:</th>
                                <td>{{ $room->floor->floor_number }}</td>
                            </tr>
                            @if($room->floor->description)
                            <tr>
                                <th>Floor Description:</th>
                                <td>{{ $room->floor->description }}</td>
                            </tr>
                            @endif
                        @else
                            <tr>
                                <td colspan="2" class="text-muted text-center py-3">
                                    Location information not available
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Additional Statistics -->
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">Additional Statistics</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tbody>
                        <tr>
                            <th width="180">Total Lockers:</th>
                            <td><span class="badge bg-info">{{ $stats['total_lockers'] ?? 0 }}</span></td>
                        </tr>
                        <tr>
                            <th>Assigned Lockers:</th>
                            <td><span class="badge bg-warning">{{ $stats['assigned_lockers'] ?? 0 }}</span></td>
                        </tr>
                        <tr>
                            <th>Available Lockers:</th>
                            <td><span class="badge bg-success">{{ $stats['available_lockers'] ?? 0 }}</span></td>
                        </tr>
                        <tr>
                            <th>Total Furniture:</th>
                            <td><span class="badge bg-primary">{{ $stats['total_furniture'] ?? 0 }}</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Beds List -->
@if(isset($room->beds) && $room->beds->count() > 0)
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Beds ({{ $room->beds->count() }})</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th>Bed Number</th>
                        <th>Status</th>
                        <th>Condition</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($room->beds as $bed)
                        <tr>
                            <td><strong>{{ $bed->bed_number }}</strong></td>
                            <td>
                                @if($bed->is_occupied)
                                    <span class="badge bg-warning text-dark">Occupied</span>
                                @else
                                    @if($bed->is_active)
                                        <span class="badge bg-success">Available</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if($bed->condition_status === 'Good')
                                    <span class="badge bg-success">Good</span>
                                @elseif($bed->condition_status === 'Damaged')
                                    <span class="badge bg-warning text-dark">Damaged</span>
                                @elseif($bed->condition_status === 'Under Repair')
                                    <span class="badge bg-danger">Under Repair</span>
                                @else
                                    <span class="badge bg-info">{{ $bed->condition_status ?? 'N/A' }}</span>
                                @endif
                            </td>
                            <td>{{ $bed->notes ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<!-- Lockers List -->
@if(isset($room->lockers) && $room->lockers->count() > 0)
<div class="card mb-4">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">Lockers ({{ $room->lockers->count() }})</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th>Locker Number</th>
                        <th>Status</th>
                        <th>Condition</th>
                        <th>Has Key</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($room->lockers as $locker)
                        <tr>
                            <td><strong>{{ $locker->locker_number }}</strong></td>
                            <td>
                                @if($locker->is_assigned)
                                    <span class="badge bg-warning text-dark">Assigned</span>
                                @else
                                    <span class="badge bg-success">Available</span>
                                @endif
                            </td>
                            <td>
                                @if($locker->condition_status === 'Good')
                                    <span class="badge bg-success">Good</span>
                                @elseif($locker->condition_status === 'Damaged')
                                    <span class="badge bg-warning text-dark">Damaged</span>
                                @elseif($locker->condition_status === 'Under Repair')
                                    <span class="badge bg-danger">Under Repair</span>
                                @else
                                    <span class="badge bg-info">{{ $locker->condition_status ?? 'N/A' }}</span>
                                @endif
                            </td>
                            <td>
                                @if($locker->has_key ?? false)
                                    <i class="bi bi-check-circle text-success"></i>
                                @else
                                    <i class="bi bi-x-circle text-muted"></i>
                                @endif
                            </td>
                            <td>{{ $locker->notes ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<!-- Furniture List -->
@if(isset($room->furniture) && $room->furniture->count() > 0)
<div class="card mb-4">
    <div class="card-header bg-secondary text-white">
        <h5 class="mb-0">Furniture ({{ $room->furniture->count() }})</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Condition</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($room->furniture as $item)
                        <tr>
                            <td><strong>{{ $item->item_name }}</strong></td>
                            <td>{{ $item->quantity ?? 1 }}</td>
                            <td>
                                @if($item->condition_status === 'Good')
                                    <span class="badge bg-success">Good</span>
                                @elseif($item->condition_status === 'Damaged')
                                    <span class="badge bg-warning text-dark">Damaged</span>
                                @elseif($item->condition_status === 'Under Repair')
                                    <span class="badge bg-danger">Under Repair</span>
                                @else
                                    <span class="badge bg-info">{{ $item->condition_status ?? 'N/A' }}</span>
                                @endif
                            </td>
                            <td>{{ $item->notes ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<!-- Audit Information -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0">Audit Information</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p class="mb-2"><strong>Created:</strong> {{ $room->created_at->format('d M Y H:i') }}</p>
                @if($room->created_by)
                    <p class="mb-2"><strong>Created By:</strong> User ID {{ $room->created_by }}</p>
                @endif
            </div>
            <div class="col-md-6">
                <p class="mb-2"><strong>Last Updated:</strong> {{ $room->updated_at->format('d M Y H:i') }}</p>
                @if($room->updated_by)
                    <p class="mb-2"><strong>Updated By:</strong> User ID {{ $room->updated_by }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
