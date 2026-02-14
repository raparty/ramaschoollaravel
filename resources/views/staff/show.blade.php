@extends('layouts.app')

@section('title', $staff->name . ' - Staff Profile - School ERP')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>{{ $staff->name }}</h2>
        <p class="text-muted mb-0">Staff Profile</p>
    </div>
    <div>
        <a href="{{ route('staff.edit', $staff->id) }}" class="btn btn-primary me-2">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="{{ route('staff.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="row">
    <!-- Left Column -->
    <div class="col-lg-8">
        <!-- Personal Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Personal Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <strong>Staff ID:</strong><br>
                        <span>{{ $staff->staff_id }}</span>
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>Name:</strong><br>
                        <span>{{ $staff->name }}</span>
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>Status:</strong><br>
                        @if($staff->status === 'active')
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Inactive</span>
                        @endif
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>Email:</strong><br>
                        <span>{{ $staff->email }}</span>
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>Phone:</strong><br>
                        <span>{{ $staff->phone }}</span>
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>Date of Birth:</strong><br>
                        <span>{{ $staff->date_of_birth ? $staff->date_of_birth->format('d M, Y') : 'N/A' }}</span>
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>Gender:</strong><br>
                        <span>{{ $staff->gender ?? 'N/A' }}</span>
                    </div>
                    <div class="col-md-8 mb-3">
                        <strong>Address:</strong><br>
                        <span>{{ $staff->address ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Professional Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Professional Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <strong>Department:</strong><br>
                        <span class="badge bg-info">{{ $staff->department->name ?? 'N/A' }}</span>
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>Position:</strong><br>
                        <span>{{ $staff->position->title ?? 'N/A' }}</span>
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>Joining Date:</strong><br>
                        <span>{{ $staff->joining_date ? $staff->joining_date->format('d M, Y') : 'N/A' }}</span>
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>Years of Service:</strong><br>
                        <span>{{ $staff->yearsOfService }} years</span>
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>Basic Salary:</strong><br>
                        <span>₹{{ number_format($staff->salary, 2) }}</span>
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>Qualification:</strong><br>
                        <span>{{ $staff->qualification ?? 'N/A' }}</span>
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>Experience:</strong><br>
                        <span>{{ $staff->experience ? $staff->experience . ' years' : 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Salary History -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Salary History</h5>
                <a href="{{ route('salaries.history', $staff->id) }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                @if($recentSalaries->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Month/Year</th>
                                    <th>Basic</th>
                                    <th>Allowances</th>
                                    <th>Deductions</th>
                                    <th>Net Salary</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentSalaries as $salary)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::create($salary->year, $salary->month)->format('M Y') }}</td>
                                        <td>₹{{ number_format($salary->basic_salary, 2) }}</td>
                                        <td>₹{{ number_format($salary->allowances, 2) }}</td>
                                        <td>₹{{ number_format($salary->deductions, 2) }}</td>
                                        <td><strong>₹{{ number_format($salary->net_salary, 2) }}</strong></td>
                                        <td>
                                            @if($salary->payment_status === 'paid')
                                                <span class="badge bg-success">Paid</span>
                                            @else
                                                <span class="badge bg-warning">Pending</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center py-3">No salary records yet</p>
                @endif
            </div>
        </div>

        <!-- Attendance Statistics -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Attendance Statistics (This Month)</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <h3 class="text-success">{{ $attendanceStats['present'] }}</h3>
                        <p class="text-muted">Present</p>
                    </div>
                    <div class="col-md-3">
                        <h3 class="text-danger">{{ $attendanceStats['absent'] }}</h3>
                        <p class="text-muted">Absent</p>
                    </div>
                    <div class="col-md-3">
                        <h3 class="text-warning">{{ $attendanceStats['leave'] }}</h3>
                        <p class="text-muted">On Leave</p>
                    </div>
                    <div class="col-md-3">
                        <h3 class="text-info">{{ $attendanceStats['halfDay'] }}</h3>
                        <p class="text-muted">Half Day</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column -->
    <div class="col-lg-4">
        <!-- Photo -->
        <div class="card mb-4">
            <div class="card-body text-center">
                @if($staff->photo)
                    <img src="{{ $staff->photoUrl }}" alt="{{ $staff->name }}" 
                         class="rounded-circle mb-3" 
                         style="width: 200px; height: 200px; object-fit: cover;">
                @else
                    <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center mx-auto mb-3" 
                         style="width: 200px; height: 200px; font-size: 80px;">
                        {{ substr($staff->name, 0, 1) }}
                    </div>
                @endif
                <h4>{{ $staff->name }}</h4>
                <p class="text-muted">{{ $staff->position->title ?? 'N/A' }}</p>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <a href="{{ route('salaries.process') }}?staff_id={{ $staff->id }}" class="btn btn-outline-primary w-100 mb-2">
                    <i class="bi bi-cash"></i> Process Salary
                </a>
                <a href="{{ route('salaries.history', $staff->id) }}" class="btn btn-outline-info w-100 mb-2">
                    <i class="bi bi-clock-history"></i> Salary History
                </a>
                <a href="{{ route('staff.edit', $staff->id) }}" class="btn btn-outline-secondary w-100 mb-2">
                    <i class="bi bi-pencil"></i> Edit Profile
                </a>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div class="toast show" role="alert">
            <div class="toast-header bg-success text-white">
                <strong class="me-auto">Success</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                {{ session('success') }}
            </div>
        </div>
    </div>
@endif
@endsection
