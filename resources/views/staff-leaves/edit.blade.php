@extends('layouts.app')

@section('title', 'Edit Leave Application')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col-md-12">
            <h2><i class="bi bi-pencil"></i> Edit Leave Application</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('staff-leaves.update', $staffLeave) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="staff_id" class="form-label">Staff Member <span class="text-danger">*</span></label>
                            <select class="form-select @error('staff_id') is-invalid @enderror" 
                                    id="staff_id" name="staff_id" required>
                                <option value="">Select Staff Member</option>
                                @foreach($staff as $s)
                                    <option value="{{ $s->id }}" {{ old('staff_id', $staffLeave->staff_id) == $s->id ? 'selected' : '' }}>
                                        {{ $s->name }} ({{ $s->employee_id }})
                                    </option>
                                @endforeach
                            </select>
                            @error('staff_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="leave_type_id" class="form-label">Leave Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('leave_type_id') is-invalid @enderror" 
                                    id="leave_type_id" name="leave_type_id" required>
                                <option value="">Select Leave Type</option>
                                @foreach($leaveTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('leave_type_id', $staffLeave->leave_type_id) == $type->id ? 'selected' : '' }}
                                            data-max-days="{{ $type->max_days }}" data-requires-approval="{{ $type->requires_approval }}">
                                        {{ $type->name }}
                                        @if($type->max_days)
                                            (Max: {{ $type->max_days }} days/year)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('leave_type_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted" id="leave-type-info"></small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                           id="start_date" name="start_date" value="{{ old('start_date', $staffLeave->start_date->format('Y-m-d')) }}" 
                                           min="{{ date('Y-m-d') }}" required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                           id="end_date" name="end_date" value="{{ old('end_date', $staffLeave->end_date->format('Y-m-d')) }}" 
                                           min="{{ date('Y-m-d') }}" required>
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Number of Days: <span id="days-count" class="badge bg-info">{{ $staffLeave->days }}</span></label>
                        </div>

                        <div class="mb-3">
                            <label for="reason" class="form-label">Reason for Leave <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('reason') is-invalid @enderror" 
                                      id="reason" name="reason" rows="4" required 
                                      placeholder="Please provide a brief reason for your leave application">{{ old('reason', $staffLeave->reason) }}</textarea>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> <strong>Note:</strong> Your leave application will be sent for admin approval.
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('staff-leaves.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Update Application
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const daysCount = document.getElementById('days-count');
    const leaveTypeSelect = document.getElementById('leave_type_id');
    const leaveTypeInfo = document.getElementById('leave-type-info');

    function calculateDays() {
        if (startDateInput.value && endDateInput.value) {
            const start = new Date(startDateInput.value);
            const end = new Date(endDateInput.value);
            const diffTime = end - start;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
            
            if (diffDays > 0) {
                daysCount.textContent = diffDays;
            } else {
                daysCount.textContent = '0';
            }
        }
    }

    function updateLeaveTypeInfo() {
        const selectedOption = leaveTypeSelect.options[leaveTypeSelect.selectedIndex];
        if (selectedOption.value) {
            const maxDays = selectedOption.dataset.maxDays;
            const requiresApproval = selectedOption.dataset.requiresApproval === '1';
            
            let info = '';
            if (maxDays) {
                info += `Maximum ${maxDays} days per year. `;
            }
            info += requiresApproval ? 'Requires admin approval.' : 'Auto-approved.';
            
            leaveTypeInfo.textContent = info;
        } else {
            leaveTypeInfo.textContent = '';
        }
    }

    startDateInput.addEventListener('change', function() {
        endDateInput.min = this.value;
        calculateDays();
    });

    endDateInput.addEventListener('change', calculateDays);
    leaveTypeSelect.addEventListener('change', updateLeaveTypeInfo);

    // Initial calculation
    calculateDays();
    updateLeaveTypeInfo();
});
</script>
@endpush
@endsection
