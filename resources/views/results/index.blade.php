@extends('layouts.app')

@section('title', 'Results - School ERP')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Examination Results</h2>
        <p class="text-muted mb-0">View and manage student results</p>
    </div>
    <a href="{{ route('results.generate') }}" class="btn btn-primary">
        <i class="bi bi-calculator"></i> Generate Results
    </a>
</div>

<!-- Summary Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-primary">
            <div class="card-body">
                <h6 class="text-muted">Total Results</h6>
                <h3 class="mb-0">{{ $results->total() }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-success">
            <div class="card-body">
                <h6 class="text-muted">Pass</h6>
                <h3 class="mb-0 text-success">{{ $passCount }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-danger">
            <div class="card-body">
                <h6 class="text-muted">Fail</h6>
                <h3 class="mb-0 text-danger">{{ $failCount }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-info">
            <div class="card-body">
                <h6 class="text-muted">Pass Percentage</h6>
                <h3 class="mb-0 text-info">{{ $results->total() > 0 ? number_format(($passCount / $results->total()) * 100, 1) : 0 }}%</h3>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('results.index') }}" method="GET" class="row g-3">
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
                <label class="form-label">Status</label>
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="pass" {{ request('status') == 'pass' ? 'selected' : '' }}>Pass</option>
                    <option value="fail" {{ request('status') == 'fail' ? 'selected' : '' }}>Fail</option>
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label">Search Student</label>
                <input type="text" name="search" class="form-control" placeholder="Name or Roll No" value="{{ request('search') }}">
            </div>
            
            <div class="col-12">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-funnel"></i> Apply Filters
                </button>
                <a href="{{ route('results.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Clear Filters
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Results List -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Results List</h5>
        <div>
            <button class="btn btn-sm btn-success" onclick="exportToExcel()">
                <i class="bi bi-file-earmark-excel"></i> Export
            </button>
        </div>
    </div>
    <div class="card-body">
        @if($results->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Roll No</th>
                            <th>Student Name</th>
                            <th>Class</th>
                            <th>Exam</th>
                            <th>Total Marks</th>
                            <th>Obtained Marks</th>
                            <th>Percentage</th>
                            <th>Grade</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $index => $result)
                        <tr>
                            <td>
                                @if($result->rank == 1)
                                    <span class="badge bg-warning">🥇 1st</span>
                                @elseif($result->rank == 2)
                                    <span class="badge bg-secondary">🥈 2nd</span>
                                @elseif($result->rank == 3)
                                    <span class="badge bg-secondary">🥉 3rd</span>
                                @else
                                    <span class="badge bg-light text-dark">{{ $result->rank }}</span>
                                @endif
                            </td>
                            <td>{{ $result->student->roll_number ?? 'N/A' }}</td>
                            <td>
                                <strong>{{ $result->student->name }}</strong>
                            </td>
                            <td>{{ $result->student->class->name ?? 'N/A' }}</td>
                            <td>{{ $result->exam->name }}</td>
                            <td>{{ $result->total_marks }}</td>
                            <td><strong>{{ $result->obtained_marks }}</strong></td>
                            <td>
                                <strong>{{ number_format($result->percentage, 2) }}%</strong>
                            </td>
                            <td>
                                @php
                                    $gradeColors = [
                                        'A+' => 'success', 'A' => 'success',
                                        'B+' => 'info', 'B' => 'info',
                                        'C' => 'warning', 'D' => 'warning',
                                        'F' => 'danger'
                                    ];
                                    $gradeClass = $gradeColors[$result->grade] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $gradeClass }}">{{ $result->grade }}</span>
                            </td>
                            <td>
                                @if($result->status == 'pass')
                                    <span class="badge bg-success">Pass</span>
                                @else
                                    <span class="badge bg-danger">Fail</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('results.marksheet', ['exam' => $result->exam_id, 'student' => $result->student_id]) }}" 
                                   class="btn btn-sm btn-outline-primary" target="_blank">
                                    <i class="bi bi-file-earmark-pdf"></i> Marksheet
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-3">
                {{ $results->links() }}
            </div>
        @else
            <div class="text-center text-muted py-5">
                <i class="bi bi-inbox display-4 d-block mb-3"></i>
                <h5>No Results Found</h5>
                <p>No results have been generated with the selected filters.</p>
                <a href="{{ route('results.generate') }}" class="btn btn-primary">
                    <i class="bi bi-calculator"></i> Generate Results
                </a>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function exportToExcel() {
    window.location.href = '{{ route("results.export") }}?' + new URLSearchParams({
        exam_id: '{{ request("exam_id", "") }}',
        class_id: '{{ request("class_id", "") }}',
        status: '{{ request("status", "") }}',
        search: '{{ request("search", "") }}'
    }).toString();
}
</script>
@endpush
@endsection
