@extends('layouts.app')

@section('title', $exam->name . ' - School ERP')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>{{ $exam->name }}</h2>
        <p class="text-muted mb-0">
            {{ $exam->term?->name ?? 'N/A' }} | 
            <span class="badge bg-{{ $exam->status_badge }}">{{ $exam->status_text }}</span>
        </p>
    </div>
    <div>
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
            <div class="card-header">
                <h5 class="mb-0">Exam Details</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted mb-1">Exam Name</h6>
                        <p class="mb-0">{{ $exam->name }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted mb-1">Academic Term</h6>
                        <p class="mb-0">{{ $exam->term?->name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted mb-1">Start Date</h6>
                        <p class="mb-0">{{ $exam->start_date?->format('d M, Y') ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted mb-1">End Date</h6>
                        <p class="mb-0">{{ $exam->end_date?->format('d M, Y') ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted mb-1">Duration</h6>
                        <p class="mb-0">
                            @if($exam->start_date && $exam->end_date)
                                {{ $exam->start_date->diffInDays($exam->end_date) + 1 }} days
                            @else
                                N/A
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted mb-1">Status</h6>
                        <p class="mb-0">
                            <span class="badge bg-{{ $exam->status_badge }}">{{ $exam->status_text }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Subjects Assigned -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Assigned Subjects ({{ $exam->examSubjects->count() }})</h5>
                <a href="{{ route('exams.subjects', $exam) }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-circle"></i> Manage Subjects
                </a>
            </div>
            <div class="card-body">
                @if($exam->examSubjects->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Subject</th>
                                    <th>Class</th>
                                    <th>Max Marks</th>
                                    <th>Pass Marks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($exam->examSubjects as $examSubject)
                                <tr>
                                    <td><strong>{{ $examSubject->subject?->name ?? 'N/A' }}</strong></td>
                                    <td>{{ $examSubject->classModel?->name ?? 'N/A' }}</td>
                                    <td>{{ $examSubject->max_marks }}</td>
                                    <td>{{ $examSubject->pass_marks }}</td>
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
                        <small class="text-muted">Subjects Assigned</small>
                        <strong>{{ $exam->examSubjects->count() }}</strong>
                    </div>
                    <div class="progress" style="height: 5px;">
                        <div class="progress-bar bg-primary" style="width: 100%"></div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="text-muted">Academic Term</small>
                        <strong>{{ $exam->term?->name ?? 'N/A' }}</strong>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="text-muted">Created</small>
                        <strong>{{ $exam->created_at?->format('d M, Y') ?? 'N/A' }}</strong>
                    </div>
                </div>
                
                <div>
                    <div class="d-flex justify-content-between mb-1">
                        <small class="text-muted">Last Updated</small>
                        <strong>{{ $exam->updated_at?->format('d M, Y') ?? 'N/A' }}</strong>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-lightning"></i> Quick Actions</h5>
            </div>
            <div class="card-body">
                <a href="{{ route('exams.subjects', $exam) }}" class="btn btn-outline-primary w-100 mb-2">
                    <i class="bi bi-journals"></i> Manage Subjects
                </a>
                <a href="{{ route('exams.edit', $exam) }}" class="btn btn-outline-warning w-100 mb-2">
                    <i class="bi bi-pencil"></i> Edit Exam
                </a>
                <form action="{{ route('exams.destroy', $exam) }}" method="POST" class="d-inline w-100"
                      onsubmit="return confirm('Are you sure you want to delete this exam?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger w-100">
                        <i class="bi bi-trash"></i> Delete Exam
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
