@extends('layouts.app')

@section('title', 'Monthly Attendance Report - School ERP')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Monthly Attendance Report</h2>
        <p class="text-muted mb-0">Daily attendance summary for the month</p>
    </div>
    <div>
        <button onclick="window.print()" class="btn btn-info me-2">
            <i class="bi bi-printer"></i> Print
        </a>
        <a href="{{ route('reports.attendance.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

<!-- Month Information -->
<div class="card mb-4">
    <div class="card-body">
        <h4>{{ date('F Y', mktime(0, 0, 0, $month, 1, $year)) }}</h4>
        @if($class)
            <p class="text-muted mb-0"><strong>Class:</strong> {{ $class->name }} {{ $class->section ? '- ' . $class->section : '' }}</p>
        @else
            <p class="text-muted mb-0"><strong>Scope:</strong> All Classes</p>
        @endif
    </div>
</div>

<!-- Monthly Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h3 class="mb-0">{{ $monthlyStats['total_records'] }}</h3>
                <p class="mb-0">Total Records</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h3 class="mb-0">{{ $monthlyStats['total_present'] }}</h3>
                <p class="mb-0">Total Present</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body text-center">
                <h3 class="mb-0">{{ $monthlyStats['total_absent'] }}</h3>
                <p class="mb-0">Total Absent</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h3 class="mb-0">{{ $monthlyStats['avg_attendance'] }}%</h3>
                <p class="mb-0">Average Attendance</p>
            </div>
        </div>
    </div>
</div>

<!-- Daily Attendance Breakdown -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Daily Attendance Breakdown</h5>
    </div>
    <div class="card-body">
        @if(count($dailyStats) > 0)
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Day</th>
                            <th class="text-center">Total</th>
                            <th class="text-center">Present</th>
                            <th class="text-center">Absent</th>
                            <th class="text-center">Attendance %</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dailyStats as $stat)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($stat['date'])->format('M d, Y') }}</td>
                                <td><span class="badge bg-secondary">{{ $stat['day'] }}</span></td>
                                <td class="text-center">{{ $stat['total'] }}</td>
                                <td class="text-center"><span class="badge bg-success">{{ $stat['present'] }}</span></td>
                                <td class="text-center"><span class="badge bg-danger">{{ $stat['absent'] }}</span></td>
                                <td class="text-center"><strong>{{ $stat['percentage'] }}%</strong></td>
                                <td class="text-center">
                                    @if($stat['percentage'] >= 75)
                                        <span class="badge bg-success">Excellent</span>
                                    @elseif($stat['percentage'] >= 50)
                                        <span class="badge bg-warning">Average</span>
                                    @elseif($stat['total'] > 0)
                                        <span class="badge bg-danger">Poor</span>
                                    @else
                                        <span class="badge bg-secondary">No Data</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-secondary">
                            <td colspan="2"><strong>Monthly Total</strong></td>
                            <td class="text-center"><strong>{{ $monthlyStats['total_records'] }}</strong></td>
                            <td class="text-center"><strong>{{ $monthlyStats['total_present'] }}</strong></td>
                            <td class="text-center"><strong>{{ $monthlyStats['total_absent'] }}</strong></td>
                            <td class="text-center"><strong>{{ $monthlyStats['avg_attendance'] }}%</strong></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="text-center py-4">
                <i class="bi bi-inbox display-4 text-muted"></i>
                <p class="text-muted mt-3">No attendance data available for this month.</p>
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
