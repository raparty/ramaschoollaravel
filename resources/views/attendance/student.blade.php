@extends('layouts.app')

@section('title', 'Student Attendance - School ERP')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Student Attendance</h2>
        <p class="text-muted mb-0">View individual student attendance records</p>
    </div>
    <a href="{{ route('attendance.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

<!-- Student Selection -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('attendance.student') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Student <span class="text-danger">*</span></label>
                <input type="text" name="student_id" class="form-control" placeholder="Enter student ID" value="{{ request('student_id') }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Start Date</label>
                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}" max="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">End Date</label>
                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}" max="{{ date('Y-m-d') }}">
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

@if($student)
    <!-- Student Information -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-2 text-center">
                    @if($student->photo)
                        <img src="{{ asset('storage/' . $student->photo) }}" alt="{{ $student->name }}" class="rounded-circle" width="100" height="100">
                    @else
                        <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 100px; height: 100px; font-size: 2rem;">
                            {{ strtoupper(substr($student->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <div class="col-md-10">
                    <h4>{{ $student->name }}</h4>
                    <p class="text-muted mb-1"><strong>Reg No:</strong> {{ $student->regno }}</p>
                    <p class="text-muted mb-1"><strong>Class:</strong> {{ $student->class?->name ?? 'N/A' }}</p>
                    <p class="text-muted mb-0"><strong>Period:</strong> {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ $statistics['total'] }}</h3>
                    <small>Total Days</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ $statistics['present'] }}</h3>
                    <small>Present</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ $statistics['absent'] }}</h3>
                    <small>Absent</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-warning text-dark">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ $statistics['late'] }}</h3>
                    <small>Late</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-secondary text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ $statistics['leave'] }}</h3>
                    <small>Leave</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ $statistics['percentage'] }}%</h3>
                    <small>Attendance</small>
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
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Day</th>
                                <th>Status</th>
                                <th>In Time</th>
                                <th>Out Time</th>
                                <th>Duration</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendance as $record)
                                <tr>
                                    <td>{{ $record->date->format('M d, Y') }}</td>
                                    <td>{{ $record->date->format('D') }}</td>
                                    <td><span class="badge {{ $record->status_badge }}">{{ $record->status_text }}</span></td>
                                    <td>{{ $record->in_time ? $record->in_time->format('h:i A') : '-' }}</td>
                                    <td>{{ $record->out_time ? $record->out_time->format('h:i A') : '-' }}</td>
                                    <td>{{ $record->formatted_duration ?? '-' }}</td>
                                    <td>{{ $record->remarks ?: '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="mt-3">
                    {{ $attendance->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="bi bi-inbox display-4 text-muted"></i>
                    <p class="text-muted mt-3">No attendance records found for the selected period.</p>
                </div>
            @endif
        </div>
    </div>
@else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-arrow-up display-1 text-muted"></i>
            <p class="text-muted mt-3">Please search for a student to view their attendance.</p>
        </div>
    </div>
@endif
@endsection
