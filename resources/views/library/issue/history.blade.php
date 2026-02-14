@extends('layouts.app')

@section('title', 'Student Issue History - School ERP')

@section('content')
<div class="mb-4">
    <h2>Student Issue History</h2>
    <p class="text-muted">Complete book issue and return history for {{ $student->student_name }}</p>
</div>

<div class="row">
    <div class="col-md-9">
        <!-- Student Info Card -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Student Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <p class="mb-2"><strong>Name:</strong> {{ $student->student_name }}</p>
                        <p class="mb-0"><strong>Reg No:</strong> {{ $student->reg_no }}</p>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-2"><strong>Class:</strong> {{ $student->class->name ?? 'N/A' }}</p>
                        <p class="mb-0"><strong>Guardian:</strong> {{ $student->guardian_name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-2"><strong>Phone:</strong> {{ $student->guardian_phone ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Issue History Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Issue History</h5>
            </div>
            <div class="card-body">
                @if($issues->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Book</th>
                                    <th>Category</th>
                                    <th>Issue Date</th>
                                    <th>Due Date</th>
                                    <th>Return Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($issues as $issue)
                                    <tr>
                                        <td>
                                            <strong>{{ $issue->book->book_name }}</strong><br>
                                            <small class="text-muted">{{ $issue->book->author_name }}</small>
                                        </td>
                                        <td>{{ $issue->book->category->category_name ?? 'N/A' }}</td>
                                        <td>{{ $issue->issue_date->format('d M Y') }}</td>
                                        <td>{{ $issue->due_date->format('d M Y') }}</td>
                                        <td>
                                            @if($issue->return_date)
                                                {{ $issue->return_date->format('d M Y') }}
                                            @else
                                                <span class="badge bg-warning">Not Returned</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($issue->return_date)
                                                @if($issue->return_date->gt($issue->due_date))
                                                    <span class="badge bg-danger">Returned Late</span>
                                                @else
                                                    <span class="badge bg-success">Returned On Time</span>
                                                @endif
                                            @elseif($issue->isOverdue())
                                                <span class="badge bg-danger">Overdue ({{ $issue->days_overdue }} days)</span>
                                            @else
                                                <span class="badge bg-info">Issued</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $issues->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <p class="text-muted">No issue history found for this student.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <!-- Statistics Card -->
        <div class="card mb-3">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">Statistics</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td>Total Issued:</td>
                        <td class="text-end"><strong>{{ $stats['total_issued'] }}</strong></td>
                    </tr>
                    <tr>
                        <td>Currently Issued:</td>
                        <td class="text-end">
                            @if($stats['currently_issued'] > 0)
                                <span class="badge bg-warning">{{ $stats['currently_issued'] }}</span>
                            @else
                                <span class="badge bg-success">{{ $stats['currently_issued'] }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Returned:</td>
                        <td class="text-end text-success">{{ $stats['total_returned'] }}</td>
                    </tr>
                    <tr>
                        <td>Overdue:</td>
                        <td class="text-end">
                            @if($stats['overdue'] > 0)
                                <span class="badge bg-danger">{{ $stats['overdue'] }}</span>
                            @else
                                <span class="badge bg-success">0</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('library.issue.create', ['registration_no' => $student->reg_no]) }}" 
                       class="btn btn-outline-primary btn-sm">
                        📚 Issue Book
                    </a>
                    @if($stats['currently_issued'] > 0)
                    <a href="{{ route('library.issue.return', ['registration_no' => $student->reg_no]) }}" 
                       class="btn btn-outline-warning btn-sm">
                        ↩️ Return Book
                    </a>
                    @endif
                    <a href="{{ route('admissions.show', $student) }}" 
                       class="btn btn-outline-info btn-sm">
                        👁️ Student Profile
                    </a>
                    <a href="{{ route('library.books.index') }}" 
                       class="btn btn-outline-secondary btn-sm">
                        📋 Back to Books
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
