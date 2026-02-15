@extends('layouts.app')

@section('title', $exam->name . ' - School ERP')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>{{ $exam->name }}</h2>
        <p class="text-muted mb-0">
            {{ $exam->class?->name ?? 'N/A' }} | {{ $exam->academicYear?->name ?? 'N/A' }} | 
            @if($exam->status == 'draft')
                <span class="badge bg-secondary">Draft</span>
            @elseif($exam->status == 'scheduled')
                <span class="badge bg-info">Scheduled</span>
            @elseif($exam->status == 'ongoing')
                <span class="badge bg-warning">Ongoing</span>
            @elseif($exam->status == 'completed')
                <span class="badge bg-success">Completed</span>
            @endif
        </p>
    </div>
    <div>
        <a href="{{ route('exams.timetable', $exam) }}" class="btn btn-info me-2" target="_blank">
            <i class="bi bi-calendar3"></i> Timetable
        </a>
        <a href="{{ route('exams.edit', $exam) }}" class="btn btn-warning me-2">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="{{ route('exams.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="row">
    <!-- Left Column -->
    <div class="col-lg-8">
        <!-- Basic Information -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Exam Details</h5>
                @if($exam->is_published)
                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> Published</span>
                @else
                    <span class="badge bg-warning"><i class="bi bi-eye-slash"></i> Unpublished</span>
                @endif
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted mb-1">Exam Type</h6>
                        <p class="mb-0">{{ ucfirst($exam->type) }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted mb-1">Academic Year</h6>
                        <p class="mb-0">{{ $exam->academicYear->name }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted mb-1">Class</h6>
                        <p class="mb-0">{{ $exam->class?->name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted mb-1">Duration</h6>
                        <p class="mb-0">{{ $exam->start_date?->format('d M, Y') ?? 'N/A' }} - {{ $exam->end_date?->format('d M, Y') ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted mb-1">Total Marks</h6>
                        <p class="mb-0">{{ $exam->total_marks }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted mb-1">Passing Marks</h6>
                        <p class="mb-0">{{ $exam->passing_marks }}</p>
                    </div>
                    @if($exam->grace_marks)
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted mb-1">Grace Marks</h6>
                        <p class="mb-0">{{ $exam->grace_marks }}</p>
                    </div>
                    @endif
                    @if($exam->description)
                    <div class="col-12">
                        <h6 class="text-muted mb-1">Description</h6>
                        <p class="mb-0">{{ $exam->description }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Subjects Assigned -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Assigned Subjects ({{ $exam->subjects->count() }})</h5>
                <a href="{{ route('exams.subjects', $exam) }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-circle"></i> Manage Subjects
                </a>
            </div>
            <div class="card-body">
                @if($exam->subjects->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Subject</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Duration</th>
                                    <th>Max Marks</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($exam->subjects as $subject)
                                <tr>
                                    <td><strong>{{ $subject->name }}</strong></td>
                                    <td>{{ $subject->pivot->exam_date ? \Carbon\Carbon::parse($subject->pivot->exam_date)->format('d M, Y') : '-' }}</td>
                                    <td>{{ $subject->pivot->start_time ?? '-' }}</td>
                                    <td>{{ $subject->pivot->duration ? $subject->pivot->duration . ' mins' : '-' }}</td>
                                    <td>{{ $subject->pivot->max_marks }}</td>
                                    <td>
                                        @if($subject->pivot->exam_date && \Carbon\Carbon::parse($subject->pivot->exam_date) < now())
                                            <span class="badge bg-success">Completed</span>
                                        @elseif($subject->pivot->exam_date && \Carbon\Carbon::parse($subject->pivot->exam_date)->isToday())
                                            <span class="badge bg-warning">Today</span>
                                        @else
                                            <span class="badge bg-info">Upcoming</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-inbox display-4 d-block mb-3"></i>
                        <p>No subjects assigned yet</p>
                        <a href="{{ route('exams.subjects', $exam) }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Assign Subjects
                        </a>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Recent Marks Entry -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Marks Entry</h5>
                <a href="{{ route('marks.index') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-pencil-square"></i> Enter Marks
                </a>
            </div>
            <div class="card-body">
                @if($exam->marks->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Subject</th>
                                    <th>Marks</th>
                                    <th>Entered By</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($exam->marks->take(5) as $mark)
                                <tr>
                                    <td>{{ $mark->student->name }}</td>
                                    <td>{{ $mark->subject->name }}</td>
                                    <td>
                                        <strong>{{ $mark->marks_obtained }}/{{ $mark->total_marks }}</strong>
                                        @if($mark->marks_obtained >= $exam->passing_marks)
                                            <span class="badge bg-success">Pass</span>
                                        @else
                                            <span class="badge bg-danger">Fail</span>
                                        @endif
                                    </td>
                                    <td>{{ $mark->enteredBy->name ?? 'N/A' }}</td>
                                    <td>{{ $mark->created_at->format('d M, Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($exam->marks->count() > 5)
                        <div class="text-center mt-3">
                            <a href="{{ route('marks.index', ['exam_id' => $exam->id]) }}" class="btn btn-sm btn-outline-primary">
                                View All Marks
                            </a>
                        </div>
                    @endif
                @else
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-inbox display-6 d-block mb-2"></i>
                        <p class="mb-0">No marks entered yet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Right Column -->
    <div class="col-lg-4">
        <!-- Statistics -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Statistics</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="text-muted">Subjects</small>
                        <strong>{{ $exam->subjects->count() }}</strong>
                    </div>
                    <div class="progress" style="height: 5px;">
                        <div class="progress-bar bg-primary" style="width: 100%"></div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="text-muted">Students Enrolled</small>
                        <strong>{{ $exam->class->students->count() ?? 0 }}</strong>
                    </div>
                    <div class="progress" style="height: 5px;">
                        <div class="progress-bar bg-info" style="width: 100%"></div>
                    </div>
                </div>
                
                @php
                    $totalPossibleMarks = $exam->subjects->count() * ($exam->class->students->count() ?? 0);
                    $marksEnteredCount = $exam->marks->count();
                    $marksProgress = $totalPossibleMarks > 0 ? ($marksEnteredCount / $totalPossibleMarks) * 100 : 0;
                @endphp
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="text-muted">Marks Entry Progress</small>
                        <strong>{{ number_format($marksProgress, 1) }}%</strong>
                    </div>
                    <div class="progress" style="height: 5px;">
                        <div class="progress-bar bg-success" style="width: {{ $marksProgress }}%"></div>
                    </div>
                    <small class="text-muted">{{ $marksEnteredCount }} / {{ $totalPossibleMarks }}</small>
                </div>
                
                <div>
                    <div class="d-flex justify-content-between mb-1">
                        <small class="text-muted">Results Generated</small>
                        <strong>{{ $exam->results->count() }}</strong>
                    </div>
                    <div class="progress" style="height: 5px;">
                        <div class="progress-bar bg-warning" style="width: {{ $exam->class->students->count() > 0 ? ($exam->results->count() / $exam->class->students->count()) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-lightning"></i> Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('exams.subjects', $exam) }}" class="btn btn-outline-primary">
                        <i class="bi bi-book"></i> Manage Subjects
                    </a>
                    <a href="{{ route('exams.timetable', $exam) }}" class="btn btn-outline-info" target="_blank">
                        <i class="bi bi-calendar3"></i> View Timetable
                    </a>
                    <a href="{{ route('marks.entry', ['exam_id' => $exam->id]) }}" class="btn btn-outline-success">
                        <i class="bi bi-pencil-square"></i> Enter Marks
                    </a>
                    <a href="{{ route('results.generate', ['exam_id' => $exam->id]) }}" class="btn btn-outline-warning">
                        <i class="bi bi-calculator"></i> Generate Results
                    </a>
                    <a href="{{ route('results.index', ['exam_id' => $exam->id]) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-file-text"></i> View Results
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Publish Settings -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-gear"></i> Publication</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted">Exam Visibility</label>
                    <div>
                        @if($exam->is_published)
                            <span class="badge bg-success">
                                <i class="bi bi-eye"></i> Published
                            </span>
                        @else
                            <span class="badge bg-warning">
                                <i class="bi bi-eye-slash"></i> Unpublished
                            </span>
                        @endif
                    </div>
                </div>
                
                <div>
                    <label class="text-muted">Results Visibility</label>
                    <div>
                        @if($exam->is_results_published)
                            <span class="badge bg-success">
                                <i class="bi bi-eye"></i> Published
                            </span>
                        @else
                            <span class="badge bg-warning">
                                <i class="bi bi-eye-slash"></i> Unpublished
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Delete Exam -->
        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Danger Zone</h5>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">
                    Deleting this exam will remove all associated data including marks and results. This action cannot be undone.
                </p>
                <form action="{{ route('exams.destroy', $exam) }}" method="POST" 
                      onsubmit="return confirm('Are you sure you want to delete this exam? All marks and results will be permanently deleted.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="bi bi-trash"></i> Delete Exam
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
