@extends('layouts.app')

@section('title', 'Attendance Dashboard - School ERP')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Attendance Dashboard</h2>
        <p class="text-muted mb-0">Manage student attendance</p>
    </div>
    <a href="{{ route('attendance.register') }}" class="btn btn-primary">
        <i class="bi bi-calendar-check"></i> Mark Attendance
    </a>
</div>

<!-- Today's Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h3 class="mb-0">{{ $todayStats['total'] }}</h3>
                <p class="mb-0">Total Marked Today</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h3 class="mb-0">{{ $todayStats['present'] }}</h3>
                <p class="mb-0">Present</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h3 class="mb-0">{{ $todayStats['absent'] }}</h3>
                <p class="mb-0">Absent</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <h3 class="mb-0">{{ $todayStats['late'] }}</h3>
                <p class="mb-0">Late</p>
            </div>
        </div>
    </div>
</div>

<!-- Quick Links -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Quick Actions</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="bi bi-calendar-check display-4 text-primary"></i>
                        <h6 class="mt-2">Mark Attendance</h6>
                        <a href="{{ route('attendance.register') }}" class="btn btn-sm btn-primary mt-2">Go <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="bi bi-pencil-square display-4 text-warning"></i>
                        <h6 class="mt-2">Edit Attendance</h6>
                        <a href="{{ route('attendance.edit') }}" class="btn btn-sm btn-warning mt-2">Go <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="bi bi-file-earmark-text display-4 text-info"></i>
                        <h6 class="mt-2">View Reports</h6>
                        <a href="{{ route('reports.attendance.index') }}" class="btn btn-sm btn-info mt-2">Go <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="bi bi-people display-4 text-success"></i>
                        <h6 class="mt-2">Class Attendance</h6>
                        <a href="{{ route('attendance.class') }}" class="btn btn-sm btn-success mt-2">Go <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Classes List -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Mark Attendance by Class</h5>
    </div>
    <div class="card-body">
        @if($classes->count() > 0)
            <div class="row">
                @foreach($classes as $class)
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">{{ $class->name }}</h5>
                                <p class="text-muted mb-0">{{ $class->section ?? 'All Sections' }}</p>
                                <div class="mt-3">
                                    <a href="{{ route('attendance.register', ['class_id' => $class->id, 'date' => $today]) }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-calendar-check"></i> Mark Attendance
                                    </a>
                                    <a href="{{ route('attendance.class', ['class_id' => $class->id, 'date' => $today]) }}" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-4">
                <i class="bi bi-inbox display-1 text-muted"></i>
                <p class="text-muted mt-3">No classes found. Please add classes first.</p>
            </div>
        @endif
    </div>
</div>
@endsection
