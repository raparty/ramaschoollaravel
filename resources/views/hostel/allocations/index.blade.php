@extends('layouts.app')

@section('title', 'Student Allocations - School ERP')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>🛏️ Student Allocations</h2>
        <p class="text-muted mb-0">Manage student bed check-ins and check-outs</p>
    </div>
    <a href="{{ route('hostel.allocations.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> New Check-in
    </a>
</div>

<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('hostel.allocations.index') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search by student name or reg no..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Checked Out" {{ request('status') == 'Checked Out' ? 'selected' : '' }}>Checked Out</option>
                    <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
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

<!-- Allocations Table -->
<div class="card">
    <div class="card-body">
        @if($allocations->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Receipt No.</th>
                            <th>Student</th>
                            <th>Bed Location</th>
                            <th>Check-in Date</th>
                            <th>Check-out Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allocations as $allocation)
                            <tr>
                                <td><strong>{{ $allocation->receipt_number }}</strong></td>
                                <td>
                                    @if($allocation->student)
                                        <div>{{ $allocation->student->student_name }}</div>
                                        <small class="text-muted">{{ $allocation->student->reg_no }}</small>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($allocation->bed && $allocation->bed->room)
                                        <div>Room {{ $allocation->bed->room->room_number }}</div>
                                        <small class="text-muted">
                                            Bed {{ $allocation->bed->bed_number }}
                                            @if($allocation->bed->room->floor && $allocation->bed->room->floor->block)
                                                - {{ $allocation->bed->room->floor->block->block_name }}
                                            @endif
                                        </small>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $allocation->check_in_date ? \Carbon\Carbon::parse($allocation->check_in_date)->format('d M Y') : '-' }}</td>
                                <td>{{ $allocation->check_out_date ? \Carbon\Carbon::parse($allocation->check_out_date)->format('d M Y') : '-' }}</td>
                                <td>
                                    @if($allocation->status === 'Active')
                                        <span class="badge bg-success">Active</span>
                                    @elseif($allocation->status === 'Checked Out')
                                        <span class="badge bg-secondary">Checked Out</span>
                                    @elseif($allocation->status === 'Cancelled')
                                        <span class="badge bg-danger">Cancelled</span>
                                    @else
                                        <span class="badge bg-info">{{ $allocation->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('hostel.allocations.show', $allocation) }}" 
                                           class="btn btn-outline-primary" 
                                           title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if($allocation->status === 'Active')
                                            <a href="{{ route('hostel.allocations.checkout.form', $allocation) }}" 
                                               class="btn btn-outline-warning" 
                                               title="Check Out">
                                                <i class="bi bi-box-arrow-right"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $allocations->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <div class="mb-3">
                    <i class="bi bi-person-badge" style="font-size: 4rem; color: #6c757d;"></i>
                </div>
                <h5 class="text-muted">No allocations found</h5>
                <p class="text-muted">
                    @if(request()->hasAny(['search', 'status']))
                        No allocations match your search criteria. Try adjusting your filters.
                    @else
                        Get started by checking in your first student.
                    @endif
                </p>
                <a href="{{ route('hostel.allocations.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Check In First Student
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
