@extends('layouts.app')

@section('title', 'Class Attendance - School ERP')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Class Attendance</h2>
        <p class="text-muted mb-0">View class-wise attendance for a date</p>
    </div>
    <a href="{{ route('attendance.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

<!-- Class and Date Selection -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('attendance.class') }}" class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Class <span class="text-danger">*</span></label>
                <select name="class_id" class="form-select" required>
                    <option value="">Select Class</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->name }} {{ $c->section ? '- ' . $c->section : '' }}
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
                    <i class="bi bi-search"></i> Load
                </button>
            </div>
        </form>
    </div>
</div>

@if($class)
    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ $statistics['total'] }}</h3>
                    <p class="mb-0">Total Students</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ $statistics['present'] }}</h3>
                    <p class="mb-0">Present</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ $statistics['absent'] }}</h3>
                    <p class="mb-0">Absent</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ $statistics['percentage'] }}%</h3>
                    <p class="mb-0">Attendance</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Class Information -->
    <div class="card mb-4">
        <div class="card-body">
            <h5>{{ $class->name }} {{ $class->section ? '- ' . $class->section : '' }}</h5>
            <p class="text-muted mb-0">Date: {{ \Carbon\Carbon::parse($date)->format('l, F d, Y') }}</p>
        </div>
    </div>

    <!-- Attendance List -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Student Attendance</h5>
            <a href="{{ route('attendance.register', ['class_id' => $class->id, 'date' => $date]) }}" class="btn btn-sm btn-primary">
                <i class="bi bi-pencil"></i> Edit Attendance
            </a>
        </div>
        <div class="card-body">
            @if($attendance->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Roll No</th>
                                <th>Student Name</th>
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
                                    <td>{{ $record->student->regno }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($record->student->photo)
                                                <img src="{{ asset('storage/' . $record->student->photo) }}" alt="{{ $record->student->name }}" class="rounded-circle me-2" width="32" height="32">
                                            @else
                                                <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                                    {{ strtoupper(substr($record->student->name, 0, 1)) }}
                                                </div>
                                            @endif
                                            {{ $record->student->name }}
                                        </div>
                                    </td>
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
            @else
                <div class="text-center py-4">
                    <i class="bi bi-inbox display-4 text-muted"></i>
                    <p class="text-muted mt-3">No attendance marked for this class on the selected date.</p>
                    <a href="{{ route('attendance.register', ['class_id' => $class->id, 'date' => $date]) }}" class="btn btn-primary mt-2">
                        <i class="bi bi-calendar-check"></i> Mark Attendance
                    </a>
                </div>
            @endif
        </div>
    </div>
@else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-arrow-up display-1 text-muted"></i>
            <p class="text-muted mt-3">Please select a class and date to view attendance.</p>
        </div>
    </div>
@endif
@endsection
