@extends('layouts.app')

@section('title', 'Transport Drivers')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2>👨‍✈️ Transport Drivers</h2>
            <p class="text-muted">Manage school transport drivers</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('transport.drivers.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add New Driver
            </a>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('transport.drivers.index') }}" class="row g-3">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control" placeholder="Search by name, license, or contact..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Search
                    </button>
                    <a href="{{ route('transport.drivers.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Drivers Table -->
    <div class="card">
        <div class="card-body">
            @if($drivers->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-person-x" style="font-size: 3rem; color: #6c757d;"></i>
                    <p class="mt-3 text-muted">No drivers found. Click "Add New Driver" to add one.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th style="width: 60px;">S.No.</th>
                                <th>Driver Name</th>
                                <th>License Number</th>
                                <th>Aadhar Number</th>
                                <th>Contact</th>
                                <th>Assigned Vehicles</th>
                                <th style="width: 100px;" class="text-center">Status</th>
                                <th style="width: 200px;" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($drivers as $index => $driver)
                            <tr>
                                <td>{{ $drivers->firstItem() + $index }}</td>
                                <td>
                                    <strong>{{ $driver->driver_name }}</strong>
                                </td>
                                <td>
                                    {{ $driver->license_number ?: '-' }}
                                </td>
                                <td>
                                    {{ $driver->aadhar_number ?: '-' }}
                                </td>
                                <td>
                                    {{ $driver->contact_number ?: '-' }}
                                </td>
                                <td>
                                    @if($driver->vehicles->count() > 0)
                                        <span class="badge bg-info">{{ $driver->vehicles->count() }} vehicle(s)</span>
                                        <br><small class="text-muted">{{ $driver->vehicle_numbers }}</small>
                                    @else
                                        <span class="text-muted fst-italic">No vehicle assigned</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($driver->status == 'active')
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('transport.drivers.edit', $driver) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <form action="{{ route('transport.drivers.destroy', $driver) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this driver?');">
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
                    {{ $drivers->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
