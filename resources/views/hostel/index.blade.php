@extends('layouts.app')

@section('title', 'Hostels - School ERP')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>🏠 Hostels</h2>
        <p class="text-muted mb-0">Manage hostel buildings and facilities</p>
    </div>
    <a href="{{ route('hostel.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> New Hostel
    </a>
</div>

<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('hostel.index') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search by hostel name..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Type</label>
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    <option value="Boys" {{ request('type') == 'Boys' ? 'selected' : '' }}>Boys</option>
                    <option value="Girls" {{ request('type') == 'Girls' ? 'selected' : '' }}>Girls</option>
                    <option value="Junior" {{ request('type') == 'Junior' ? 'selected' : '' }}>Junior</option>
                    <option value="Senior" {{ request('type') == 'Senior' ? 'selected' : '' }}>Senior</option>
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
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Search
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Hostels Table -->
<div class="card">
    <div class="card-body">
        @if($hostels->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Total Capacity</th>
                            <th>Occupied</th>
                            <th>Available</th>
                            <th>Address</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($hostels as $hostel)
                            <tr>
                                <td><strong>{{ $hostel->name }}</strong></td>
                                <td>
                                    <span class="badge bg-info">{{ $hostel->type }}</span>
                                </td>
                                <td>{{ $hostel->total_capacity }}</td>
                                <td>
                                    <span class="badge bg-warning text-dark">
                                        {{ $hostel->total_occupied ?? 0 }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-success">
                                        {{ $hostel->total_available ?? $hostel->total_capacity }}
                                    </span>
                                </td>
                                <td>{{ Str::limit($hostel->address ?? '-', 30) }}</td>
                                <td>
                                    @if($hostel->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('hostel.show', $hostel) }}" 
                                           class="btn btn-outline-primary" 
                                           title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('hostel.edit', $hostel) }}" 
                                           class="btn btn-outline-warning" 
                                           title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" 
                                              action="{{ route('hostel.destroy', $hostel) }}"
                                              style="display: inline;"
                                              onsubmit="return confirm('Are you sure you want to delete this hostel?');">
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
                {{ $hostels->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <div class="mb-3">
                    <svg width="64" height="64" fill="currentColor" style="color: #6c757d;" viewBox="0 0 24 24">
                        <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                    </svg>
                </div>
                <h5 class="text-muted">No hostels found</h5>
                <p class="text-muted">
                    @if(request()->hasAny(['search', 'type', 'is_active']))
                        No hostels match your search criteria. Try adjusting your filters.
                    @else
                        Get started by creating your first hostel.
                    @endif
                </p>
                <a href="{{ route('hostel.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Create First Hostel
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
