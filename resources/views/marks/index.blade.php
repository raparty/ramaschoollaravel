@extends('layouts.app')

@section('title', 'Marks Entry Dashboard - School ERP')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Marks Entry Dashboard</h2>
        <p class="text-muted mb-0">Enter and manage student examination marks</p>
    </div>
    <a href="{{ route('marks.entry') }}" class="btn btn-primary">
        <i class="bi bi-pencil-square"></i> Enter Marks
    </a>
</div>

<!-- Summary Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-primary">
            <div class="card-body">
                <h6 class="text-muted">Total Marks Entered</h6>
                <h3 class="mb-0">{{ $totalMarks }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-success">
            <div class="card-body">
                <h6 class="text-muted">Pass Count</h6>
                <h3 class="mb-0 text-success">{{ $passCount }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-danger">
            <div class="card-body">
                <h6 class="text-muted">Fail Count</h6>
                <h3 class="mb-0 text-danger">{{ $failCount }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-info">
            <div class="card-body">
                <h6 class="text-muted">Average Marks</h6>
                <h3 class="mb-0 text-info">{{ number_format($averageMarks, 1) }}%</h3>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('marks.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Exam</label>
                <select name="exam_id" class="form-select" onchange="this.form.submit()">
                    <option value="">All Exams</option>
                    @foreach($exams as $exam)
                        <option value="{{ $exam->id }}" {{ request('exam_id') == $exam->id ? 'selected' : '' }}>
                            {{ $exam->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label">Class</label>
                <select name="class_id" class="form-select" onchange="this.form.submit()">
                    <option value="">All Classes</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label">Subject</label>
                <select name="subject_id" class="form-select" onchange="this.form.submit()">
                    <option value="">All Subjects</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="pass" {{ request('status') == 'pass' ? 'selected' : '' }}>Pass</option>
                    <option value="fail" {{ request('status') == 'fail' ? 'selected' : '' }}>Fail</option>
                </select>
            </div>
            
            <div class="col-12">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-funnel"></i> Apply Filters
                </button>
                <a href="{{ route('marks.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Clear Filters
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Marks List -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Student Marks</h5>
        <div>
            <button class="btn btn-sm btn-success" onclick="exportToExcel()">
                <i class="bi bi-file-earmark-excel"></i> Export
            </button>
        </div>
    </div>
    <div class="card-body">
        @if($marks->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Roll No</th>
                            <th>Exam</th>
                            <th>Subject</th>
                            <th>Marks Obtained</th>
                            <th>Total Marks</th>
                            <th>Percentage</th>
                            <th>Grade</th>
                            <th>Status</th>
                            <th>Entered By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($marks as $mark)
                        <tr>
                            <td>
                                <a href="{{ route('marks.student', $mark->student) }}">
                                    {{ $mark->student->name }}
                                </a>
                            </td>
                            <td>{{ $mark->student->roll_number ?? 'N/A' }}</td>
                            <td>{{ $mark->exam->name }}</td>
                            <td>{{ $mark->subject->name }}</td>
                            <td><strong>{{ $mark->marks_obtained }}</strong></td>
                            <td>{{ $mark->total_marks }}</td>
                            <td>{{ $mark->total_marks > 0 ? number_format(($mark->marks_obtained / $mark->total_marks) * 100, 2) : 0 }}%</td>
                            <td>
                                @php
                                    $percentage = $mark->total_marks > 0 ? ($mark->marks_obtained / $mark->total_marks) * 100 : 0;
                                    if ($percentage >= 90) {
                                        $grade = 'A+';
                                        $gradeClass = 'success';
                                    } elseif ($percentage >= 80) {
                                        $grade = 'A';
                                        $gradeClass = 'success';
                                    } elseif ($percentage >= 70) {
                                        $grade = 'B+';
                                        $gradeClass = 'info';
                                    } elseif ($percentage >= 60) {
                                        $grade = 'B';
                                        $gradeClass = 'info';
                                    } elseif ($percentage >= 50) {
                                        $grade = 'C';
                                        $gradeClass = 'warning';
                                    } elseif ($percentage >= 40) {
                                        $grade = 'D';
                                        $gradeClass = 'warning';
                                    } else {
                                        $grade = 'F';
                                        $gradeClass = 'danger';
                                    }
                                @endphp
                                <span class="badge bg-{{ $gradeClass }}">{{ $grade }}</span>
                            </td>
                            <td>
                                @if($mark->marks_obtained >= $mark->exam->passing_marks)
                                    <span class="badge bg-success">Pass</span>
                                @else
                                    <span class="badge bg-danger">Fail</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $mark->enteredBy->name ?? 'N/A' }}<br>
                                    {{ $mark->created_at->format('d M, Y') }}
                                </small>
                            </td>
                            <td>
                                <a href="{{ route('marks.edit', $mark) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('marks.destroy', $mark) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to delete this mark?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-3">
                {{ $marks->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center text-muted py-5">
                <i class="bi bi-inbox display-4 d-block mb-3"></i>
                <h5>No Marks Entered Yet</h5>
                <p>Start by entering marks for an exam</p>
                <a href="{{ route('marks.entry') }}" class="btn btn-primary">
                    <i class="bi bi-pencil-square"></i> Enter Marks
                </a>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function exportToExcel() {
    // Simple CSV export
    window.location.href = '{{ route("marks.export") }}?' + new URLSearchParams({
        exam_id: '{{ request("exam_id", "") }}',
        class_id: '{{ request("class_id", "") }}',
        subject_id: '{{ request("subject_id", "") }}',
        status: '{{ request("status", "") }}'
    }).toString();
}
</script>
@endpush
@endsection
