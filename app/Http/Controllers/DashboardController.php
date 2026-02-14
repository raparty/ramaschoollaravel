<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\StudentFee;
use App\Models\Book;
use App\Models\BookIssue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * DashboardController
 * 
 * Displays the main dashboard with statistics and module links
 * Converts: dashboard.php
 */
class DashboardController extends Controller
{
    /**
     * Display the dashboard
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get statistics
        $stats = [
            'total_students' => Admission::where('is_active', true)->count(),
            'total_books' => Book::sum('total_copies'),
            'books_issued' => BookIssue::where('status', 'issued')->count(),
            'pending_fees' => StudentFee::where('status', '!=', 'paid')->sum('amount'),
            'total_fees_collected' => StudentFee::where('status', 'paid')->sum('paid_amount'),
        ];

        // Get recent admissions
        $recent_admissions = Admission::with('class')
            ->latest()
            ->take(5)
            ->get();

        // Get overdue books
        $overdue_books = BookIssue::with(['admission', 'book'])
            ->where('status', 'issued')
            ->where('due_date', '<', now())
            ->take(10)
            ->get();

        return view('dashboard', compact('stats', 'recent_admissions', 'overdue_books'));
    }

    /**
     * Search across all modules
     * Converts: searchby_name.php
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function search(Request $request)
    {
        $query = $request->input('q');
        
        if (empty($query)) {
            return redirect()->route('dashboard');
        }

        // Search students
        $students = Admission::where('student_name', 'like', "%{$query}%")
            ->orWhere('reg_no', 'like', "%{$query}%")
            ->orWhere('guardian_name', 'like', "%{$query}%")
            ->with('class')
            ->get();

        // Search books
        $books = Book::where('title', 'like', "%{$query}%")
            ->orWhere('author', 'like', "%{$query}%")
            ->orWhere('isbn', 'like', "%{$query}%")
            ->get();

        return view('search-results', compact('query', 'students', 'books'));
    }
}
