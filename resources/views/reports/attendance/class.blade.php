@extends('layouts.app')

@section('title', 'Class Attendance Report - School ERP')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Class Attendance Report</h2>
        <p class="text-muted mb-0">Student-wise attendance for class</p>
    </div>
    <div>
        <a href="{{ route('reports.attendance.class', array_merge(request()->query(), ['export' => 'csv'])) }}" class="btn btn-success me-2">
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

<!-- Class Information -->
<div class="card mb-4">
    <div class="card-body">
        <h4>{{ $class->name }} {{ $class->section ? '- ' . $class->section : '' }}</h4>
        <p class="text-muted mb-0"><strong>Report Period:</strong> {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</p>
    </div>
</div>

<!-- Class Statistics -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h3 class="mb-0">{{ $classStats['total_students'] }}</h3>
                <p class="mb-0">Total Students</p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h3 class="mb-0">{{ $classStats['avg_attendance'] }}%</h3>
                <p class="mb-0">Average Attendance</p>
            </div>
        </div>
    </div>
</div>

<!-- Student Attendance Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Student Attendance Summary</h5>
    </div>
    <div class="card-body">
        @if(count($studentAttendance) > 0)
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Reg No</th>
                            <th>Student Name</th>
                            <th class="text-center">Total Days</th>
                            <th class="text-center">Present</th>
                            <th class="text-center">Absent</th>
                            <th class="text-center">Attendance %</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($studentAttendance as $index => $data)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $data['student']->regno }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($data['student']->photo)
                                            <img src="{{ asset('storage/' . $data['student']->photo) }}" alt="{{ $data['student']->name }}" class="rounded-circle me-2" width="32" height="32">
                                        @else
                                            <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                                {{ strtoupper(substr($data['student']->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        {{ $data['student']->name }}
                                    </div>
                                </td>
                                <td class="text-center">{{ $data['total'] }}</td>
                                <td class="text-center"><span class="badge bg-success">{{ $data['present'] }}</span></td>
                                <td class="text-center"><span class="badge bg-danger">{{ $data['absent'] }}</span></td>
                                <td class="text-center"><strong>{{ $data['percentage'] }}%</strong></td>
                                <td class="text-center">
                                    @if($data['percentage'] >= 75)
                                        <span class="badge bg-success">Good</span>
                                    @elseif($data['percentage'] >= 50)
                                        <span class="badge bg-warning">Average</span>
                                    @else
                                        <span class="badge bg-danger">Poor</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-secondary">
                            <td colspan="3"><strong>Class Average</strong></td>
                            <td class="text-center"><strong>{{ collect($studentAttendance)->avg('total') }}</strong></td>
                            <td class="text-center"><strong>{{ collect($studentAttendance)->sum('present') }}</strong></td>
                            <td class="text-center"><strong>{{ collect($studentAttendance)->sum('absent') }}</strong></td>
                            <td class="text-center"><strong>{{ $classStats['avg_attendance'] }}%</strong></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="text-center py-4">
                <i class="bi bi-inbox display-4 text-muted"></i>
                <p class="text-muted mt-3">No students found in this class.</p>
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
