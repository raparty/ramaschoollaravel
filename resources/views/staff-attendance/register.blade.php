@extends('layouts.app')

@section('title', 'Mark Staff Attendance - School ERP')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Mark Staff Attendance</h2>
        <p class="text-muted mb-0">Record staff attendance for the day</p>
    </div>
    <a href="{{ route('staff-attendance.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Dashboard
    </a>
</div>

<!-- Department and Date Selection -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('staff-attendance.register') }}" class="row g-3">
            <div class="col-md-5">
                <label class="form-label">Department</label>
                <select name="department_id" class="form-select" onchange="this.form.submit()">
                    <option value="">All Departments</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ $departmentId == $department->id ? 'selected' : '' }}>
                            {{ $department->name }} ({{ $department->staff_count }} staff)
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label">Date <span class="text-danger">*</span></label>
                <input type="date" name="date" class="form-control" value="{{ $date }}" max="{{ date('Y-m-d') }}" onchange="this.form.submit()" required>
            </div>
            <div class="col-md-2">
                <label class="form-label d-block">&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Load
                </button>
            </div>
        </form>
    </div>
</div>

@if($staffMembers->count() > 0)
    <!-- Attendance Form -->
    <form method="POST" action="{{ route('staff-attendance.store') }}" id="attendanceForm">
        @csrf
        <input type="hidden" name="department_id" value="{{ $departmentId }}">
        <input type="hidden" name="date" value="{{ $date }}">
        
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Staff List ({{ $staffMembers->count() }} staff members)</h5>
                <button type="button" class="btn btn-sm btn-success" onclick="markAllPresent()">
                    <i class="bi bi-check-all"></i> Mark All Present
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Employee ID</th>
                                <th>Staff Name</th>
                                <th>Department</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($staffMembers as $staff)
                                @php
                                    $existing = $existingAttendance[$staff->id] ?? null;
                                    $defaultStatus = $existing ? $existing->status : 'present';
                                @endphp
                                <tr>
                                    <td>{{ $staff->employee_id }}</td>
                                    <td>
                                        {{ $staff->name }}
                                        <input type="hidden" name="attendance[{{ $loop->index }}][staff_id]" value="{{ $staff->id }}">
                                    </td>
                                    <td>
                                        @if($staff->department)
                                            {{ $staff->department->name }}
                                        @else
                                            <span class="text-muted">Not Assigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group status-group" role="group">
                                            <input type="radio" class="btn-check" name="attendance[{{ $loop->index }}][status]" value="present" id="present_{{ $staff->id }}" {{ $defaultStatus == 'present' ? 'checked' : '' }} required>
                                            <label class="btn btn-outline-success btn-sm" for="present_{{ $staff->id }}">Present</label>
                                            
                                            <input type="radio" class="btn-check" name="attendance[{{ $loop->index }}][status]" value="absent" id="absent_{{ $staff->id }}" {{ $defaultStatus == 'absent' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-danger btn-sm" for="absent_{{ $staff->id }}">Absent</label>
                                            
                                            <input type="radio" class="btn-check" name="attendance[{{ $loop->index }}][status]" value="leave" id="leave_{{ $staff->id }}" {{ $defaultStatus == 'leave' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-warning btn-sm" for="leave_{{ $staff->id }}">Leave</label>
                                            
                                            <input type="radio" class="btn-check" name="attendance[{{ $loop->index }}][status]" value="half-day" id="halfday_{{ $staff->id }}" {{ $defaultStatus == 'half-day' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-info btn-sm" for="halfday_{{ $staff->id }}">Half Day</label>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Save Attendance
                </button>
                <a href="{{ route('staff-attendance.index') }}" class="btn btn-outline-secondary">
                    Cancel
                </a>
            </div>
        </div>
    </form>
@elseif(request()->has('date'))
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-inbox display-1 text-muted"></i>
            <p class="text-muted mt-3">No staff members found for the selected criteria.</p>
        </div>
    </div>
@endif

@push('scripts')
<script>
function markAllPresent() {
    document.querySelectorAll('input[type="radio"][value="present"]').forEach(radio => {
        radio.checked = true;
    });
}
</script>
@endpush
@endsection
