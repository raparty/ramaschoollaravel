@extends('layouts.app')

@section('title', 'Add New Book - School ERP')

@section('content')
<div class="mb-4">
    <h2>Add New Book</h2>
    <p class="text-muted">Add a new book to the library inventory</p>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('library.books.store') }}">
                    @csrf
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="book_name" class="form-label">Book Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('book_name') is-invalid @enderror" 
                                   id="book_name" 
                                   name="book_name" 
                                   value="{{ old('book_name') }}" 
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
                                   value="{{ old('book_no') }}" 
                                   placeholder="Unique book identifier"
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
                                   value="{{ old('author_name') }}" 
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
                                    <option value="{{ $category->id }}" {{ old('book_cat_id') == $category->id ? 'selected' : '' }}>
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
                                   value="{{ old('no_of_copies', 1) }}" 
                                   min="1"
                                   required>
                            @error('no_of_copies')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="book_edition" class="form-label">Edition</label>
                            <input type="text" 
                                   class="form-control @error('book_edition') is-invalid @enderror" 
                                   id="book_edition" 
                                   name="book_edition" 
                                   value="{{ old('book_edition') }}"
                                   placeholder="e.g., 1st, 2nd">
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
                                   value="{{ old('book_price') }}"
                                   step="0.01"
                                   min="0"
                                   placeholder="0.00">
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
                               value="{{ old('publisher') }}"
                               placeholder="Publisher name">
                        @error('publisher')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('library.books.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Add Book</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">💡 Tips</h6>
            </div>
            <div class="card-body">
                <ul class="mb-0 ps-3">
                    <li class="mb-2">Book Number should be unique for each book</li>
                    <li class="mb-2">Specify number of copies available in library</li>
                    <li class="mb-2">Category helps in organizing books</li>
                    <li>Edition and publisher are optional but helpful</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
