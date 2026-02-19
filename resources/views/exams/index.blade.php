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
        <div class="col-md-4">
            <div class="card border-primary">
                <div class="card-body">
                    <h6 class="text-muted">Total Exams</h6>
                    <h3 class="mb-0">{{ $exams->total() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-success">
                <div class="card-body">
                    <h6 class="text-muted">Upcoming</h6>
                    <h3 class="mb-0 text-success">{{ $exams->where('start_date', '>', now())->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
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
                    <div class="col-md-4">
                        <label class="form-label">Academic Term</label>
                        <select name="term_id" class="form-select">
                            <option value="">All Terms</option>
                            @foreach($terms as $term)
                                <option value="{{ $term->id }}" {{ request('term_id') == $term->id ? 'selected' : '' }}>
                                    {{ $term->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control" 
                               value="{{ request('search') }}" placeholder="Search exam name...">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="bi bi-search"></i> Filter
                            </button>
                            <a href="{{ route('exams.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-clockwise"></i>
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
                    <div class="card h-100">
                        <div class="card-header bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">{{ $exam->name }}</h5>
                                <span class="badge bg-{{ $exam->status_badge }}">
                                    {{ $exam->status_text }}
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="mb-2">
                                <strong>Term:</strong> {{ $exam->term?->name ?? 'N/A' }}
                            </p>
                            <p class="mb-2">
                                <strong>Duration:</strong> 
                                {{ $exam->start_date?->format('d M Y') ?? 'N/A' }} - {{ $exam->end_date?->format('d M Y') ?? 'N/A' }}
                            </p>
                            <p class="mb-0">
                                <strong>Subjects:</strong> {{ $exam->examSubjects->count() }}
                            </p>
                        </div>
                        <div class="card-footer bg-white">
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('exams.show', $exam) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                <a href="{{ route('exams.edit', $exam) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <a href="{{ route('exams.subjects', $exam) }}" class="btn btn-sm btn-secondary">
                                    <i class="bi bi-journals"></i> Subjects
                                </a>
                                <button type="button" class="btn btn-sm btn-danger" 
                                        onclick="confirmDelete({{ $exam->id }})">
                                    <i class="bi bi-trash"></i>
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
            {{ $exams->appends(request()->query())->links() }}
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-clipboard2 display-4 text-muted mb-3"></i>
                <h4>No Exams Found</h4>
                <p class="text-muted">Get started by creating your first exam.</p>
                <a href="{{ route('exams.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus"></i> Create Exam
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
