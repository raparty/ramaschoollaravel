@extends('layouts.app')

@section('title', 'Return Book - School ERP')

@section('content')
<div class="mb-4">
    <h2>Return Book</h2>
    <p class="text-muted">Process book returns and collect fines</p>
</div>

<div class="row">
    <div class="col-md-10 mx-auto">
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

        <!-- Active Issues List -->
        @if($student)
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Student Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-2"><strong>Name:</strong> {{ $student->student_name }}</p>
                        <p class="mb-0"><strong>Reg No:</strong> {{ $student->reg_no }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-2"><strong>Class:</strong> {{ $student->class->name ?? 'N/A' }}</p>
                        <p class="mb-0"><strong>Active Issues:</strong> {{ $activeIssues->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        @if($activeIssues->count() > 0)
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Step 2: Select Book to Return</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Book</th>
                                <th>Author</th>
                                <th>Issue Date</th>
                                <th>Due Date</th>
                                <th>Days</th>
                                <th>Fine</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activeIssues as $issue)
                                <tr>
                                    <td>
                                        <strong>{{ $issue->book->book_name }}</strong><br>
                                        <small class="text-muted">{{ $issue->book->book_no }}</small>
                                    </td>
                                    <td>{{ $issue->book->author_name }}</td>
                                    <td>{{ $issue->issue_date->format('d M Y') }}</td>
                                    <td>{{ $issue->due_date->format('d M Y') }}</td>
                                    <td>
                                        @if($issue->isOverdue())
                                            <span class="badge bg-danger">{{ $issue->days_overdue }} days overdue</span>
                                        @else
                                            <span class="badge bg-success">On time</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($issue->isOverdue())
                                            <strong class="text-danger">₹{{ number_format($issue->calculateFine(), 2) }}</strong>
                                        @else
                                            <span class="text-muted">₹0.00</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" 
                                                class="btn btn-sm btn-primary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#returnModal{{ $issue->id }}">
                                            Return Book
                                        </button>
                                    </td>
                                </tr>

                                <!-- Return Modal -->
                                <div class="modal fade" id="returnModal{{ $issue->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST" action="{{ route('library.issue.process-return') }}">
                                                @csrf
                                                <input type="hidden" name="issue_id" value="{{ $issue->id }}">
                                                
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Return Book: {{ $issue->book->book_name }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="return_date{{ $issue->id }}" class="form-label">Return Date <span class="text-danger">*</span></label>
                                                        <input type="date" 
                                                               class="form-control" 
                                                               id="return_date{{ $issue->id }}" 
                                                               name="return_date" 
                                                               value="{{ date('Y-m-d') }}" 
                                                               required>
                                                    </div>

                                                    @if($issue->isOverdue())
                                                    <div class="alert alert-warning">
                                                        <strong>Overdue Fine:</strong> ₹{{ number_format($issue->calculateFine(), 2) }}<br>
                                                        <small>{{ $issue->days_overdue }} days × ₹5 per day</small>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="fine_amount{{ $issue->id }}" class="form-label">Fine Amount (₹)</label>
                                                        <input type="number" 
                                                               class="form-control" 
                                                               id="fine_amount{{ $issue->id }}" 
                                                               name="fine_amount" 
                                                               value="{{ $issue->calculateFine() }}" 
                                                               step="0.01"
                                                               min="0">
                                                        <small class="text-muted">Adjust if needed or enter 0 if waived</small>
                                                    </div>
                                                    @else
                                                    <div class="alert alert-success">
                                                        <strong>No Fine</strong> - Book returned on time!
                                                    </div>
                                                    @endif
                                                </div>
                                                
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">Process Return</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @else
        <div class="alert alert-info">
            <h5>No Active Issues</h5>
            <p class="mb-0">This student has no books currently issued.</p>
        </div>
        @endif
        @endif

        <!-- Info Card -->
        <div class="card mt-4">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">💡 Return Instructions</h6>
            </div>
            <div class="card-body">
                <ol class="mb-0 ps-3">
                    <li class="mb-2">Search and select a student</li>
                    <li class="mb-2">View the list of books currently issued</li>
                    <li class="mb-2">Click "Return Book" for the book being returned</li>
                    <li class="mb-2">Verify the return date (today by default)</li>
                    <li>If overdue, collect the fine amount or waive if applicable</li>
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
                                <a href="/library/return?registration_no=${student.reg_no}" 
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
