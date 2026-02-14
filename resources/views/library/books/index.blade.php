@extends('layouts.app')

@section('title', 'Library Books - School ERP')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Library Books</h2>
        <p class="text-muted mb-0">Manage library book inventory</p>
    </div>
    <a href="{{ route('library.books.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add New Book
    </a>
</div>

<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('library.books.index') }}" class="row g-3">
            <div class="col-md-5">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Book name, author, or book number..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->category_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Availability</label>
                <select name="availability" class="form-select">
                    <option value="">All Books</option>
                    <option value="available" {{ request('availability') == 'available' ? 'selected' : '' }}>Available Only</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Search</button>
            </div>
        </form>
    </div>
</div>

<!-- Books Table -->
<div class="card">
    <div class="card-body">
        @if($books->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Book No</th>
                            <th>Book Name</th>
                            <th>Author</th>
                            <th>Category</th>
                            <th>Total Copies</th>
                            <th>Available</th>
                            <th>Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($books as $book)
                            <tr>
                                <td><strong>{{ $book->book_no }}</strong></td>
                                <td>{{ $book->book_name }}</td>
                                <td>{{ $book->author_name }}</td>
                                <td>{{ $book->category->category_name ?? 'N/A' }}</td>
                                <td class="text-center">{{ $book->no_of_copies }}</td>
                                <td class="text-center">
                                    @if($book->available_copies > 0)
                                        <span class="badge bg-success">{{ $book->available_copies }}</span>
                                    @else
                                        <span class="badge bg-danger">0</span>
                                    @endif
                                </td>
                                <td>{{ $book->formatted_price }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('library.books.show', $book) }}" 
                                           class="btn btn-outline-primary" 
                                           title="View">
                                            👁️
                                        </a>
                                        <a href="{{ route('library.books.edit', $book) }}" 
                                           class="btn btn-outline-warning" 
                                           title="Edit">
                                            ✏️
                                        </a>
                                        <form method="POST" 
                                              action="{{ route('library.books.destroy', $book) }}" 
                                              class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this book?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-outline-danger" 
                                                    title="Delete">
                                                🗑️
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="mt-3">
                {{ $books->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <p class="text-muted">No books found.</p>
                <a href="{{ route('library.books.create') }}" class="btn btn-primary">
                    Add First Book
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Quick Links -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">📚 Issue Book</h5>
                <p class="card-text text-muted">Issue books to students</p>
                <a href="{{ route('library.issue.create') }}" class="btn btn-outline-primary btn-sm">Go to Issue</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">↩️ Return Book</h5>
                <p class="card-text text-muted">Process book returns</p>
                <a href="{{ route('library.issue.return') }}" class="btn btn-outline-primary btn-sm">Go to Return</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">⏰ Overdue Books</h5>
                <p class="card-text text-muted">View overdue books</p>
                <a href="{{ route('library.issue.overdue') }}" class="btn btn-outline-warning btn-sm">View Overdue</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">📖 Categories</h5>
                <p class="card-text text-muted">Manage categories</p>
                <a href="#" class="btn btn-outline-secondary btn-sm">Manage Categories</a>
            </div>
        </div>
    </div>
</div>
@endsection
