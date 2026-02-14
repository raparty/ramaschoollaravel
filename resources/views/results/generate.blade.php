@extends('layouts.app')

@section('title', 'Generate Results - School ERP')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Generate Exam Results</h2>
        <p class="text-muted mb-0">Calculate and generate student results from marks</p>
    </div>
    <a href="{{ route('results.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Back to Results
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Selection Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Select Exam</h5>
            </div>
            <div class="card-body">
                <form id="examSelectionForm" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Academic Year <span class="text-danger">*</span></label>
                        <select name="academic_year_id" id="academicYearSelect" class="form-select" required>
                            <option value="">Select Academic Year</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>
                                    {{ $year->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Class <span class="text-danger">*</span></label>
                        <select name="class_id" id="classSelect" class="form-select" required>
                            <option value="">Select Class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-12">
                        <label class="form-label">Exam <span class="text-danger">*</span></label>
                        <select name="exam_id" id="examSelect" class="form-select" required>
                            <option value="">Select Exam</option>
                        </select>
                    </div>
                    
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Load Exam Details
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Generate Results Form -->
        @if(isset($exam))
        <form action="{{ route('results.store') }}" method="POST">
            @csrf
            <input type="hidden" name="exam_id" value="{{ $exam->id }}">
            
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Exam Details: {{ $exam->name }}</h5>
                    <span class="badge bg-info">{{ $exam->class->name }}</span>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <h6 class="text-muted">Total Students</h6>
                            <h4>{{ $studentsCount }}</h4>
                        </div>
                        <div class="col-md-3">
                            <h6 class="text-muted">Subjects Assigned</h6>
                            <h4>{{ $exam->subjects->count() }}</h4>
                        </div>
                        <div class="col-md-3">
                            <h6 class="text-muted">Marks Entered</h6>
                            <h4>{{ $marksEnteredCount }}</h4>
                        </div>
                        <div class="col-md-3">
                            <h6 class="text-muted">Completion</h6>
                            <h4 class="{{ $completionPercentage >= 100 ? 'text-success' : 'text-warning' }}">
                                {{ number_format($completionPercentage, 1) }}%
                            </h4>
                        </div>
                    </div>
                    
                    @if($completionPercentage < 100)
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            <strong>Warning:</strong> Not all marks have been entered yet. 
                            {{ $missingMarksCount }} marks entries are still pending. 
                            Results can still be generated, but they may be incomplete.
                        </div>
                    @else
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle"></i>
                            All marks have been entered. Ready to generate results!
                        </div>
                    @endif
                    
                    <div class="mb-3">
                        <h6>Subjects in this Exam:</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Subject</th>
                                        <th width="120">Max Marks</th>
                                        <th width="150">Marks Entered</th>
                                        <th width="100">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($exam->subjects as $subject)
                                    @php
                                        $subjectMarksCount = $exam->marks()
                                            ->where('subject_id', $subject->id)
                                            ->count();
                                        $subjectCompletion = $studentsCount > 0 
                                            ? ($subjectMarksCount / $studentsCount) * 100 
                                            : 0;
                                    @endphp
                                    <tr>
                                        <td>{{ $subject->name }}</td>
                                        <td class="text-center">{{ $subject->pivot->max_marks }}</td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar {{ $subjectCompletion >= 100 ? 'bg-success' : 'bg-warning' }}" 
                                                     style="width: {{ $subjectCompletion }}%">
                                                    {{ $subjectMarksCount }}/{{ $studentsCount }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @if($subjectCompletion >= 100)
                                                <span class="badge bg-success">Complete</span>
                                            @else
                                                <span class="badge bg-warning">Pending</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="regenerate" class="form-check-input" id="regenerate" value="1">
                            <label class="form-check-label" for="regenerate">
                                Regenerate existing results (if any)
                            </label>
                        </div>
                        <small class="text-muted">Check this to recalculate results for students who already have results</small>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="calculate_rank" class="form-check-input" id="calculateRank" value="1" checked>
                            <label class="form-check-label" for="calculateRank">
                                Calculate class rank
                            </label>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="bi bi-calculator"></i> Generate Results
                        </button>
                        <a href="{{ route('results.index') }}" class="btn btn-secondary btn-lg">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                    </div>
                </div>
            </div>
        </form>
        @endif
    </div>
    
    <div class="col-lg-4">
        <!-- Process Information -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> Process Steps</h5>
            </div>
            <div class="card-body">
                <ol class="ps-3 mb-0">
                    <li class="mb-2">Select academic year and class</li>
                    <li class="mb-2">Choose the exam to generate results for</li>
                    <li class="mb-2">Review marks entry completion status</li>
                    <li class="mb-2">Configure generation options</li>
                    <li class="mb-2">Click "Generate Results" button</li>
                    <li>View and publish results</li>
                </ol>
            </div>
        </div>
        
        <!-- Calculation Method -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-calculator"></i> Calculation Method</h5>
            </div>
            <div class="card-body">
                <small class="text-muted">
                    <strong>Total Marks:</strong> Sum of all subject marks<br>
                    <strong>Obtained Marks:</strong> Sum of marks obtained in all subjects<br>
                    <strong>Percentage:</strong> (Obtained / Total) × 100<br>
                    <strong>Grade:</strong> Based on percentage thresholds<br>
                    <strong>Status:</strong> Pass if obtained marks ≥ passing marks in all subjects<br>
                    <strong>Rank:</strong> Sorted by percentage (highest first)
                </small>
            </div>
        </div>
        
        <!-- Grading Scale -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-award"></i> Grading Scale</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tbody>
                        <tr>
                            <td><span class="badge bg-success">A+</span></td>
                            <td>90% and above</td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-success">A</span></td>
                            <td>80% - 89%</td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-info">B+</span></td>
                            <td>70% - 79%</td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-info">B</span></td>
                            <td>60% - 69%</td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-warning">C</span></td>
                            <td>50% - 59%</td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-warning">D</span></td>
                            <td>40% - 49%</td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-danger">F</span></td>
                            <td>Below 40%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Load exams based on selection
function loadExams() {
    const academicYearId = document.getElementById('academicYearSelect').value;
    const classId = document.getElementById('classSelect').value;
    const examSelect = document.getElementById('examSelect');
    
    if (academicYearId && classId) {
        examSelect.innerHTML = '<option value="">Loading...</option>';
        
        fetch(`/api/exams?academic_year_id=${academicYearId}&class_id=${classId}`)
            .then(response => response.json())
            .then(data => {
                examSelect.innerHTML = '<option value="">Select Exam</option>';
                data.forEach(exam => {
                    examSelect.innerHTML += `<option value="${exam.id}">${exam.name}</option>`;
                });
            })
            .catch(error => {
                examSelect.innerHTML = '<option value="">Error loading exams</option>';
            });
    } else {
        examSelect.innerHTML = '<option value="">Select Academic Year and Class first</option>';
    }
}

document.getElementById('academicYearSelect').addEventListener('change', loadExams);
document.getElementById('classSelect').addEventListener('change', loadExams);

// Handle form submission
document.getElementById('examSelectionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const examId = document.getElementById('examSelect').value;
    const academicYearId = document.getElementById('academicYearSelect').value;
    const classId = document.getElementById('classSelect').value;
    
    if (examId && academicYearId && classId) {
        window.location.href = `{{ route('results.generate') }}?exam_id=${examId}&academic_year_id=${academicYearId}&class_id=${classId}`;
    } else {
        alert('Please select all required fields');
    }
});
</script>
@endpush
@endsection
