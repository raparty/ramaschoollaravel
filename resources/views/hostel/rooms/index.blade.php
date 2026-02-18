@extends('layouts.app')

@section('title', 'Hostel Rooms - School ERP')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>🚪 Hostel Rooms</h2>
        <p class="text-muted mb-0">Manage hostel room inventory</p>
    </div>
    <a href="{{ route('hostel.rooms.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> New Room
    </a>
</div>

<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('hostel.rooms.index') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search by room number..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Room Type</label>
                <select name="room_type" class="form-select">
                    <option value="">All Types</option>
                    <option value="Single" {{ request('room_type') == 'Single' ? 'selected' : '' }}>Single</option>
                    <option value="Double" {{ request('room_type') == 'Double' ? 'selected' : '' }}>Double</option>
                    <option value="Triple" {{ request('room_type') == 'Triple' ? 'selected' : '' }}>Triple</option>
                    <option value="Dormitory" {{ request('room_type') == 'Dormitory' ? 'selected' : '' }}>Dormitory</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="is_active" class="form-select">
                    <option value="">All Status</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Search
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Rooms Table -->
<div class="card">
    <div class="card-body">
        @if($rooms->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Room Number</th>
                            <th>Location</th>
                            <th>Type</th>
                            <th>Capacity</th>
                            <th>Occupied</th>
                            <th>Available</th>
                            <th>Bathroom</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rooms as $room)
                            <tr>
                                <td><strong>{{ $room->room_number }}</strong></td>
                                <td>
                                    @if($room->floor && $room->floor->block && $room->floor->block->hostel)
                                        {{ $room->floor->block->hostel->name }} - 
                                        {{ $room->floor->block->block_name }} - 
                                        Floor {{ $room->floor->floor_number }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $room->room_type }}</span>
                                </td>
                                <td>{{ $room->max_strength }}</td>
                                <td>
                                    <span class="badge bg-warning text-dark">
                                        {{ $room->current_occupancy ?? 0 }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-success">
                                        {{ $room->available_beds ?? $room->max_strength }}
                                    </span>
                                </td>
                                <td>
                                    @if($room->has_attached_bathroom)
                                        <i class="bi bi-check-circle text-success" title="Attached Bathroom"></i>
                                    @else
                                        <i class="bi bi-x-circle text-muted" title="No Attached Bathroom"></i>
                                    @endif
                                </td>
                                <td>
                                    @if($room->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('hostel.rooms.show', $room) }}" 
                                           class="btn btn-outline-primary" 
                                           title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('hostel.rooms.edit', $room) }}" 
                                           class="btn btn-outline-warning" 
                                           title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" 
                                              action="{{ route('hostel.rooms.destroy', $room) }}"
                                              style="display: inline;"
                                              onsubmit="return confirm('Are you sure you want to delete this room?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-outline-danger btn-sm" 
                                                    title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $rooms->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <div class="mb-3">
                    <i class="bi bi-door-closed" style="font-size: 4rem; color: #6c757d;"></i>
                </div>
                <h5 class="text-muted">No rooms found</h5>
                <p class="text-muted">
                    @if(request()->hasAny(['search', 'room_type', 'is_active']))
                        No rooms match your search criteria. Try adjusting your filters.
                    @else
                        Get started by creating your first room.
                    @endif
                </p>
                <a href="{{ route('hostel.rooms.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Create First Room
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
