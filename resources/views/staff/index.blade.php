@extends('layouts.app')

@section('title', 'Staff Management - School ERP')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Staff Management</h2>
        <p class="text-muted mb-0">Manage staff and employee records</p>
    </div>
    <a href="{{ route('staff.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add New Staff
    </a>
</div>

<!-- Summary Statistics -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h3 class="mb-0">{{ $staff->total() }}</h3>
                <p class="mb-0">Total Staff</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h3 class="mb-0">{{ $activeCount }}</h3>
                <p class="mb-0">Active Staff</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h3 class="mb-0">{{ $inactiveCount }}</h3>
                <p class="mb-0">Inactive Staff</p>
            </div>
        </div>
    </div>
</div>

<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('staff.index') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Name, email or staff ID..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Department</label>
                <select name="department" class="form-select">
                    <option value="">All Departments</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ request('department') == $department->id ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Search</button>
            </div>
        </form>
    </div>
</div>

<!-- Staff Cards -->
@if($staff->count() > 0)
    <div class="row">
        @foreach($staff as $member)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start mb-3">
                            @if($member->photo)
                                <img src="{{ $member->photoUrl }}" alt="{{ $member->name }}" 
                                     class="rounded-circle me-3" 
                                     style="width: 60px; height: 60px; object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-3" 
                                     style="width: 60px; height: 60px; font-size: 24px;">
                                    {{ substr($member->name, 0, 1) }}
                                </div>
                            @endif
                            <div class="flex-grow-1">
                                <h5 class="card-title mb-1">{{ $member->name }}</h5>
                                <p class="text-muted mb-0 small">{{ $member->staff_id }}</p>
                                @if($member->status === 'active')
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="mb-2">
                            <i class="bi bi-building text-muted"></i>
                            <span class="badge bg-info">{{ $member->department->name ?? 'N/A' }}</span>
                        </div>
                        <div class="mb-2">
                            <i class="bi bi-briefcase text-muted"></i>
                            <span>{{ $member->position->title ?? 'N/A' }}</span>
                        </div>
                        <div class="mb-2">
                            <i class="bi bi-envelope text-muted"></i>
                            <span class="small">{{ $member->email }}</span>
                        </div>
                        <div class="mb-3">
                            <i class="bi bi-telephone text-muted"></i>
                            <span class="small">{{ $member->phone }}</span>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <a href="{{ route('staff.show', $member->id) }}" class="btn btn-sm btn-outline-primary flex-fill">
                                <i class="bi bi-eye"></i> View
                            </a>
                            <a href="{{ route('staff.edit', $member->id) }}" class="btn btn-sm btn-outline-secondary flex-fill">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <form action="{{ route('staff.destroy', $member->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this staff member?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $staff->links() }}
    </div>
@else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-people" style="font-size: 3rem; color: #ccc;"></i>
            <h4 class="mt-3">No Staff Found</h4>
            <p class="text-muted">
                @if(request()->hasAny(['search', 'department', 'status']))
                    No staff members match your search criteria.
                    <a href="{{ route('staff.index') }}">Clear filters</a>
                @else
                    Start by adding your first staff member.
                @endif
            </p>
            @if(!request()->hasAny(['search', 'department', 'status']))
                <a href="{{ route('staff.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Add First Staff
                </a>
            @endif
        </div>
    </div>
@endif

@if(session('success'))
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div class="toast show" role="alert">
            <div class="toast-header bg-success text-white">
                <strong class="me-auto">Success</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                {{ session('success') }}
            </div>
        </div>
    </div>
@endif

@if(session('error'))
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div class="toast show" role="alert">
            <div class="toast-header bg-danger text-white">
                <strong class="me-auto">Error</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                {{ session('error') }}
            </div>
        </div>
    </div>
@endif
@endsection
