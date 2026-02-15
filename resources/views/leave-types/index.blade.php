@extends('layouts.app')

@section('title', 'Leave Types')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="bi bi-calendar-check"></i> Leave Types</h2>
                <a href="{{ route('leave-types.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Add Leave Type
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            @if($leaveTypes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Max Days</th>
                                <th>Requires Approval</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leaveTypes as $leaveType)
                                <tr>
                                    <td><strong>{{ $leaveType->name }}</strong></td>
                                    <td>{{ Str::limit($leaveType->description ?? 'N/A', 50) }}</td>
                                    <td>
                                        @if($leaveType->max_days)
                                            <span class="badge bg-secondary">{{ $leaveType->max_days }} days/year</span>
                                        @else
                                            <span class="badge bg-info">Unlimited</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($leaveType->requires_approval)
                                            <span class="badge bg-warning">Yes</span>
                                        @else
                                            <span class="badge bg-success">No</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($leaveType->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('leave-types.edit', $leaveType) }}" class="btn btn-outline-primary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('leave-types.toggle-status', $leaveType) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-secondary" title="Toggle Status">
                                                    <i class="bi bi-toggle-{{ $leaveType->is_active ? 'on' : 'off' }}"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('leave-types.destroy', $leaveType) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this leave type?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Delete">
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
                
                <div class="d-flex justify-content-center mt-3">
                    {{ $leaveTypes->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-1 text-muted"></i>
                    <p class="text-muted mt-3">No leave types found</p>
                    <a href="{{ route('leave-types.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Add First Leave Type
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
