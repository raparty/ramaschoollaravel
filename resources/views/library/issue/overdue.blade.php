@extends('layouts.app')

@section('title', 'Overdue Books - School ERP')

@section('content')
<div class="mb-4">
    <h2>Overdue Books Report</h2>
    <p class="text-muted">List of all books that are overdue for return</p>
</div>

<!-- Summary Card -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row text-center">
            <div class="col-md-3">
                <h3 class="text-danger mb-0">{{ $overdueIssues->total() }}</h3>
                <p class="text-muted mb-0">Total Overdue</p>
            </div>
            <div class="col-md-3">
                <h3 class="text-warning mb-0">₹{{ number_format($overdueIssues->sum(function($issue) { return $issue->calculateFine(); }), 2) }}</h3>
                <p class="text-muted mb-0">Total Fines Due</p>
            </div>
            <div class="col-md-3">
                <h3 class="text-info mb-0">{{ $overdueIssues->unique('registration_no')->count() }}</h3>
                <p class="text-muted mb-0">Students Affected</p>
            </div>
            <div class="col-md-3">
                <h3 class="text-primary mb-0">{{ $overdueIssues->unique('book_id')->count() }}</h3>
                <p class="text-muted mb-0">Different Books</p>
            </div>
        </div>
    </div>
</div>

<!-- Overdue Books Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Overdue Books List</h5>
    </div>
    <div class="card-body">
        @if($overdueIssues->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Class</th>
                            <th>Book</th>
                            <th>Issue Date</th>
                            <th>Due Date</th>
                            <th>Days Overdue</th>
                            <th>Fine Amount</th>
                            <th>Contact</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($overdueIssues as $issue)
                            <tr class="{{ $issue->days_overdue > 30 ? 'table-danger' : ($issue->days_overdue > 14 ? 'table-warning' : '') }}">
                                <td>
                                    <strong>{{ $issue->student->student_name ?? 'N/A' }}</strong><br>
                                    <small class="text-muted">{{ $issue->registration_no }}</small>
                                </td>
                                <td>{{ $issue->student->class->name ?? 'N/A' }}</td>
                                <td>
                                    <strong>{{ $issue->book->book_name }}</strong><br>
                                    <small class="text-muted">{{ $issue->book->author_name }}</small>
                                </td>
                                <td>{{ $issue->issue_date->format('d M Y') }}</td>
                                <td>{{ $issue->due_date->format('d M Y') }}</td>
                                <td>
                                    <span class="badge {{ $issue->days_overdue > 30 ? 'bg-danger' : ($issue->days_overdue > 14 ? 'bg-warning' : 'bg-secondary') }}">
                                        {{ $issue->days_overdue }} days
                                    </span>
                                </td>
                                <td>
                                    <strong class="text-danger">₹{{ number_format($issue->calculateFine(), 2) }}</strong>
                                </td>
                                <td>
                                    @if($issue->student && $issue->student->guardian_phone)
                                        <a href="tel:{{ $issue->student->guardian_phone }}" class="btn btn-sm btn-outline-primary">
                                            📞 {{ $issue->student->guardian_phone }}
                                        </a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('library.issue.return', ['registration_no' => $issue->registration_no]) }}" 
                                       class="btn btn-sm btn-warning">
                                        Return
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="mt-3">
                {{ $overdueIssues->links() }}
            </div>

            <!-- Export Options -->
            <div class="mt-3">
                <button class="btn btn-outline-success" onclick="window.print()">
                    🖨️ Print Report
                </button>
                <button class="btn btn-outline-primary" onclick="exportToCSV()">
                    📥 Export to CSV
                </button>
            </div>
        @else
            <div class="text-center py-5">
                <h4 class="text-success">✅ No Overdue Books!</h4>
                <p class="text-muted">All books have been returned on time.</p>
            </div>
        @endif
    </div>
</div>

<!-- Legend -->
<div class="card mt-4">
    <div class="card-header bg-info text-white">
        <h6 class="mb-0">💡 Legend</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <span class="badge bg-secondary">1-14 days</span> - Recent overdue
            </div>
            <div class="col-md-4">
                <span class="badge bg-warning">15-30 days</span> - Moderate overdue
            </div>
            <div class="col-md-4">
                <span class="badge bg-danger">30+ days</span> - Severely overdue
            </div>
        </div>
        <hr>
        <p class="mb-0"><strong>Fine Rate:</strong> ₹5 per day overdue</p>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function exportToCSV() {
        let csv = 'Student Name,Reg No,Class,Book Name,Author,Issue Date,Due Date,Days Overdue,Fine Amount,Guardian Phone\n';
        
        @foreach($overdueIssues as $issue)
            csv += '"{{ $issue->student->student_name ?? 'N/A' }}",' +
                   '"{{ $issue->registration_no }}",' +
                   '"{{ $issue->student->class->name ?? 'N/A' }}",' +
                   '"{{ $issue->book->book_name }}",' +
                   '"{{ $issue->book->author_name }}",' +
                   '"{{ $issue->issue_date->format('Y-m-d') }}",' +
                   '"{{ $issue->due_date->format('Y-m-d') }}",' +
                   '"{{ $issue->days_overdue }}",' +
                   '"{{ $issue->calculateFine() }}",' +
                   '"{{ $issue->student->guardian_phone ?? '' }}"\n';
        @endforeach
        
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'overdue-books-' + new Date().toISOString().split('T')[0] + '.csv';
        a.click();
    }
</script>
@endpush
