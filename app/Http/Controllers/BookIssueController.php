<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookIssue;
use App\Models\Admission;
use App\Models\LibraryFine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * BookIssueController
 * 
 * Handles book issue, return, and fine collection
 * Converts: library_add_student_books.php, library_return_student_books.php, library_fine_manager.php
 */
class BookIssueController extends Controller
{
    /**
     * Show book issue form.
     * Converts: library_add_student_books.php
     */
    public function issueForm(Request $request)
    {
        // Check permission
        $this->authorize('issue-library');

        $regNo = $request->get('registration_no');
        $student = null;
        $availableBooks = Book::available()->with('category')->get();

        if ($regNo) {
            $student = Admission::with('class')->where('reg_no', $regNo)->first();
        }

        return view('library.issue.create', compact('student', 'availableBooks'));
    }

    /**
     * Process book issue.
     */
    public function issueBook(Request $request)
    {
        // Check permission
        $this->authorize('issue-library');

        $validated = $request->validate([
            'registration_no' => 'required|exists:admissions,reg_no',
            'book_id' => 'required|exists:book_manager,book_id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after:issue_date',
        ]);

        DB::beginTransaction();
        
        try {
            // Check if book is available
            $book = Book::findOrFail($validated['book_id']);
            
            if (!$book->isAvailable()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'This book is not available. All copies are currently issued.');
            }

            // Create book issue record using book_number instead of book_id
            $issueData = [
                'registration_no' => $validated['registration_no'],
                'book_number' => $book->book_number,
                'issue_date' => $validated['issue_date'],
                'due_date' => $validated['due_date'],
                'booking_status' => '1',
                'session' => date('Y') . '-' . (date('Y') + 1),
            ];
            
            $issue = BookIssue::create($issueData);

            DB::commit();

            return redirect()
                ->route('library.issue.history', ['registration_no' => $validated['registration_no']])
                ->with('success', 'Book issued successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to issue book: ' . $e->getMessage());
        }
    }

    /**
     * Show book return form.
     * Converts: library_return_student_books.php
     */
    public function returnForm(Request $request)
    {
        // Check permission
        $this->authorize('return-library');

        $regNo = $request->get('registration_no');
        $student = null;
        $activeIssues = collect();

        if ($regNo) {
            $student = Admission::with('class')->where('reg_no', $regNo)->first();
            if ($student) {
                $activeIssues = BookIssue::with('book.category')
                    ->forStudent($regNo)
                    ->active()
                    ->get();
            }
        }

        return view('library.issue.return', compact('student', 'activeIssues'));
    }

    /**
     * Process book return.
     */
    public function returnBook(Request $request)
    {
        // Check permission
        $this->authorize('return-library');

        $validated = $request->validate([
            'issue_id' => 'required|exists:student_books_details,id',
            'return_date' => 'required|date',
            'fine_amount' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        
        try {
            // Get the issue record
            $issue = BookIssue::with('book', 'student')->findOrFail($validated['issue_id']);

            // Update return date
            $issue->return_date = $validated['return_date'];
            $issue->save();

            // If there's a fine, record it
            if (!empty($validated['fine_amount']) && $validated['fine_amount'] > 0) {
                LibraryFine::create([
                    'registration_no' => $issue->registration_no,
                    'book_issue_id' => $issue->id,
                    'fine_amount' => $validated['fine_amount'],
                    'payment_date' => now(),
                    'collected_by' => auth()->user()->name ?? 'Admin',
                ]);
            }

            DB::commit();

            return redirect()
                ->route('library.issue.history', ['registration_no' => $issue->registration_no])
                ->with('success', 'Book returned successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to return book: ' . $e->getMessage());
        }
    }

    /**
     * Display overdue books list.
     * Converts: library_overdue_books.php
     */
    public function overdueList(Request $request)
    {
        // Check permission
        $this->authorize('view-library');

        // Get overdue issues
        $overdueIssues = BookIssue::with(['book.category', 'student.class'])
            ->overdue()
            ->orderBy('due_date', 'asc')
            ->paginate(30);

        return view('library.issue.overdue', compact('overdueIssues'));
    }

    /**
     * Display student's issue history.
     * Converts: library_student_books_manager.php
     */
    public function studentHistory(Request $request)
    {
        // Check permission
        $this->authorize('view-library');

        $regNo = $request->get('registration_no');
        
        if (!$regNo) {
            return redirect()
                ->route('library.issue.create')
                ->with('error', 'Please select a student first.');
        }

        $student = Admission::with('class')->where('reg_no', $regNo)->firstOrFail();

        // Get issue history
        $issues = BookIssue::with('book.category')
            ->forStudent($regNo)
            ->orderBy('issue_date', 'desc')
            ->paginate(20);

        // Calculate statistics
        $stats = [
            'total_issued' => BookIssue::forStudent($regNo)->count(),
            'currently_issued' => BookIssue::forStudent($regNo)->active()->count(),
            'total_returned' => BookIssue::forStudent($regNo)->returned()->count(),
            'overdue' => BookIssue::forStudent($regNo)->overdue()->count(),
        ];

        return view('library.issue.history', compact('student', 'issues', 'stats'));
    }

    /**
     * Collect fine for overdue book.
     * Converts: library_add_fine.php
     */
    public function collectFine(Request $request)
    {
        // Check permission
        $this->authorize('collect-fine');

        $validated = $request->validate([
            'registration_no' => 'required|exists:admissions,reg_no',
            'book_issue_id' => 'required|exists:student_books_details,id',
            'fine_amount' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        
        try {
            // Create fine record
            $validated['payment_date'] = now();
            $validated['collected_by'] = auth()->user()->name ?? 'Admin';
            
            $fine = LibraryFine::create($validated);

            DB::commit();

            return redirect()
                ->back()
                ->with('success', 'Fine collected successfully! Amount: ₹' . number_format($fine->fine_amount, 2));

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to collect fine: ' . $e->getMessage());
        }
    }

    /**
     * Search students for book issue.
     */
    public function searchStudents(Request $request)
    {
        $search = $request->get('q', '');
        
        $students = Admission::with('class')
            ->where(function($query) use ($search) {
                $query->where('student_name', 'LIKE', "%{$search}%")
                      ->orWhere('reg_no', 'LIKE', "%{$search}%");
            })
            ->limit(10)
            ->get(['id', 'reg_no', 'student_name', 'class_id']);

        return response()->json($students);
    }
}
