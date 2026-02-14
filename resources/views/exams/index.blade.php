@extends('layouts.app')

@section('title', 'Exams')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Examinations</h2>
        <a href="{{ route('exams.create') }}" class="btn btn-primary">
            <i class="bi bi-plus"></i> Create Exam
        </a>
    </div>

    {{-- Summary Statistics --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body">
                    <h6 class="text-muted">Total Exams</h6>
                    <h3 class="mb-0">{{ $exams->total() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body">
                    <h6 class="text-muted">Published</h6>
                    <h3 class="mb-0 text-success">{{ $exams->where('is_published', true)->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body">
                    <h6 class="text-muted">Unpublished</h6>
                    <h3 class="mb-0 text-warning">{{ $exams->where('is_published', false)->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body">
                    <h6 class="text-muted">Ongoing</h6>
                    <h3 class="mb-0 text-info">{{ $exams->where('start_date', '<=', now())->where('end_date', '>=', now())->count() }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('exams.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Session</label>
                        <input type="text" name="session" class="form-control" 
                               value="{{ request('session') }}" placeholder="e.g., 2023-2024">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Class</label>
                        <select name="class_id" class="form-select">
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
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                            <option value="unpublished" {{ request('status') == 'unpublished' ? 'selected' : '' }}>Unpublished</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="fas fa-search"></i> Filter
                            </button>
                            <a href="{{ route('exams.index') }}" class="btn btn-secondary">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Exam List --}}
    @if($exams->count() > 0)
        <div class="row">
            @foreach($exams as $exam)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 {{ $exam->is_published ? 'border-success' : 'border-warning' }}">
                        <div class="card-header bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">{{ $exam->name }}</h5>
                                <span class="badge {{ $exam->is_published ? 'bg-success' : 'bg-warning' }}">
                                    {{ $exam->is_published ? 'Published' : 'Unpublished' }}
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="mb-2">
                                <strong>Class:</strong> {{ $exam->class->name ?? 'N/A' }}
                            </p>
                            <p class="mb-2">
                                <strong>Session:</strong> {{ $exam->session }}
                            </p>
                            <p class="mb-2">
                                <strong>Duration:</strong> 
                                {{ $exam->start_date->format('d M Y') }} - {{ $exam->end_date->format('d M Y') }}
                            </p>
                            <p class="mb-2">
                                <strong>Total Marks:</strong> {{ $exam->total_marks }}
                            </p>
                            <p class="mb-0">
                                <strong>Pass Marks:</strong> {{ $exam->pass_marks }}
                            </p>
                        </div>
                        <div class="card-footer bg-white">
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('exams.show', $exam) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="{{ route('exams.edit', $exam) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="{{ route('exams.timetable', $exam) }}" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-calendar"></i> Timetable
                                </a>
                                <form action="{{ route('exams.toggle-publish', $exam) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $exam->is_published ? 'btn-warning' : 'btn-success' }}">
                                        <i class="fas fa-{{ $exam->is_published ? 'eye-slash' : 'check' }}"></i>
                                        {{ $exam->is_published ? 'Unpublish' : 'Publish' }}
                                    </button>
                                </form>
                                <button type="button" class="btn btn-sm btn-danger" 
                                        onclick="confirmDelete({{ $exam->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <form id="delete-form-{{ $exam->id }}" 
                                      action="{{ route('exams.destroy', $exam) }}" 
                                      method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-center">
            {{ $exams->links() }}
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-clipboard-list fa-4x text-muted mb-3"></i>
                <h4>No Exams Found</h4>
                <p class="text-muted">Get started by creating your first exam.</p>
                <a href="{{ route('exams.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create Exam
                </a>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
function confirmDelete(examId) {
    if (confirm('Are you sure you want to delete this exam? This action cannot be undone.')) {
        document.getElementById('delete-form-' + examId).submit();
    }
}
</script>
@endpush
@endsection
