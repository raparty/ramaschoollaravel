@extends('layouts.app')

@section('title', 'Attendance Reports - School ERP')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Attendance Reports</h2>
        <p class="text-muted mb-0">Generate and view attendance reports</p>
    </div>
    <a href="{{ route('attendance.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

<!-- Report Generation Form -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Generate Report</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('reports.attendance.generate') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Report Type <span class="text-danger">*</span></label>
                    <select name="report_type" id="reportType" class="form-select" required>
                        <option value="">Select Report Type</option>
                        <option value="student">Student Attendance Report</option>
                        <option value="class">Class Attendance Report</option>
                        <option value="monthly">Monthly Summary Report</option>
                        <option value="daterange">Date Range Report</option>
                    </select>
                </div>
                <div class="col-md-6 d-none" id="studentField">
                    <label class="form-label">Student ID <span class="text-danger">*</span></label>
                    <input type="number" name="admission_id" class="form-control" placeholder="Enter student ID">
                </div>
                <div class="col-md-6 d-none" id="classField">
                    <label class="form-label">Class <span class="text-danger">*</span></label>
                    <select name="class_id" class="form-select">
                        <option value="">Select Class</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }} {{ $class->section ? '- ' . $class->section : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-none" id="startDateField">
                    <label class="form-label">Start Date <span class="text-danger">*</span></label>
                    <input type="date" name="start_date" class="form-control" value="{{ \Carbon\Carbon::today()->startOfMonth()->toDateString() }}" max="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-3 d-none" id="endDateField">
                    <label class="form-label">End Date <span class="text-danger">*</span></label>
                    <input type="date" name="end_date" class="form-control" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-3 d-none" id="monthField">
                    <label class="form-label">Month <span class="text-danger">*</span></label>
                    <select name="month" class="form-select">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3 d-none" id="yearField">
                    <label class="form-label">Year <span class="text-danger">*</span></label>
                    <select name="year" class="form-select">
                        @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-file-earmark-text"></i> Generate Report
                </button>
                <button type="submit" name="export" value="csv" class="btn btn-success">
                    <i class="bi bi-file-earmark-excel"></i> Export to CSV
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Quick Report Links -->
<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-person text-primary"></i> Student Attendance Report</h5>
                <p class="text-muted">View individual student's attendance records with statistics and percentage.</p>
                <ul class="small text-muted">
                    <li>Date range filter</li>
                    <li>Complete attendance history</li>
                    <li>Statistics (present, absent, late, leave)</li>
                    <li>Export to CSV</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-people text-success"></i> Class Attendance Report</h5>
                <p class="text-muted">View class-wise attendance summary for all students.</p>
                <ul class="small text-muted">
                    <li>Student-wise attendance</li>
                    <li>Class statistics</li>
                    <li>Attendance percentage per student</li>
                    <li>Export to CSV</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-calendar3 text-info"></i> Monthly Summary Report</h5>
                <p class="text-muted">View day-by-day attendance summary for a month.</p>
                <ul class="small text-muted">
                    <li>Daily attendance breakdown</li>
                    <li>Monthly totals</li>
                    <li>Average attendance percentage</li>
                    <li>Filter by class (optional)</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-calendar-range text-warning"></i> Date Range Report</h5>
                <p class="text-muted">View attendance for a custom date range.</p>
                <ul class="small text-muted">
                    <li>Custom date range</li>
                    <li>Daily statistics</li>
                    <li>Filter by class (optional)</li>
                    <li>Flexible period selection</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('reportType').addEventListener('change', function() {
    const value = this.value;
    
    // Hide all fields first
    document.getElementById('studentField').classList.add('d-none');
    document.getElementById('classField').classList.add('d-none');
    document.getElementById('startDateField').classList.add('d-none');
    document.getElementById('endDateField').classList.add('d-none');
    document.getElementById('monthField').classList.add('d-none');
    document.getElementById('yearField').classList.add('d-none');
    
    // Show relevant fields based on report type
    if (value === 'student') {
        document.getElementById('studentField').classList.remove('d-none');
        document.getElementById('startDateField').classList.remove('d-none');
        document.getElementById('endDateField').classList.remove('d-none');
    } else if (value === 'class') {
        document.getElementById('classField').classList.remove('d-none');
        document.getElementById('startDateField').classList.remove('d-none');
        document.getElementById('endDateField').classList.remove('d-none');
    } else if (value === 'monthly') {
        document.getElementById('monthField').classList.remove('d-none');
        document.getElementById('yearField').classList.remove('d-none');
        document.getElementById('classField').classList.remove('d-none');
    } else if (value === 'daterange') {
        document.getElementById('startDateField').classList.remove('d-none');
        document.getElementById('endDateField').classList.remove('d-none');
        document.getElementById('classField').classList.remove('d-none');
    }
});
</script>
@endpush
@endsection
