<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * LibraryController
 * * Handles library book management operations
 * Maps to legacy table: book_manager
 */
class LibraryController extends Controller
{
    /**
     * Display a listing of books.
     */
    public function index(Request $request)
    {
        $this->authorize('view-library');

        $query = Book::with('category');

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('category_id')) {
            $query->byCategory($request->category_id);
        }

        if ($request->filled('availability')) {
            if ($request->availability === 'available') {
                $query->available();
            }
        }

        $query->orderBy('book_name');
        $books = $query->paginate(20)->withQueryString();
        $categories = BookCategory::ordered()->get();

        return view('library.books.index', compact('books', 'categories'));
    }

    /**
     * Show the form for creating a new book.
     */
    public function create()
    {
        $this->authorize('create-library');
        $categories = BookCategory::ordered()->get();
        return view('library.books.create', compact('categories'));
    }

    /**
     * Store a newly created book.
     */
    public function store(Request $request)
    {
        $this->authorize('create-library');

        $validated = $request->validate([
            'book_name' => 'required|string|max:255',
            // Point to book_manager table and book_number column
            'book_no' => 'required|string|max:50|unique:book_manager,book_number',
            'author_name' => 'required|string|max:255',
            'book_cat_id' => 'required|exists:book_category,id',
            'no_of_copies' => 'required|integer|min:1',
            'book_edition' => 'nullable|string|max:100',
            'book_price' => 'nullable|numeric|min:0',
            'publisher' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            // Map request 'book_no' to database 'book_number'
            $data = $validated;
            $data['book_number'] = $validated['book_no'];
            $data['book_author'] = $validated['author_name'];
            $data['book_category_id'] = $validated['book_cat_id'];

            $book = Book::create($data);
            DB::commit();

            return redirect()->route('library.books.index')
                ->with('success', 'Book added successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified book.
     */
    public function show(Book $book)
    {
        $this->authorize('view-library');
        $book->load(['category', 'issues.student']);
        return view('library.books.show', compact('book'));
    }

    /**
     * Show the form for editing.
     */
    public function edit(Book $book)
    {
        $this->authorize('edit-library');
        $categories = BookCategory::ordered()->get();
        return view('library.books.edit', compact('book', 'categories'));
    }

    /**
     * Update the specified book.
     */
    public function update(Request $request, Book $book)
{
    $this->authorize('edit-library');

    $validated = $request->validate([
        'book_name'    => 'required|string|max:255',
        'book_no'      => 'required|string|max:50|unique:book_manager,book_number,' . $book->book_id . ',book_id',
        'author_name'  => 'required|string|max:255',
        // FIX: Changed from 'book_cat_id' to 'category_id' to match common form names
        'category_id'  => 'required|exists:book_category,id', 
        'no_of_copies' => 'required|integer|min:1',
        'book_edition' => 'nullable|string|max:100',
        'book_price'   => 'nullable|numeric|min:0',
        'publisher'    => 'nullable|string|max:255',
    ]);

    DB::beginTransaction();
    try {
        // Map request data to legacy column names
        $book->update([
            'book_name'        => $validated['book_name'],
            'book_number'      => $validated['book_no'],
            'book_author'      => $validated['author_name'],
            'book_category_id' => $validated['category_id'], // Maps 'category_id' from form to DB
            'no_of_copies'     => $validated['no_of_copies'],
            'book_edition'     => $validated['book_edition'],
            'book_price'       => $validated['book_price'],
        ]);

        DB::commit();
        return redirect()->route('library.books.show', $book)
            ->with('success', 'Book updated successfully!');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
    }
}

    /**
     * Remove the specified book.
     */
    public function destroy(Book $book)
    {
        $this->authorize('delete-library');

        if ($book->activeIssues()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete book with active issues.');
        }

        DB::beginTransaction();
        try {
            $book->delete();
            DB::commit();
            return redirect()->route('library.books.index')->with('success', 'Book deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * AJAX search.
     */
    public function search(Request $request)
    {
        $search = $request->get('q', '');
        $books = Book::with('category')
            ->search($search)
            ->available()
            ->limit(10)
            ->get();

        return response()->json($books);
    }
}
