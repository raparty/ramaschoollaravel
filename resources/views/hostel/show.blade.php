@extends('layouts.app')

@section('title', 'Hostel Details - School ERP')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>🏠 {{ $hostel->name }}</h2>
        <p class="text-muted mb-0">
            <span class="badge bg-info">{{ $hostel->type }}</span>
            @if($hostel->is_active)
                <span class="badge bg-success">Active</span>
            @else
                <span class="badge bg-secondary">Inactive</span>
            @endif
        </p>
    </div>
    <div class="btn-group">
        <a href="{{ route('hostel.edit', $hostel) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <form method="POST" 
              action="{{ route('hostel.destroy', $hostel) }}" 
              class="d-inline"
              onsubmit="return confirm('Are you sure you want to delete this hostel?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="bi bi-trash"></i> Delete
            </button>
        </form>
    </div>
</div>

<div class="row">
    <!-- Hostel Information Card -->
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Hostel Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tbody>
                        <tr>
                            <th width="180">Name:</th>
                            <td><strong>{{ $hostel->name }}</strong></td>
                        </tr>
                        <tr>
                            <th>Type:</th>
                            <td><span class="badge bg-info">{{ $hostel->type }}</span></td>
                        </tr>
                        <tr>
                            <th>Total Capacity:</th>
                            <td>{{ $hostel->total_capacity }} beds</td>
                        </tr>
                        <tr>
                            <th>Occupied Beds:</th>
                            <td>
                                <span class="badge bg-warning text-dark">{{ $stats['occupied_beds'] ?? 0 }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Available Beds:</th>
                            <td>
                                <span class="badge bg-success">{{ $stats['available_beds'] ?? $hostel->total_capacity }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Occupancy Rate:</th>
                            <td>
                                <div class="progress" style="width: 200px;">
                                    <div class="progress-bar" role="progressbar" 
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
                            <th>Address:</th>
                            <td>{{ $hostel->address ?? 'Not Provided' }}</td>
                        </tr>
                        <tr>
                            <th>Description:</th>
                            <td>{{ $hostel->description ?? 'No description provided' }}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                @if($hostel->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-muted">Total Blocks</h5>
                <h2 class="text-primary">{{ $stats['total_blocks'] ?? 0 }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-muted">Total Rooms</h5>
                <h2 class="text-info">{{ $stats['total_rooms'] ?? 0 }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-muted">Total Beds</h5>
                <h2 class="text-success">{{ $stats['total_beds'] ?? $hostel->total_capacity }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-muted">Occupied</h5>
                <h2 class="text-warning">{{ $stats['occupied_beds'] ?? 0 }}</h2>
            </div>
        </div>
    </div>
</div>

<!-- Blocks, Wardens, and Other Related Information -->
@if(isset($hostel->blocks) && $hostel->blocks->count() > 0)
<div class="card mb-4">
    <div class="card-header bg-secondary text-white">
        <h5 class="mb-0">Blocks & Rooms</h5>
    </div>
    <div class="card-body">
        @foreach($hostel->blocks as $block)
            <div class="mb-3">
                <h6>{{ $block->name }}</h6>
                <p class="text-muted mb-2">
                    Floors: {{ $block->floors->count() }} | 
                    Rooms: {{ $block->floors->sum(function($floor) { return $floor->rooms->count(); }) }}
                </p>
            </div>
        @endforeach
    </div>
</div>
@endif

@if(isset($hostel->wardenAssignments) && $hostel->wardenAssignments->count() > 0)
<div class="card mb-4">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">Assigned Wardens</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Warden Name</th>
                        <th>Contact</th>
                        <th>Assignment Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($hostel->wardenAssignments as $assignment)
                        <tr>
                            <td>{{ $assignment->warden->name ?? 'N/A' }}</td>
                            <td>{{ $assignment->warden->phone ?? 'N/A' }}</td>
                            <td>{{ $assignment->assignment_date?->format('d M Y') ?? 'N/A' }}</td>
                            <td>
                                @if($assignment->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<div class="mb-3">
    <a href="{{ route('hostel.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Back to Hostels
    </a>
</div>
@endsection
