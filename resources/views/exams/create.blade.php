@extends('layouts.app')

@section('title', 'Create Exam - School ERP')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Create New Exam</h2>
        <p class="text-muted mb-0">Add a new examination schedule</p>
    </div>
    <a href="{{ route('exams.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Back to List
    </a>
</div>

<form action="{{ route('exams.store') }}" method="POST">
    @csrf
    
    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Basic Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Exam Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Exam Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" placeholder="e.g., First Term Exam 2024" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Academic Term <span class="text-danger">*</span></label>
                            <select name="term_id" class="form-select @error('term_id') is-invalid @enderror" required>
                                <option value="">Select Academic Term</option>
                                @foreach($terms as $term)
                                    <option value="{{ $term->id }}" {{ old('term_id') == $term->id ? 'selected' : '' }}>
                                        {{ $term->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('term_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" 
                                   value="{{ old('start_date') }}" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">End Date <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" 
                                   value="{{ old('end_date') }}" required>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Quick Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Information</h5>
                </div>
                <div class="card-body">
                    <small class="text-muted">
                        <ul class="mb-0 ps-3">
                            <li>Create exam structure first</li>
                            <li>Assign subjects after creation</li>
                            <li>Set up timetable for each subject</li>
                            <li>Publish when ready</li>
                        </ul>
                    </small>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <i class="bi bi-check-circle"></i> Create Exam
                    </button>
                    <a href="{{ route('exams.index') }}" class="btn btn-secondary w-100">
                        <i class="bi bi-x-circle"></i> Cancel
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
// Validate end date is after start date
document.querySelector('input[name="end_date"]').addEventListener('change', function() {
    const startDate = document.querySelector('input[name="start_date"]').value;
    const endDate = this.value;
    
    if (startDate && endDate && endDate < startDate) {
        alert('End date must be after start date');
        this.value = '';
    }
});

// Validate passing marks is less than total marks
document.querySelector('input[name="passing_marks"]').addEventListener('blur', function() {
    const totalMarks = parseInt(document.querySelector('input[name="total_marks"]').value);
    const passingMarks = parseInt(this.value);
    
    if (passingMarks >= totalMarks) {
        alert('Passing marks must be less than total marks');
        this.value = Math.floor(totalMarks * 0.4);
    }
});
</script>
@endpush
@endsection
