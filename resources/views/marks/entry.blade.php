@extends('layouts.app')

@section('title', 'Bulk Marks Entry - School ERP')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Bulk Marks Entry</h2>
        <p class="text-muted mb-0">Enter marks for multiple students at once</p>
    </div>
    <a href="{{ route('marks.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Back to List
    </a>
</div>

<div class="row">
    <div class="col-lg-12">
        <!-- Selection Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Select Exam & Subject</h5>
            </div>
            <div class="card-body">
                <form id="selectionForm" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Exam <span class="text-danger">*</span></label>
                        <select name="exam_id" id="examSelect" class="form-select" required>
                            <option value="">Select Exam</option>
                            @foreach($exams as $exam)
                                <option value="{{ $exam->id }}" {{ request('exam_id') == $exam->id ? 'selected' : '' }}>
                                    {{ $exam->name }} - {{ $exam->class->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">Subject <span class="text-danger">*</span></label>
                        <select name="subject_id" id="subjectSelect" class="form-select" required>
                            <option value="">Select Subject</option>
                        </select>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Load Students
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Marks Entry Form -->
        @if(isset($students) && $students->count() > 0)
        <form action="{{ route('marks.store') }}" method="POST">
            @csrf
            <input type="hidden" name="exam_id" value="{{ request('exam_id') }}">
            <input type="hidden" name="subject_id" value="{{ request('subject_id') }}">
            
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Enter Marks for {{ $selectedExam->name }} - {{ $selectedSubject->name }}</h5>
                    <span class="badge bg-info">
                        Total Students: {{ $students->count() }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        <strong>Max Marks:</strong> {{ $maxMarks }} | 
                        <strong>Passing Marks:</strong> {{ $selectedExam->passing_marks }}
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th width="60">S.No.</th>
                                    <th width="120">Roll No</th>
                                    <th>Student Name</th>
                                    <th width="150">Marks Obtained <span class="text-danger">*</span></th>
                                    <th width="150">Status</th>
                                    <th width="200">Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $index => $student)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $student->roll_number ?? 'N/A' }}</td>
                                    <td>
                                        <strong>{{ $student->name }}</strong>
                                        <input type="hidden" name="marks[{{ $index }}][student_id]" value="{{ $student->id }}">
                                    </td>
                                    <td>
                                        <input type="number" 
                                               name="marks[{{ $index }}][marks_obtained]" 
                                               class="form-control @error('marks.'.$index.'.marks_obtained') is-invalid @enderror marks-input" 
                                               value="{{ old('marks.'.$index.'.marks_obtained', $student->existingMark->marks_obtained ?? '') }}"
                                               min="0" 
                                               max="{{ $maxMarks }}"
                                               step="0.5"
                                               data-row="{{ $index }}"
                                               data-passing="{{ $selectedExam->passing_marks }}"
                                               required>
                                        @error('marks.'.$index.'.marks_obtained')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td>
                                        <select name="marks[{{ $index }}][status]" class="form-select status-select" id="status-{{ $index }}">
                                            <option value="present" {{ old('marks.'.$index.'.status', $student->existingMark->status ?? 'present') == 'present' ? 'selected' : '' }}>Present</option>
                                            <option value="absent" {{ old('marks.'.$index.'.status', $student->existingMark->status ?? '') == 'absent' ? 'selected' : '' }}>Absent</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" 
                                               name="marks[{{ $index }}][remarks]" 
                                               class="form-control form-control-sm" 
                                               value="{{ old('marks.'.$index.'.remarks', $student->existingMark->remarks ?? '') }}"
                                               placeholder="Optional">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6>Quick Stats</h6>
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <h4 class="text-success mb-0" id="passCount">0</h4>
                                            <small class="text-muted">Pass</small>
                                        </div>
                                        <div class="col-4">
                                            <h4 class="text-danger mb-0" id="failCount">0</h4>
                                            <small class="text-muted">Fail</small>
                                        </div>
                                        <div class="col-4">
                                            <h4 class="text-info mb-0" id="avgMarks">0</h4>
                                            <small class="text-muted">Average</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-save"></i> Save All Marks
                            </button>
                            <a href="{{ route('marks.index') }}" class="btn btn-secondary btn-lg">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        @elseif(request('exam_id') && request('subject_id'))
        <div class="card">
            <div class="card-body text-center text-muted py-5">
                <i class="bi bi-people display-4 d-block mb-3"></i>
                <h5>No Students Found</h5>
                <p>No students are enrolled in the selected class for this exam.</p>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
// Load subjects when exam is selected
document.getElementById('examSelect').addEventListener('change', function() {
    const examId = this.value;
    const subjectSelect = document.getElementById('subjectSelect');
    
    subjectSelect.innerHTML = '<option value="">Loading...</option>';
    
    if (examId) {
        fetch(`/api/exams/${examId}/subjects`)
            .then(response => response.json())
            .then(data => {
                subjectSelect.innerHTML = '<option value="">Select Subject</option>';
                data.forEach(subject => {
                    subjectSelect.innerHTML += `<option value="${subject.id}">${subject.name}</option>`;
                });
            })
            .catch(error => {
                subjectSelect.innerHTML = '<option value="">Error loading subjects</option>';
            });
    } else {
        subjectSelect.innerHTML = '<option value="">Select Subject</option>';
    }
});

// Handle form submission
document.getElementById('selectionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const examId = document.getElementById('examSelect').value;
    const subjectId = document.getElementById('subjectSelect').value;
    
    if (examId && subjectId) {
        window.location.href = `{{ route('marks.entry') }}?exam_id=${examId}&subject_id=${subjectId}`;
    } else {
        alert('Please select both exam and subject');
    }
});

// Calculate stats on marks input
@if(isset($students))
document.querySelectorAll('.marks-input').forEach(input => {
    input.addEventListener('input', calculateStats);
});

function calculateStats() {
    let passCount = 0;
    let failCount = 0;
    let totalMarks = 0;
    let count = 0;
    
    document.querySelectorAll('.marks-input').forEach(input => {
        const marks = parseFloat(input.value);
        const passing = parseFloat(input.dataset.passing);
        
        if (!isNaN(marks)) {
            totalMarks += marks;
            count++;
            
            if (marks >= passing) {
                passCount++;
            } else {
                failCount++;
            }
        }
    });
    
    document.getElementById('passCount').textContent = passCount;
    document.getElementById('failCount').textContent = failCount;
    document.getElementById('avgMarks').textContent = count > 0 ? (totalMarks / count).toFixed(2) : 0;
}

// Initialize stats
calculateStats();

// Handle absent status
document.querySelectorAll('.status-select').forEach(select => {
    select.addEventListener('change', function() {
        const row = this.id.split('-')[1];
        const marksInput = document.querySelector(`input[data-row="${row}"]`);
        
        if (this.value === 'absent') {
            marksInput.value = 0;
            marksInput.disabled = true;
        } else {
            marksInput.disabled = false;
        }
        
        calculateStats();
    });
});
@endif
</script>
@endpush
@endsection
