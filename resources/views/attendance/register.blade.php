@extends('layouts.app')

@section('title', 'Mark Attendance - School ERP')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Mark Attendance</h2>
        <p class="text-muted mb-0">Record student attendance for the day</p>
    </div>
    <a href="{{ route('attendance.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Dashboard
    </a>
</div>

<!-- Class and Date Selection -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('attendance.register') }}" class="row g-3">
            <div class="col-md-5">
                <label class="form-label">Class <span class="text-danger">*</span></label>
                <select name="class_id" class="form-select" onchange="this.form.submit()" required>
                    <option value="">Select Class</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ $classId == $class->id ? 'selected' : '' }}>
                            {{ $class->name }} {{ $class->section ? '- ' . $class->section : '' }}
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

@if($classId && $students->count() > 0)
    <!-- Attendance Form -->
    <form method="POST" action="{{ route('attendance.store') }}" id="attendanceForm">
        @csrf
        <input type="hidden" name="class_id" value="{{ $classId }}">
        <input type="hidden" name="date" value="{{ $date }}">
        
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Student List ({{ $students->count() }} students)</h5>
                <button type="button" class="btn btn-sm btn-success" onclick="markAllPresent()">
                    <i class="bi bi-check-all"></i> Mark All Present
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Roll No</th>
                                <th>Student Name</th>
                                <th>Status</th>
                                <th>In Time</th>
                                <th>Out Time</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                                @php
                                    $existing = $existingAttendance->get($student->id);
                                    $defaultStatus = $existing ? $existing->status : 'present';
                                @endphp
                                <tr>
                                    <td>{{ $student->regno }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($student->photo)
                                                <img src="{{ asset('storage/' . $student->photo) }}" alt="{{ $student->name }}" class="rounded-circle me-2" width="32" height="32">
                                            @else
                                                <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                                    {{ strtoupper(substr($student->name, 0, 1)) }}
                                                </div>
                                            @endif
                                            {{ $student->name }}
                                        </div>
                                        <input type="hidden" name="attendance[{{ $loop->index }}][admission_id]" value="{{ $student->id }}">
                                    </td>
                                    <td>
                                        <div class="btn-group status-group" role="group">
                                            <input type="radio" class="btn-check" name="attendance[{{ $loop->index }}][status]" value="present" id="present_{{ $student->id }}" {{ $defaultStatus == 'present' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-success btn-sm" for="present_{{ $student->id }}">Present</label>
                                            
                                            <input type="radio" class="btn-check" name="attendance[{{ $loop->index }}][status]" value="absent" id="absent_{{ $student->id }}" {{ $defaultStatus == 'absent' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-danger btn-sm" for="absent_{{ $student->id }}">Absent</label>
                                            
                                            <input type="radio" class="btn-check" name="attendance[{{ $loop->index }}][status]" value="late" id="late_{{ $student->id }}" {{ $defaultStatus == 'late' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-warning btn-sm" for="late_{{ $student->id }}">Late</label>
                                            
                                            <input type="radio" class="btn-check" name="attendance[{{ $loop->index }}][status]" value="half_day" id="half_{{ $student->id }}" {{ $defaultStatus == 'half_day' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-info btn-sm" for="half_{{ $student->id }}">Half Day</label>
                                            
                                            <input type="radio" class="btn-check" name="attendance[{{ $loop->index }}][status]" value="leave" id="leave_{{ $student->id }}" {{ $defaultStatus == 'leave' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-secondary btn-sm" for="leave_{{ $student->id }}">Leave</label>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="time" name="attendance[{{ $loop->index }}][in_time]" class="form-control form-control-sm" style="width: 110px;" value="{{ $existing && $existing->in_time ? $existing->in_time->format('H:i') : '' }}">
                                    </td>
                                    <td>
                                        <input type="time" name="attendance[{{ $loop->index }}][out_time]" class="form-control form-control-sm" style="width: 110px;" value="{{ $existing && $existing->out_time ? $existing->out_time->format('H:i') : '' }}">
                                    </td>
                                    <td>
                                        <input type="text" name="attendance[{{ $loop->index }}][remarks]" class="form-control form-control-sm" placeholder="Optional remarks" value="{{ $existing ? $existing->remarks : '' }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Save Attendance
                </button>
                <a href="{{ route('attendance.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </div>
    </form>
@elseif($classId)
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-inbox display-1 text-muted"></i>
            <p class="text-muted mt-3">No students found in this class.</p>
        </div>
    </div>
@else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-arrow-up display-1 text-muted"></i>
            <p class="text-muted mt-3">Please select a class and date to mark attendance.</p>
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
