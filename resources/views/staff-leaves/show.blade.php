@extends('layouts.app')

@section('title', 'Leave Application Details')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="bi bi-file-text"></i> Leave Application Details</h2>
                <a href="{{ route('staff-leaves.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Leave Application Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted">Staff Member</label>
                            <p><strong>{{ $staffLeave->staff->name }}</strong></p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted">Employee ID</label>
                            <p><strong>{{ $staffLeave->staff->employee_id }}</strong></p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted">Leave Type</label>
                            <p><strong>{{ $staffLeave->leaveType->name }}</strong></p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted">Status</label>
                            <p>
                                <span class="{{ $staffLeave->status_badge_class }}">
                                    {{ ucfirst($staffLeave->status) }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="text-muted">Start Date</label>
                            <p><strong>{{ $staffLeave->start_date->format('d M Y') }}</strong></p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted">End Date</label>
                            <p><strong>{{ $staffLeave->end_date->format('d M Y') }}</strong></p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted">Number of Days</label>
                            <p><span class="badge bg-info">{{ $staffLeave->days }} days</span></p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted">Reason for Leave</label>
                        <p>{{ $staffLeave->reason }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted">Applied On</label>
                        <p>{{ $staffLeave->created_at->format('d M Y, h:i A') }}</p>
                    </div>

                    @if($staffLeave->status !== 'pending')
                        <hr>
                        <h6 class="mb-3">Approval Information</h6>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="text-muted">{{ $staffLeave->status === 'approved' ? 'Approved' : 'Rejected' }} By</label>
                                <p><strong>{{ $staffLeave->approver ? $staffLeave->approver->name : 'N/A' }}</strong></p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted">{{ $staffLeave->status === 'approved' ? 'Approved' : 'Rejected' }} On</label>
                                <p>{{ $staffLeave->approved_at ? $staffLeave->approved_at->format('d M Y, h:i A') : 'N/A' }}</p>
                            </div>
                        </div>

                        @if($staffLeave->admin_remarks)
                            <div class="mb-3">
                                <label class="text-muted">Admin Remarks</label>
                                <p>{{ $staffLeave->admin_remarks }}</p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            @if($staffLeave->status === 'pending')
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="mb-0">Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="btn-group" role="group">
                            <a href="{{ route('staff-leaves.edit', $staffLeave) }}" class="btn btn-primary">
                                <i class="bi bi-pencil"></i> Edit Application
                            </a>
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                                <i class="bi bi-check-circle"></i> Approve
                            </button>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                <i class="bi bi-x-circle"></i> Reject
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('staff-leaves.approve', $staffLeave) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Approve Leave Application</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to approve this leave application?</p>
                    <div class="mb-3">
                        <label for="admin_remarks_approve" class="form-label">Remarks (Optional)</label>
                        <textarea class="form-control" id="admin_remarks_approve" 
                                  name="admin_remarks" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Approve</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('staff-leaves.reject', $staffLeave) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Reject Leave Application</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to reject this leave application?</p>
                    <div class="mb-3">
                        <label for="admin_remarks_reject" class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="admin_remarks_reject" 
                                  name="admin_remarks" rows="2" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
