@extends('layouts.app')

@section('title', 'Warden Details - School ERP')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>👮 {{ $warden->name }}</h2>
        <p class="text-muted mb-0">
            <span class="badge bg-secondary">{{ $warden->employee_code }}</span>
            @if($warden->status === 'Active')
                <span class="badge bg-success">Active</span>
            @elseif($warden->status === 'Inactive')
                <span class="badge bg-secondary">Inactive</span>
            @elseif($warden->status === 'On Leave')
                <span class="badge bg-warning text-dark">On Leave</span>
            @else
                <span class="badge bg-info">{{ $warden->status }}</span>
            @endif
        </p>
    </div>
    <div class="btn-group">
        <a href="{{ route('hostel.wardens.edit', $warden) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <form method="POST" 
              action="{{ route('hostel.wardens.destroy', $warden) }}" 
              class="d-inline"
              onsubmit="return confirm('Are you sure you want to delete this warden?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="bi bi-trash"></i> Delete
            </button>
        </form>
        <a href="{{ route('hostel.wardens.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="row">
    <!-- Warden Information Card -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Personal Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tbody>
                        <tr>
                            <th width="180">Name:</th>
                            <td><strong>{{ $warden->name }}</strong></td>
                        </tr>
                        <tr>
                            <th>Employee Code:</th>
                            <td><span class="badge bg-secondary">{{ $warden->employee_code }}</span></td>
                        </tr>
                        <tr>
                            <th>Gender:</th>
                            <td><span class="badge bg-info">{{ $warden->gender }}</span></td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ $warden->email ?? 'Not Provided' }}</td>
                        </tr>
                        <tr>
                            <th>Phone:</th>
                            <td>{{ $warden->phone }}</td>
                        </tr>
                        <tr>
                            <th>Address:</th>
                            <td>{{ $warden->address ?? 'Not Provided' }}</td>
                        </tr>
                        <tr>
                            <th>Date of Joining:</th>
                            <td>{{ $warden->date_of_joining ? \Carbon\Carbon::parse($warden->date_of_joining)->format('d M Y') : 'Not Set' }}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                @if($warden->status === 'Active')
                                    <span class="badge bg-success">Active</span>
                                @elseif($warden->status === 'Inactive')
                                    <span class="badge bg-secondary">Inactive</span>
                                @elseif($warden->status === 'On Leave')
                                    <span class="badge bg-warning text-dark">On Leave</span>
                                @else
                                    <span class="badge bg-info">{{ $warden->status }}</span>
                                @endif
                            </td>
                        </tr>
                        @if($warden->notes)
                        <tr>
                            <th>Notes:</th>
                            <td>{{ $warden->notes }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Hostel Assignments -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Hostel Assignments</h5>
            </div>
            <div class="card-body">
                @if($warden->hostelAssignments->count() > 0)
                    <div class="list-group">
                        @foreach($warden->hostelAssignments as $assignment)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $assignment->hostel->name }}</h6>
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($assignment->assigned_date)->format('d M Y') }}
                                            @if($assignment->end_date)
                                                - {{ \Carbon\Carbon::parse($assignment->end_date)->format('d M Y') }}
                                            @else
                                                - Present
                                            @endif
                                        </small>
                                    </div>
                                    <span class="badge bg-{{ $assignment->is_active ? 'success' : 'secondary' }}">
                                        {{ $assignment->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                @if($assignment->responsibilities)
                                    <small class="text-muted d-block mt-2">
                                        <strong>Responsibilities:</strong> {{ $assignment->responsibilities }}
                                    </small>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted text-center py-3">No hostel assignments</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Reported Incidents -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">Reported Incidents ({{ $warden->reportedIncidents->count() }})</h5>
            </div>
            <div class="card-body">
                @if($warden->reportedIncidents->count() > 0)
                    <div class="list-group">
                        @foreach($warden->reportedIncidents->take(5) as $incident)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $incident->title }}</h6>
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($incident->incident_date)->format('d M Y') }}
                                        </small>
                                    </div>
                                    <span class="badge bg-{{ $incident->severity === 'High' ? 'danger' : ($incident->severity === 'Medium' ? 'warning' : 'info') }}">
                                        {{ $incident->severity }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if($warden->reportedIncidents->count() > 5)
                        <div class="text-center mt-2">
                            <small class="text-muted">Showing 5 of {{ $warden->reportedIncidents->count() }} incidents</small>
                        </div>
                    @endif
                @else
                    <p class="text-muted text-center py-3">No incidents reported</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Submitted Attendance -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Submitted Attendance ({{ $warden->submittedAttendance->count() }})</h5>
            </div>
            <div class="card-body">
                @if($warden->submittedAttendance->count() > 0)
                    <div class="list-group">
                        @foreach($warden->submittedAttendance->take(5) as $attendance)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ \Carbon\Carbon::parse($attendance->attendance_date)->format('d M Y') }}</h6>
                                        <small class="text-muted">
                                            Submitted: {{ \Carbon\Carbon::parse($attendance->created_at)->format('d M Y H:i') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if($warden->submittedAttendance->count() > 5)
                        <div class="text-center mt-2">
                            <small class="text-muted">Showing 5 of {{ $warden->submittedAttendance->count() }} records</small>
                        </div>
                    @endif
                @else
                    <p class="text-muted text-center py-3">No attendance submitted</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Complaint Responses -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">Complaint Responses ({{ $warden->complaintResponses->count() }})</h5>
            </div>
            <div class="card-body">
                @if($warden->complaintResponses->count() > 0)
                    <div class="list-group">
                        @foreach($warden->complaintResponses->take(5) as $complaint)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $complaint->title }}</h6>
                                        <small class="text-muted">
                                            Responded: {{ \Carbon\Carbon::parse($complaint->response_date)->format('d M Y') }}
                                        </small>
                                    </div>
                                    <span class="badge bg-{{ $complaint->status === 'Resolved' ? 'success' : ($complaint->status === 'In Progress' ? 'warning' : 'secondary') }}">
                                        {{ $complaint->status }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if($warden->complaintResponses->count() > 5)
                        <div class="text-center mt-2">
                            <small class="text-muted">Showing 5 of {{ $warden->complaintResponses->count() }} complaints</small>
                        </div>
                    @endif
                @else
                    <p class="text-muted text-center py-3">No complaint responses</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Submitted Expenses -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">Submitted Expenses ({{ $warden->submittedExpenses->count() }})</h5>
            </div>
            <div class="card-body">
                @if($warden->submittedExpenses->count() > 0)
                    <div class="list-group">
                        @foreach($warden->submittedExpenses->take(5) as $expense)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $expense->description }}</h6>
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}
                                        </small>
                                    </div>
                                    <span class="badge bg-info">
                                        ₹{{ number_format($expense->amount, 2) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if($warden->submittedExpenses->count() > 5)
                        <div class="text-center mt-2">
                            <small class="text-muted">Showing 5 of {{ $warden->submittedExpenses->count() }} expenses</small>
                        </div>
                    @endif
                @else
                    <p class="text-muted text-center py-3">No expenses submitted</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Audit Information -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0">Audit Information</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p class="mb-2"><strong>Created:</strong> {{ \Carbon\Carbon::parse($warden->created_at)->format('d M Y H:i') }}</p>
                @if($warden->created_by)
                    <p class="mb-2"><strong>Created By:</strong> User ID {{ $warden->created_by }}</p>
                @endif
            </div>
            <div class="col-md-6">
                <p class="mb-2"><strong>Last Updated:</strong> {{ \Carbon\Carbon::parse($warden->updated_at)->format('d M Y H:i') }}</p>
                @if($warden->updated_by)
                    <p class="mb-2"><strong>Updated By:</strong> User ID {{ $warden->updated_by }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
