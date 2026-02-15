@extends('layouts.app')

@section('title', 'Department Attendance - School ERP')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Department Attendance</h2>
        <p class="text-muted mb-0">View attendance for a specific department</p>
    </div>
    <a href="{{ route('staff-attendance.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Dashboard
    </a>
</div>

<!-- Department and Date Selection -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('staff-attendance.department') }}" class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Department <span class="text-danger">*</span></label>
                <select name="department_id" class="form-select" required>
                    <option value="">Select Department</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }} ({{ $dept->staff_count }} staff)
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Date <span class="text-danger">*</span></label>
                <input type="date" name="date" class="form-control" value="{{ $date }}" max="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-md-2">
                <label class="form-label d-block">&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> View
                </button>
            </div>
        </form>
    </div>
</div>

@if($department)
    <!-- Department Info -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h5>{{ $department->name }}</h5>
                    <p class="text-muted mb-0">Date: {{ \Carbon\Carbon::parse($date)->format('d M Y') }} ({{ \Carbon\Carbon::parse($date)->format('l') }})</p>
                </div>
                <div class="col-md-4 text-end">
                    @if(isset($statistics['percentage']))
                        <div class="badge bg-primary fs-6">
                            Attendance: {{ $statistics['percentage'] }}%
                        </div>
                    @endif
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
                    <p class="mb-0">Total Staff</p>
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
                    <h3 class="mb-0">{{ $statistics['on_leave'] ?? 0 }}</h3>
                    <p class="mb-0">On Leave</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance List -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Attendance Details</h5>
            <a href="{{ route('staff-attendance.edit', ['department_id' => $department->id, 'date' => $date]) }}" class="btn btn-sm btn-warning">
                <i class="bi bi-pencil"></i> Edit Attendance
            </a>
        </div>
        <div class="card-body">
            @if($attendance->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Employee ID</th>
                                <th>Staff Name</th>
                                <th>Position</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendance as $record)
                                <tr>
                                    <td>{{ $record->staff->employee_id }}</td>
                                    <td>{{ $record->staff->name }}</td>
                                    <td>
                                        @if($record->staff->position)
                                            {{ $record->staff->position->title }}
                                        @else
                                            <span class="text-muted">Not Assigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="{{ $record->attendance->status_badge_class }}">
                                            {{ ucfirst($record->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="bi bi-calendar-x display-4 text-muted"></i>
                    <p class="text-muted mt-3">No attendance records found for this department on the selected date.</p>
                    <a href="{{ route('staff-attendance.register', ['department_id' => $department->id, 'date' => $date]) }}" class="btn btn-primary mt-2">
                        <i class="bi bi-calendar-check"></i> Mark Attendance
                    </a>
                </div>
            @endif
        </div>
    </div>
@endif
@endsection
