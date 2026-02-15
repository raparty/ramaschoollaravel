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
                            <label class="form-label">Academic Session <span class="text-danger">*</span></label>
                            <select name="session" class="form-select @error('session') is-invalid @enderror" required>
                                <option value="">Select Academic Session</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ old('session', $currentSession) == $year->id ? 'selected' : '' }}>
                                        {{ $year->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('session')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Class <span class="text-danger">*</span></label>
                            <select name="class_id" class="form-select @error('class_id') is-invalid @enderror" required>
                                <option value="">Select Class</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('class_id')
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
                        
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                      rows="3" placeholder="Optional exam details or instructions">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Grading Configuration -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Grading Configuration</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Total Marks <span class="text-danger">*</span></label>
                            <input type="number" name="total_marks" class="form-control @error('total_marks') is-invalid @enderror" 
                                   value="{{ old('total_marks', 100) }}" min="1" required>
                            @error('total_marks')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Passing Marks <span class="text-danger">*</span></label>
                            <input type="number" name="passing_marks" class="form-control @error('passing_marks') is-invalid @enderror" 
                                   value="{{ old('passing_marks', 40) }}" min="1" required>
                            @error('passing_marks')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Grace Marks</label>
                            <input type="number" name="grace_marks" class="form-control @error('grace_marks') is-invalid @enderror" 
                                   value="{{ old('grace_marks', 0) }}" min="0">
                            @error('grace_marks')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12">
                            <div class="form-check">
                                <input type="checkbox" name="enable_grace_marks" class="form-check-input" 
                                       id="enableGraceMarks" value="1" {{ old('enable_grace_marks') ? 'checked' : '' }}>
                                <label class="form-check-label" for="enableGraceMarks">
                                    Enable grace marks for this exam
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Exam Settings -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Exam Settings</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="scheduled" {{ old('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                            <option value="ongoing" {{ old('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="is_published" class="form-check-input" 
                                   id="isPublished" value="1" {{ old('is_published') ? 'checked' : '' }}>
                            <label class="form-check-label" for="isPublished">
                                Publish Exam
                            </label>
                        </div>
                        <small class="text-muted">Students can view published exams</small>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="is_results_published" class="form-check-input" 
                                   id="isResultsPublished" value="1" {{ old('is_results_published') ? 'checked' : '' }}>
                            <label class="form-check-label" for="isResultsPublished">
                                Publish Results
                            </label>
                        </div>
                        <small class="text-muted">Allow students to view their results</small>
                    </div>
                </div>
            </div>
            
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
