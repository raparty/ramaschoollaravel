@extends('layouts.app')

@section('title', 'Transport Vehicles')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2>🚌 Transport Vehicles</h2>
            <p class="text-muted">Manage school transport vehicles</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('transport.vehicles.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add New Vehicle
            </a>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('transport.vehicles.index') }}" class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Search by vehicle number..." value="{{ request('search') }}">
                </div>
                <div class="col-md-6">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Search
                    </button>
                    <a href="{{ route('transport.vehicles.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Vehicles Table -->
    <div class="card">
        <div class="card-body">
            @if($vehicles->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-inbox" style="font-size: 3rem; color: #6c757d;"></i>
                    <p class="mt-3 text-muted">No vehicles found. Click "Add New Vehicle" to add one.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th style="width: 80px;">S.No.</th>
                                <th>Vehicle Number</th>
                                <th>Routes Assigned</th>
                                <th style="width: 150px;" class="text-center">Seats</th>
                                <th style="width: 150px;" class="text-center">Available</th>
                                <th style="width: 200px;" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vehicles as $index => $vehicle)
                            <tr>
                                <td>{{ $vehicles->firstItem() + $index }}</td>
                                <td>
                                    <strong>{{ $vehicle->vechile_no }}</strong>
                                </td>
                                <td>
                                    @if($vehicle->route_names)
                                        <span class="text-muted small">{{ $vehicle->route_names }}</span>
                                    @else
                                        <span class="text-muted small fst-italic">No routes assigned</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info">{{ $vehicle->no_of_seats }} seats</span>
                                </td>
                                <td class="text-center">
                                    @php
                                        $available = $vehicle->available_seats;
                                        $badgeClass = $available > 10 ? 'bg-success' : ($available > 0 ? 'bg-warning' : 'bg-danger');
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $available }} available</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('transport.vehicles.edit', $vehicle) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <form action="{{ route('transport.vehicles.destroy', $vehicle) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this vehicle?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $vehicles->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
