<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * LibraryController
 * 
 * Handles library book management operations
 * Converts: library_book_manager.php, library_add_book.php, library_edit_book.php
 */
class LibraryController extends Controller
{
    /**
     * Display a listing of books.
     * Converts: library_book_manager.php
     */
    public function index(Request $request)
    {
        // Check permission
        $this->authorize('view-library');

        $query = Book::with('category');

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->byCategory($request->category_id);
        }

        // Filter by availability
        if ($request->filled('availability')) {
            if ($request->availability === 'available') {
                $query->available();
            }
        }

        // Order by book name
        $query->orderBy('book_name');

        // Paginate results
        $books = $query->paginate(20)->withQueryString();

        // Get categories for filter dropdown
        $categories = BookCategory::ordered()->get();

        return view('library.books.index', compact('books', 'categories'));
    }

    /**
     * Show the form for creating a new book.
     * Converts: library_add_book.php (form)
     */
    public function create()
    {
        // Check permission
        $this->authorize('create-library');

        // Get all categories for dropdown
        $categories = BookCategory::ordered()->get();

        return view('library.books.create', compact('categories'));
    }

    /**
     * Store a newly created book in database.
     * Converts: library_add_book.php (processing)
     */
    public function store(Request $request)
    {
        // Check permission
        $this->authorize('create-library');

        $validated = $request->validate([
            'book_name' => 'required|string|max:255',
            'book_no' => 'required|string|max:50|unique:books,book_no',
            'author_name' => 'required|string|max:255',
            'book_cat_id' => 'required|exists:book_category,id',
            'no_of_copies' => 'required|integer|min:1',
            'book_edition' => 'nullable|string|max:100',
            'book_price' => 'nullable|numeric|min:0',
            'publisher' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        
        try {
            // Create book record
            $book = Book::create($validated);

            DB::commit();

            return redirect()
                ->route('library.books.index')
                ->with('success', 'Book added successfully! Book No: ' . $book->book_no);

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to add book: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified book.
     */
    public function show(Book $book)
    {
        // Check permission
        $this->authorize('view-library');

        // Load relationships
        $book->load(['category', 'issues.student']);

        return view('library.books.show', compact('book'));
    }

    /**
     * Show the form for editing the specified book.
     * Converts: library_edit_book.php
     */
    public function edit(Book $book)
    {
        // Check permission
        $this->authorize('edit-library');

        // Get all categories for dropdown
        $categories = BookCategory::ordered()->get();

        return view('library.books.edit', compact('book', 'categories'));
    }

    /**
     * Update the specified book in database.
     */
    public function update(Request $request, Book $book)
    {
        // Check permission
        $this->authorize('edit-library');

        $validated = $request->validate([
            'book_name' => 'required|string|max:255',
            'book_no' => 'required|string|max:50|unique:books,book_no,' . $book->id,
            'author_name' => 'required|string|max:255',
            'book_cat_id' => 'required|exists:book_category,id',
            'no_of_copies' => 'required|integer|min:1',
            'book_edition' => 'nullable|string|max:100',
            'book_price' => 'nullable|numeric|min:0',
            'publisher' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        
        try {
            // Update book record
            $book->update($validated);

            DB::commit();

            return redirect()
                ->route('library.books.show', $book)
                ->with('success', 'Book updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update book: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified book from database.
     * Converts: library_delete_book.php
     */
    public function destroy(Book $book)
    {
        // Check permission
        $this->authorize('delete-library');

        // Check if book has active issues
        if ($book->activeIssues()->exists()) {
            return redirect()
                ->back()
                ->with('error', 'Cannot delete book with active issues. Please collect all issued copies first.');
        }

        DB::beginTransaction();
        
        try {
            $book->delete();

            DB::commit();

            return redirect()
                ->route('library.books.index')
                ->with('success', 'Book deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Failed to delete book: ' . $e->getMessage());
        }
    }

    /**
     * AJAX search for books.
     */
    public function search(Request $request)
    {
        $search = $request->get('q', '');
        
        $books = Book::with('category')
            ->search($search)
            ->available()
            ->limit(10)
            ->get(['id', 'book_name', 'book_no', 'author_name', 'book_cat_id']);

        return response()->json($books);
    }
}
