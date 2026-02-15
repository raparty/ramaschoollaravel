@extends('layouts.app')

@section('title', 'Manage Subjects - ' . $exam->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Manage Exam Subjects</h2>
        <p class="text-muted mb-0">{{ $exam->name }} - {{ $exam->class?->name ?? 'N/A' }}</p>
    </div>
    <a href="{{ route('exams.show', $exam) }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Back to Exam
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Assigned Subjects -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Assigned Subjects ({{ $exam->subjects->count() }})</h5>
            </div>
            <div class="card-body">
                @if($exam->subjects->count() > 0)
                    <form action="{{ route('exams.subjects.update', $exam) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Subject</th>
                                        <th width="150">Exam Date</th>
                                        <th width="120">Start Time</th>
                                        <th width="100">Duration (mins)</th>
                                        <th width="100">Max Marks</th>
                                        <th width="80">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($exam->subjects as $index => $subject)
                                    <tr>
                                        <td>
                                            <strong>{{ $subject->name }}</strong>
                                            <input type="hidden" name="subjects[{{ $index }}][subject_id]" value="{{ $subject->id }}">
                                        </td>
                                        <td>
                                            <input type="date" 
                                                   name="subjects[{{ $index }}][exam_date]" 
                                                   class="form-control form-control-sm @error('subjects.'.$index.'.exam_date') is-invalid @enderror" 
                                                   value="{{ old('subjects.'.$index.'.exam_date', $subject->pivot->exam_date ? \Carbon\Carbon::parse($subject->pivot->exam_date)->format('Y-m-d') : '') }}"
                                                   min="{{ $exam->start_date->format('Y-m-d') }}"
                                                   max="{{ $exam->end_date->format('Y-m-d') }}">
                                            @error('subjects.'.$index.'.exam_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="time" 
                                                   name="subjects[{{ $index }}][start_time]" 
                                                   class="form-control form-control-sm @error('subjects.'.$index.'.start_time') is-invalid @enderror" 
                                                   value="{{ old('subjects.'.$index.'.start_time', $subject->pivot->start_time) }}">
                                            @error('subjects.'.$index.'.start_time')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="number" 
                                                   name="subjects[{{ $index }}][duration]" 
                                                   class="form-control form-control-sm @error('subjects.'.$index.'.duration') is-invalid @enderror" 
                                                   value="{{ old('subjects.'.$index.'.duration', $subject->pivot->duration ?? 60) }}"
                                                   min="30" max="300" step="15">
                                            @error('subjects.'.$index.'.duration')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="number" 
                                                   name="subjects[{{ $index }}][max_marks]" 
                                                   class="form-control form-control-sm @error('subjects.'.$index.'.max_marks') is-invalid @enderror" 
                                                   value="{{ old('subjects.'.$index.'.max_marks', $subject->pivot->max_marks ?? $exam->total_marks) }}"
                                                   min="1" max="{{ $exam->total_marks }}" required>
                                            @error('subjects.'.$index.'.max_marks')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-danger" onclick="removeSubject({{ $subject->id }})">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Update Schedule
                            </button>
                        </div>
                    </form>
                @else
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-inbox display-4 d-block mb-3"></i>
                        <p>No subjects assigned yet</p>
                        <p class="small">Add subjects from the available list</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Add Subjects -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Add Subjects</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('exams.subjects.store', $exam) }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label">Select Subject <span class="text-danger">*</span></label>
                        <select name="subject_id" class="form-select @error('subject_id') is-invalid @enderror" required>
                            <option value="">Choose a subject...</option>
                            @foreach($availableSubjects as $subject)
                                <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('subject_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($availableSubjects->isEmpty())
                            <small class="text-muted">All subjects have been assigned</small>
                        @endif
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Exam Date</label>
                        <input type="date" name="exam_date" class="form-control @error('exam_date') is-invalid @enderror" 
                               value="{{ old('exam_date') }}"
                               min="{{ $exam->start_date->format('Y-m-d') }}"
                               max="{{ $exam->end_date->format('Y-m-d') }}">
                        @error('exam_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Start Time</label>
                        <input type="time" name="start_time" class="form-control @error('start_time') is-invalid @enderror" 
                               value="{{ old('start_time', '09:00') }}">
                        @error('start_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Duration (minutes)</label>
                        <input type="number" name="duration" class="form-control @error('duration') is-invalid @enderror" 
                               value="{{ old('duration', 60) }}" min="30" max="300" step="15">
                        @error('duration')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Max Marks <span class="text-danger">*</span></label>
                        <input type="number" name="max_marks" class="form-control @error('max_marks') is-invalid @enderror" 
                               value="{{ old('max_marks', $exam->total_marks) }}" min="1" max="{{ $exam->total_marks }}" required>
                        @error('max_marks')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Maximum: {{ $exam->total_marks }}</small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100" {{ $availableSubjects->isEmpty() ? 'disabled' : '' }}>
                        <i class="bi bi-plus-circle"></i> Add Subject
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Quick Tips -->
        <div class="card border-info">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-lightbulb"></i> Tips</h5>
            </div>
            <div class="card-body">
                <ul class="small mb-0 ps-3">
                    <li>Add subjects one by one</li>
                    <li>Set exam date within the exam period</li>
                    <li>Schedule subjects to avoid conflicts</li>
                    <li>Update timetable after adding subjects</li>
                    <li>Max marks cannot exceed exam total marks</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Remove Subject Form (Hidden) -->
<form id="removeSubjectForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
function removeSubject(subjectId) {
    if (confirm('Are you sure you want to remove this subject from the exam?')) {
        const form = document.getElementById('removeSubjectForm');
        form.action = '{{ route("exams.subjects.destroy", $exam) }}'.replace('{{ $exam->id }}', '{{ $exam->id }}') + '?subject_id=' + subjectId;
        form.submit();
    }
}

// Validate exam dates are within exam period
document.querySelectorAll('input[type="date"]').forEach(input => {
    input.addEventListener('change', function() {
        const examStart = new Date('{{ $exam->start_date->format("Y-m-d") }}');
        const examEnd = new Date('{{ $exam->end_date->format("Y-m-d") }}');
        const selectedDate = new Date(this.value);
        
        if (selectedDate < examStart || selectedDate > examEnd) {
            alert('Exam date must be between {{ $exam->start_date->format("d M, Y") }} and {{ $exam->end_date->format("d M, Y") }}');
            this.value = '';
        }
    });
});
</script>
@endpush
@endsection
