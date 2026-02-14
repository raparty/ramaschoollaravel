@extends('layouts.app')

@section('title', 'Book Details - School ERP')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Book Details</h2>
        <p class="text-muted mb-0">{{ $book->book_name }}</p>
    </div>
    <div class="btn-group">
        <a href="{{ route('library.books.edit', $book) }}" class="btn btn-warning">
            ✏️ Edit
        </a>
        <form method="POST" 
              action="{{ route('library.books.destroy', $book) }}" 
              class="d-inline"
              onsubmit="return confirm('Are you sure you want to delete this book?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                🗑️ Delete
            </button>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Book Information Card -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Book Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tbody>
                        <tr>
                            <th width="180">Book Name:</th>
                            <td><strong>{{ $book->book_name }}</strong></td>
                        </tr>
                        <tr>
                            <th>Book Number:</th>
                            <td>{{ $book->book_no }}</td>
                        </tr>
                        <tr>
                            <th>Author:</th>
                            <td>{{ $book->author_name }}</td>
                        </tr>
                        <tr>
                            <th>Category:</th>
                            <td>{{ $book->category->category_name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Edition:</th>
                            <td>{{ $book->book_edition ?? 'Not specified' }}</td>
                        </tr>
                        <tr>
                            <th>Publisher:</th>
                            <td>{{ $book->publisher ?? 'Not specified' }}</td>
                        </tr>
                        <tr>
                            <th>Price:</th>
                            <td>{{ $book->formatted_price }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Issue History Card -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Issue History</h5>
            </div>
            <div class="card-body">
                @if($book->issues->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Reg No</th>
                                    <th>Issue Date</th>
                                    <th>Due Date</th>
                                    <th>Return Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($book->issues->take(10) as $issue)
                                    <tr>
                                        <td>{{ $issue->student->student_name ?? 'N/A' }}</td>
                                        <td>{{ $issue->registration_no }}</td>
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
                                                <span class="badge bg-success">Returned</span>
                                            @elseif($issue->isOverdue())
                                                <span class="badge bg-danger">Overdue</span>
                                            @else
                                                <span class="badge bg-info">Issued</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($book->issues->count() > 10)
                        <p class="text-muted mb-0 mt-2 small">
                            Showing 10 of {{ $book->issues->count() }} total issues
                        </p>
                    @endif
                @else
                    <p class="text-muted mb-0">This book has never been issued.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Availability Card -->
        <div class="card mb-3">
            <div class="card-header {{ $book->available_copies > 0 ? 'bg-success' : 'bg-danger' }} text-white">
                <h6 class="mb-0">Availability Status</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td>Total Copies:</td>
                        <td class="text-end"><strong>{{ $book->no_of_copies }}</strong></td>
                    </tr>
                    <tr>
                        <td>Currently Issued:</td>
                        <td class="text-end text-warning">{{ $book->activeIssues()->count() }}</td>
                    </tr>
                    <tr class="border-top">
                        <td><strong>Available:</strong></td>
                        <td class="text-end">
                            <strong class="{{ $book->available_copies > 0 ? 'text-success' : 'text-danger' }}">
                                {{ $book->available_copies }}
                            </strong>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if($book->isAvailable())
                        <a href="{{ route('library.issue.create') }}?book_id={{ $book->id }}" class="btn btn-outline-success">
                            📚 Issue This Book
                        </a>
                    @else
                        <button class="btn btn-outline-secondary" disabled>
                            📚 No Copies Available
                        </button>
                    @endif
                    <a href="{{ route('library.books.edit', $book) }}" class="btn btn-outline-primary">
                        ✏️ Edit Details
                    </a>
                    <a href="{{ route('library.books.index') }}" class="btn btn-outline-secondary">
                        📋 Back to List
                    </a>
                </div>
            </div>
        </div>

        <!-- Record Info -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Record Information</h6>
            </div>
            <div class="card-body">
                <small class="text-muted">
                    <strong>Created:</strong> {{ $book->created_at->format('d M Y, h:i A') }}<br>
                    <strong>Last Updated:</strong> {{ $book->updated_at->format('d M Y, h:i A') }}
                </small>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="{{ route('library.books.index') }}" class="btn btn-secondary">
        ← Back to Books List
    </a>
</div>
@endsection
