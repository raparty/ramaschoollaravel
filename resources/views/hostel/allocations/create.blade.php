@extends('layouts.app')

@section('title', 'New Check-in - School ERP')

@section('content')
<div class="mb-4">
    <h2>🛏️ New Student Check-in</h2>
    <p class="text-muted">Allocate a bed to a student</p>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('hostel.allocations.store') }}">
            @csrf
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="student_id" class="form-label">Student <span class="text-danger">*</span></label>
                    <select class="form-select @error('student_id') is-invalid @enderror" 
                            id="student_id" 
                            name="student_id" 
                            required>
                        <option value="">Select Student</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ old('student_id', $studentId) == $student->id ? 'selected' : '' }}>
                                {{ $student->student_name }} ({{ $student->reg_no }})
                            </option>
                        @endforeach
                    </select>
                    @error('student_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @if($students->isEmpty())
                        <small class="text-muted">No students available for allocation. All active students may already have bed assignments.</small>
                    @endif
                </div>
                <div class="col-md-6">
                    <label for="bed_id" class="form-label">Bed <span class="text-danger">*</span></label>
                    <select class="form-select @error('bed_id') is-invalid @enderror" 
                            id="bed_id" 
                            name="bed_id" 
                            required>
                        <option value="">Select Bed</option>
                        @foreach($availableBeds as $bed)
                            <option value="{{ $bed->id }}" {{ old('bed_id') == $bed->id ? 'selected' : '' }}>
                                @if($bed->room && $bed->room->floor && $bed->room->floor->block && $bed->room->floor->block->hostel)
                                    {{ $bed->room->floor->block->hostel->name }} - 
                                    {{ $bed->room->floor->block->name }} - 
                                    {{ $bed->room->floor->name }} - 
                                    Room {{ $bed->room->room_number }} - 
                                    Bed {{ $bed->bed_number }}
                                @else
                                    Bed {{ $bed->bed_number }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('bed_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @if($availableBeds->isEmpty())
                        <small class="text-muted">No beds currently available for allocation.</small>
                    @endif
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="locker_id" class="form-label">Locker (Optional)</label>
                    <select class="form-select @error('locker_id') is-invalid @enderror" 
                            id="locker_id" 
                            name="locker_id">
                        <option value="">No Locker</option>
                    </select>
                    @error('locker_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Locker assignment is optional and can be configured after check-in if needed.</small>
                </div>
                <div class="col-md-6">
                    <label for="check_in_date" class="form-label">Check-in Date <span class="text-danger">*</span></label>
                    <input type="date" 
                           class="form-control @error('check_in_date') is-invalid @enderror" 
                           id="check_in_date" 
                           name="check_in_date" 
                           value="{{ old('check_in_date', date('Y-m-d')) }}" 
                           required>
                    @error('check_in_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="check_in_remarks" class="form-label">Check-in Remarks</label>
                    <textarea class="form-control @error('check_in_remarks') is-invalid @enderror" 
                              id="check_in_remarks" 
                              name="check_in_remarks" 
                              rows="3"
                              placeholder="Enter any remarks about the check-in">{{ old('check_in_remarks') }}</textarea>
                    @error('check_in_remarks')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> 
                <strong>Note:</strong> 
                A unique receipt number will be generated automatically upon check-in.
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Check In Student
                </button>
                <a href="{{ route('hostel.allocations.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
