@extends('layouts.app')

@section('title', 'Student Marks - ' . $student->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Student Marks Report</h2>
        <p class="text-muted mb-0">{{ $student->name }} - Roll No: {{ $student->roll_number ?? 'N/A' }}</p>
    </div>
    <div>
        <button onclick="window.print()" class="btn btn-primary me-2 d-print-none">
            <i class="bi bi-printer"></i> Print
        </button>
        <a href="{{ route('marks.index') }}" class="btn btn-secondary d-print-none">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="row">
    <!-- Student Info -->
    <div class="col-lg-3">
        <div class="card mb-4">
            <div class="card-body text-center">
                @if($student->photo)
                    <img src="{{ asset('storage/' . $student->photo) }}" alt="{{ $student->name }}" 
                         class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                @else
                    <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 120px; height: 120px; font-size: 48px;">
                        {{ substr($student->name, 0, 1) }}
                    </div>
                @endif
                
                <h5 class="mb-1">{{ $student->name }}</h5>
                <p class="text-muted mb-2">{{ $student->class->name ?? 'N/A' }}</p>
                <p class="text-muted small mb-0">Roll No: {{ $student->roll_number ?? 'N/A' }}</p>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Overall Performance</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted">Total Exams</small>
                    <h4>{{ $marks->pluck('exam_id')->unique()->count() }}</h4>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Total Subjects</small>
                    <h4>{{ $marks->count() }}</h4>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Average Percentage</small>
                    <h4 class="text-success">{{ number_format($averagePercentage, 2) }}%</h4>
                </div>
                <div>
                    <small class="text-muted">Pass Rate</small>
                    <h4 class="text-info">{{ number_format($passRate, 2) }}%</h4>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Marks Details -->
    <div class="col-lg-9">
        <!-- Filter by Exam -->
        <div class="card mb-4 d-print-none">
            <div class="card-body">
                <form action="{{ route('marks.student', $student) }}" method="GET" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Filter by Exam</label>
                        <select name="exam_id" class="form-select" onchange="this.form.submit()">
                            <option value="">All Exams</option>
                            @foreach($exams as $exam)
                                <option value="{{ $exam->id }}" {{ request('exam_id') == $exam->id ? 'selected' : '' }}>
                                    {{ $exam->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        </div>
        
        @if($marks->count() > 0)
            @php
                $groupedMarks = $marks->groupBy('exam_id');
            @endphp
            
            @foreach($groupedMarks as $examId => $examMarks)
                @php
                    $exam = $examMarks->first()->exam;
                    $totalObtained = $examMarks->sum('marks_obtained');
                    $totalMax = $examMarks->sum('total_marks');
                    $examPercentage = ($totalMax > 0) ? ($totalObtained / $totalMax) * 100 : 0;
                @endphp
                
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ $exam->name }}</h5>
                        <span class="badge bg-{{ $examPercentage >= $exam->passing_marks ? 'success' : 'danger' }}">
                            {{ number_format($examPercentage, 2) }}%
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Subject</th>
                                        <th width="120">Marks Obtained</th>
                                        <th width="120">Total Marks</th>
                                        <th width="120">Percentage</th>
                                        <th width="100">Grade</th>
                                        <th width="100">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($examMarks as $mark)
                                    <tr>
                                        <td><strong>{{ $mark->subject->name }}</strong></td>
                                        <td class="text-center">{{ $mark->marks_obtained }}</td>
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
                                            @if($mark->marks_obtained >= $exam->passing_marks)
                                                <span class="badge bg-success">Pass</span>
                                            @else
                                                <span class="badge bg-danger">Fail</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td><strong>Total</strong></td>
                                        <td class="text-center"><strong>{{ $totalObtained }}</strong></td>
                                        <td class="text-center"><strong>{{ $totalMax }}</strong></td>
                                        <td class="text-center"><strong>{{ number_format($examPercentage, 2) }}%</strong></td>
                                        <td colspan="2" class="text-center">
                                            @if($examPercentage >= $exam->passing_marks)
                                                <span class="badge bg-success">PASS</span>
                                            @else
                                                <span class="badge bg-danger">FAIL</span>
                                            @endif
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        
                        @if($exam->is_results_published)
                            <div class="text-end mt-3 d-print-none">
                                <a href="{{ route('results.marksheet', ['exam' => $exam->id, 'student' => $student->id]) }}" 
                                   class="btn btn-sm btn-primary" target="_blank">
                                    <i class="bi bi-file-earmark-pdf"></i> View Marksheet
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
            
            <!-- Performance Chart -->
            <div class="card d-print-none">
                <div class="card-header">
                    <h5 class="mb-0">Performance Trend</h5>
                </div>
                <div class="card-body">
                    <canvas id="performanceChart" height="100"></canvas>
                </div>
            </div>
        @else
            <div class="card">
                <div class="card-body text-center text-muted py-5">
                    <i class="bi bi-inbox display-4 d-block mb-3"></i>
                    <h5>No Marks Found</h5>
                    <p>No examination marks have been entered for this student yet.</p>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
@if($marks->count() > 0)
// Performance Chart
const ctx = document.getElementById('performanceChart').getContext('2d');
const chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($groupedMarks->map(function($marks) {
            return $marks->first()->exam->name;
        })->values()),
        datasets: [{
            label: 'Percentage',
            data: @json($groupedMarks->map(function($marks) {
                $total = $marks->sum('marks_obtained');
                $max = $marks->sum('total_marks');
                return $max > 0 ? ($total / $max) * 100 : 0;
            })->values()),
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            },
            title: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                max: 100,
                ticks: {
                    callback: function(value) {
                        return value + '%';
                    }
                }
            }
        }
    }
});
@endif
</script>
@endpush

<style>
@media print {
    .d-print-none {
        display: none !important;
    }
    
    body {
        font-size: 12px;
    }
    
    .card {
        page-break-inside: avoid;
        border: 1px solid #dee2e6;
        box-shadow: none;
    }
}
</style>
@endsection
