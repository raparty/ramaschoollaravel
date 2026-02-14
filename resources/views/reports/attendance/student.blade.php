@extends('layouts.app')

@section('title', 'Student Attendance Report - School ERP')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Student Attendance Report</h2>
        <p class="text-muted mb-0">Detailed attendance report for individual student</p>
    </div>
    <div>
        <a href="{{ route('reports.attendance.student', array_merge(request()->query(), ['export' => 'csv'])) }}" class="btn btn-success me-2">
            <i class="bi bi-file-earmark-excel"></i> Export CSV
        </a>
        <button onclick="window.print()" class="btn btn-info me-2">
            <i class="bi bi-printer"></i> Print
        </a>
        <a href="{{ route('reports.attendance.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

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
                <p class="text-muted mb-1"><strong>Class:</strong> {{ $student->class->name ?? 'N/A' }}</p>
                <p class="text-muted mb-0"><strong>Report Period:</strong> {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</p>
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
        <h5 class="mb-0">Attendance Records ({{ $attendance->count() }} days)</h5>
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
        @else
            <div class="text-center py-4">
                <i class="bi bi-inbox display-4 text-muted"></i>
                <p class="text-muted mt-3">No attendance records found for the selected period.</p>
            </div>
        @endif
    </div>
</div>

@push('styles')
<style>
@media print {
    .btn, .breadcrumb, nav { display: none !important; }
    .card { border: none !important; box-shadow: none !important; }
}
</style>
@endpush
@endsection
