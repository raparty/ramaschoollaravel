@extends('layouts.app')

@section('title', 'Issue Book - School ERP')

@section('content')
<div class="mb-4">
    <h2>Issue Book to Student</h2>
    <p class="text-muted">Issue a library book to a student</p>
</div>

<div class="row">
    <div class="col-md-8 mx-auto">
        <!-- Student Search Card -->
        @if(!$student)
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Step 1: Select Student</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="student_search" class="form-label">Search Student</label>
                    <input type="text" 
                           class="form-control form-control-lg" 
                           id="student_search" 
                           placeholder="Enter student name or registration number..."
                           autocomplete="off">
                    <div id="search_results" class="list-group mt-2" style="display: none;"></div>
                </div>
            </div>
        </div>
        @endif

        <!-- Issue Form -->
        @if($student)
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Student Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-2"><strong>Name:</strong> {{ $student->student_name }}</p>
                        <p class="mb-2"><strong>Reg No:</strong> {{ $student->reg_no }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-2"><strong>Class:</strong> {{ $student->class?->name ?? 'N/A' }}</p>
                        <p class="mb-2"><strong>Guardian:</strong> {{ $student->guardian_name ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Step 2: Issue Book</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('library.issue.store') }}">
                    @csrf
                    <input type="hidden" name="registration_no" value="{{ $student->reg_no }}">

                    <div class="mb-3">
                        <label for="book_id" class="form-label">Select Book <span class="text-danger">*</span></label>
                        <select class="form-select @error('book_id') is-invalid @enderror" 
                                id="book_id" 
                                name="book_id" 
                                required>
                            <option value="">Select Book</option>
                            @foreach($availableBooks as $book)
                                <option value="{{ $book->id }}" {{ old('book_id') == $book->id ? 'selected' : '' }}>
                                    {{ $book->book_name }} - {{ $book->author_name }} ({{ $book->category->category_name ?? 'N/A' }}) - Available: {{ $book->available_copies }}
                                </option>
                            @endforeach
                        </select>
                        @error('book_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="issue_date" class="form-label">Issue Date <span class="text-danger">*</span></label>
                            <input type="date" 
                                   class="form-control @error('issue_date') is-invalid @enderror" 
                                   id="issue_date" 
                                   name="issue_date" 
                                   value="{{ old('issue_date', date('Y-m-d')) }}" 
                                   required>
                            @error('issue_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="due_date" class="form-label">Due Date <span class="text-danger">*</span></label>
                            <input type="date" 
                                   class="form-control @error('due_date') is-invalid @enderror" 
                                   id="due_date" 
                                   name="due_date" 
                                   value="{{ old('due_date', date('Y-m-d', strtotime('+14 days'))) }}" 
                                   required>
                            @error('due_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Standard loan period is 14 days</small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('library.books.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Issue Book</button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        <!-- Info Card -->
        <div class="card mt-4">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">💡 Issue Instructions</h6>
            </div>
            <div class="card-body">
                <ol class="mb-0 ps-3">
                    <li class="mb-2">Search and select a student</li>
                    <li class="mb-2">Select an available book from the dropdown</li>
                    <li class="mb-2">Set issue date (today by default)</li>
                    <li class="mb-2">Set due date (14 days default)</li>
                    <li>Fine of ₹5 per day will be charged for overdue returns</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Student search functionality
    let searchTimeout;
    const searchInput = document.getElementById('student_search');
    const searchResults = document.getElementById('search_results');

    @if(!$student)
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();

        if (query.length < 2) {
            searchResults.style.display = 'none';
            return;
        }

        searchTimeout = setTimeout(() => {
            fetch(`/library/search-students?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(students => {
                    if (students.length > 0) {
                        let html = '';
                        students.forEach(student => {
                            html += `
                                <a href="/library/issue?registration_no=${student.reg_no}" 
                                   class="list-group-item list-group-item-action">
                                    <strong>${student.reg_no}</strong> - ${student.student_name}
                                    <small class="text-muted">(${student.class?.name || 'N/A'})</small>
                                </a>
                            `;
                        });
                        searchResults.innerHTML = html;
                        searchResults.style.display = 'block';
                    } else {
                        searchResults.innerHTML = '<div class="list-group-item text-muted">No students found</div>';
                        searchResults.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Search error:', error);
                    searchResults.style.display = 'none';
                });
        }, 300);
    });

    // Hide results when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });
    @endif
</script>
@endpush
