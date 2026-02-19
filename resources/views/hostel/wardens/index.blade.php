@extends('layouts.app')

@section('title', 'Hostel Wardens - School ERP')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>👮 Hostel Wardens</h2>
        <p class="text-muted mb-0">Manage hostel warden staff</p>
    </div>
    <a href="{{ route('hostel.wardens.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> New Warden
    </a>
</div>

<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('hostel.wardens.index') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search by name or employee code..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Gender</label>
                <select name="gender" class="form-select">
                    <option value="">All Genders</option>
                    <option value="Male" {{ request('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ request('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                    <option value="Other" {{ request('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="On Leave" {{ request('status') == 'On Leave' ? 'selected' : '' }}>On Leave</option>
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

<!-- Wardens Table -->
<div class="card">
    <div class="card-body">
        @if($wardens->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Employee Code</th>
                            <th>Name</th>
                            <th>Gender</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Date of Joining</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($wardens as $warden)
                            <tr>
                                <td><strong>{{ $warden->employee_code }}</strong></td>
                                <td>{{ $warden->name }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $warden->gender }}</span>
                                </td>
                                <td>{{ $warden->phone }}</td>
                                <td>{{ $warden->email ?? '-' }}</td>
                                <td>{{ $warden->date_of_joining ? \Carbon\Carbon::parse($warden->date_of_joining)->format('d M Y') : '-' }}</td>
                                <td>
                                    @if($warden->status === 'Active')
                                        <span class="badge bg-success">Active</span>
                                    @elseif($warden->status === 'Inactive')
                                        <span class="badge bg-secondary">Inactive</span>
                                    @elseif($warden->status === 'On Leave')
                                        <span class="badge bg-warning text-dark">On Leave</span>
                                    @else
                                        <span class="badge bg-info">{{ $warden->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('hostel.wardens.show', $warden) }}" 
                                           class="btn btn-outline-primary" 
                                           title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('hostel.wardens.edit', $warden) }}" 
                                           class="btn btn-outline-warning" 
                                           title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" 
                                              action="{{ route('hostel.wardens.destroy', $warden) }}"
                                              style="display: inline;"
                                              onsubmit="return confirm('Are you sure you want to delete this warden?');">
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
                {{ $wardens->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <div class="mb-3">
                    <i class="bi bi-person-badge-fill" style="font-size: 4rem; color: #6c757d;"></i>
                </div>
                <h5 class="text-muted">No wardens found</h5>
                <p class="text-muted">
                    @if(request()->hasAny(['search', 'gender', 'status']))
                        No wardens match your search criteria. Try adjusting your filters.
                    @else
                        Get started by adding your first warden.
                    @endif
                </p>
                <a href="{{ route('hostel.wardens.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Add First Warden
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
