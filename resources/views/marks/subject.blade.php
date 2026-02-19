@extends('layouts.app')

@section('title', 'Subject Marks - ' . $subject->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Subject Marks List</h2>
        <p class="text-muted mb-0">{{ $subject->name }}</p>
    </div>
    <a href="{{ route('marks.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Back to List
    </a>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('marks.subject', $subject) }}" method="GET" class="row g-3">
            <div class="col-md-4">
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
            
            <div class="col-md-4">
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
            
            <div class="col-md-4">
                <label class="form-label">Status</label>
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="pass" {{ request('status') == 'pass' ? 'selected' : '' }}>Pass</option>
                    <option value="fail" {{ request('status') == 'fail' ? 'selected' : '' }}>Fail</option>
                </select>
            </div>
        </form>
    </div>
</div>

<!-- Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-primary">
            <div class="card-body">
                <h6 class="text-muted">Total Students</h6>
                <h3 class="mb-0">{{ $marks->count() }}</h3>
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
                <h3 class="mb-0 text-info">{{ number_format($averageMarks, 2) }}</h3>
            </div>
        </div>
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
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th width="60">Rank</th>
                            <th>Roll No</th>
                            <th>Student Name</th>
                            <th>Exam</th>
                            <th>Class</th>
                            <th>Marks Obtained</th>
                            <th>Total Marks</th>
                            <th>Percentage</th>
                            <th>Grade</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($marks as $index => $mark)
                        <tr>
                            <td class="text-center">
                                @if($index == 0)
                                    <span class="badge bg-warning">🥇 1st</span>
                                @elseif($index == 1)
                                    <span class="badge bg-secondary">🥈 2nd</span>
                                @elseif($index == 2)
                                    <span class="badge bg-secondary">🥉 3rd</span>
                                @else
                                    <span class="badge bg-light text-dark">{{ $index + 1 }}</span>
                                @endif
                            </td>
                            <td>{{ $mark->student->roll_number ?? 'N/A' }}</td>
                            <td>
                                <a href="{{ route('marks.student', $mark->student) }}">
                                    {{ $mark->student->name }}
                                </a>
                            </td>
                            <td>{{ $mark->exam->name }}</td>
                            <td>{{ $mark->student->class->name ?? 'N/A' }}</td>
                            <td class="text-center"><strong>{{ $mark->marks_obtained }}</strong></td>
                            <td class="text-center">{{ $mark->total_marks }}</td>
                            <td class="text-center">
                                {{ $mark->total_marks > 0 ? number_format(($mark->marks_obtained / $mark->total_marks) * 100, 2) : 0 }}%
                            </td>
                            <td class="text-center">
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
                            <td class="text-center">
                                @if($mark->marks_obtained >= $mark->exam->passing_marks)
                                    <span class="badge bg-success">Pass</span>
                                @else
                                    <span class="badge bg-danger">Fail</span>
                                @endif
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
            
            <!-- Performance Distribution -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Grade Distribution</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="gradeChart" height="150"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Pass/Fail Ratio</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="statusChart" height="150"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center text-muted py-5">
                <i class="bi bi-inbox display-4 d-block mb-3"></i>
                <h5>No Marks Found</h5>
                <p>No marks have been entered for this subject with the selected filters.</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
@if($marks->count() > 0)
// Calculate grade distribution
const gradeDistribution = {
    'A+': 0, 'A': 0, 'B+': 0, 'B': 0, 'C': 0, 'D': 0, 'F': 0
};

@foreach($marks as $mark)
    @php
        $percentage = ($mark->marks_obtained / $mark->total_marks) * 100;
        if ($percentage >= 90) $grade = 'A+';
        elseif ($percentage >= 80) $grade = 'A';
        elseif ($percentage >= 70) $grade = 'B+';
        elseif ($percentage >= 60) $grade = 'B';
        elseif ($percentage >= 50) $grade = 'C';
        elseif ($percentage >= 40) $grade = 'D';
        else $grade = 'F';
    @endphp
    gradeDistribution['{{ $grade }}']++;
@endforeach

// Grade Chart
const gradeCtx = document.getElementById('gradeChart').getContext('2d');
new Chart(gradeCtx, {
    type: 'bar',
    data: {
        labels: Object.keys(gradeDistribution),
        datasets: [{
            label: 'Number of Students',
            data: Object.values(gradeDistribution),
            backgroundColor: [
                'rgba(40, 167, 69, 0.8)',
                'rgba(40, 167, 69, 0.6)',
                'rgba(23, 162, 184, 0.8)',
                'rgba(23, 162, 184, 0.6)',
                'rgba(255, 193, 7, 0.8)',
                'rgba(255, 193, 7, 0.6)',
                'rgba(220, 53, 69, 0.8)'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Status Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Pass', 'Fail'],
        datasets: [{
            data: [{{ $passCount }}, {{ $failCount }}],
            backgroundColor: [
                'rgba(40, 167, 69, 0.8)',
                'rgba(220, 53, 69, 0.8)'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
@endif

function exportToExcel() {
    window.location.href = '{{ route("marks.export") }}?subject_id={{ $subject->id }}&' + new URLSearchParams({
        exam_id: '{{ request("exam_id", "") }}',
        class_id: '{{ request("class_id", "") }}',
        status: '{{ request("status", "") }}'
    }).toString();
}
</script>
@endpush
@endsection
