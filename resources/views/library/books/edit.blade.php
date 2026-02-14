@extends('layouts.app')

@section('title', 'Edit Book - School ERP')

@section('content')
<div class="mb-4">
    <h2>Edit Book</h2>
    <p class="text-muted">Update book details for {{ $book->book_name }}</p>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('library.books.update', $book) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="book_name" class="form-label">Book Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('book_name') is-invalid @enderror" 
                                   id="book_name" 
                                   name="book_name" 
                                   value="{{ old('book_name', $book->book_name) }}" 
                                   required>
                            @error('book_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="book_no" class="form-label">Book Number <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('book_no') is-invalid @enderror" 
                                   id="book_no" 
                                   name="book_no" 
                                   value="{{ old('book_no', $book->book_no) }}" 
                                   required>
                            @error('book_no')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="author_name" class="form-label">Author Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('author_name') is-invalid @enderror" 
                                   id="author_name" 
                                   name="author_name" 
                                   value="{{ old('author_name', $book->author_name) }}" 
                                   required>
                            @error('author_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="book_cat_id" class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select @error('book_cat_id') is-invalid @enderror" 
                                    id="book_cat_id" 
                                    name="book_cat_id" 
                                    required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('book_cat_id', $book->book_cat_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->category_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('book_cat_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="no_of_copies" class="form-label">Number of Copies <span class="text-danger">*</span></label>
                            <input type="number" 
                                   class="form-control @error('no_of_copies') is-invalid @enderror" 
                                   id="no_of_copies" 
                                   name="no_of_copies" 
                                   value="{{ old('no_of_copies', $book->no_of_copies) }}" 
                                   min="1"
                                   required>
                            @error('no_of_copies')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Currently issued: {{ $book->activeIssues()->count() }}</small>
                        </div>
                        <div class="col-md-4">
                            <label for="book_edition" class="form-label">Edition</label>
                            <input type="text" 
                                   class="form-control @error('book_edition') is-invalid @enderror" 
                                   id="book_edition" 
                                   name="book_edition" 
                                   value="{{ old('book_edition', $book->book_edition) }}">
                            @error('book_edition')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="book_price" class="form-label">Price (₹)</label>
                            <input type="number" 
                                   class="form-control @error('book_price') is-invalid @enderror" 
                                   id="book_price" 
                                   name="book_price" 
                                   value="{{ old('book_price', $book->book_price) }}"
                                   step="0.01"
                                   min="0">
                            @error('book_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="publisher" class="form-label">Publisher</label>
                        <input type="text" 
                               class="form-control @error('publisher') is-invalid @enderror" 
                               id="publisher" 
                               name="publisher" 
                               value="{{ old('publisher', $book->publisher) }}">
                        @error('publisher')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('library.books.show', $book) }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Book</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Book Information</h6>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>Book No:</strong> {{ $book->book_no }}</p>
                <p class="mb-2"><strong>Total Copies:</strong> {{ $book->no_of_copies }}</p>
                <p class="mb-2"><strong>Available:</strong> 
                    <span class="badge {{ $book->available_copies > 0 ? 'bg-success' : 'bg-danger' }}">
                        {{ $book->available_copies }}
                    </span>
                </p>
                <p class="mb-0"><strong>Currently Issued:</strong> {{ $book->activeIssues()->count() }}</p>
                <hr>
                <small class="text-muted">
                    <strong>Created:</strong> {{ $book->created_at->format('d M Y') }}<br>
                    <strong>Updated:</strong> {{ $book->updated_at->format('d M Y') }}
                </small>
            </div>
        </div>

        @if($book->activeIssues()->count() > 0)
        <div class="card mt-3">
            <div class="card-header bg-warning">
                <h6 class="mb-0">⚠️ Note</h6>
            </div>
            <div class="card-body">
                <p class="mb-0 small">
                    This book has {{ $book->activeIssues()->count() }} copies currently issued.
                    Ensure the total copies is not less than issued copies.
                </p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
