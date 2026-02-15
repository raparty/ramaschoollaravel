@extends('layouts.app')

@section('title', 'Staff Member Attendance - School ERP')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Individual Staff Attendance</h2>
        <p class="text-muted mb-0">View attendance history for individual staff members</p>
    </div>
    <a href="{{ route('staff-attendance.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Dashboard
    </a>
</div>

<!-- Staff and Date Selection -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('staff-attendance.staff') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Staff Member <span class="text-danger">*</span></label>
                <select name="staff_id" class="form-select" required>
                    <option value="">Select Staff Member</option>
                    @foreach($staffList as $staffMember)
                        <option value="{{ $staffMember->id }}" {{ request('staff_id') == $staffMember->id ? 'selected' : '' }}>
                            {{ $staffMember->first_name }} {{ $staffMember->last_name }} ({{ $staffMember->employee_id }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Start Date <span class="text-danger">*</span></label>
                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">End Date <span class="text-danger">*</span></label>
                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}" max="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-md-2">
                <label class="form-label d-block">&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Search
                </button>
            </div>
        </form>
    </div>
</div>

@if($staff)
    <!-- Staff Info Card -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h5>{{ $staff->first_name }} {{ $staff->last_name }}</h5>
                    <p class="text-muted mb-1">Employee ID: {{ $staff->employee_id }}</p>
                    @if($staff->department)
                        <p class="text-muted mb-1">Department: {{ $staff->department->name }}</p>
                    @endif
                    @if($staff->position)
                        <p class="text-muted mb-0">Position: {{ $staff->position->title }}</p>
                    @endif
                </div>
                <div class="col-md-4 text-end">
                    <div class="badge bg-primary fs-6">
                        Attendance: {{ $statistics['percentage'] ?? 0 }}%
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h3 class="mb-0">{{ $statistics['total'] ?? 0 }}</h3>
                    <p class="mb-0">Total Days</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h3 class="mb-0">{{ $statistics['present'] ?? 0 }}</h3>
                    <p class="mb-0">Present</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h3 class="mb-0">{{ $statistics['absent'] ?? 0 }}</h3>
                    <p class="mb-0">Absent</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h3 class="mb-0">{{ $statistics['leave'] ?? 0 }}</h3>
                    <p class="mb-0">On Leave</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Records -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Attendance Records</h5>
        </div>
        <div class="card-body">
            @if($attendance->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Day</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendance as $record)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($record->att_date)->format('d M Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($record->att_date)->format('l') }}</td>
                                    <td>
                                        <span class="{{ $record->status_badge_class }}">
                                            {{ ucfirst($record->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $attendance->appends(request()->except('page'))->links() }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="bi bi-calendar-x display-4 text-muted"></i>
                    <p class="text-muted mt-3">No attendance records found for the selected date range.</p>
                </div>
            @endif
        </div>
    </div>
@endif
@endsection
