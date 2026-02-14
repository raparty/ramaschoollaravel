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
        // FIXED: Removed 'is_active' because it doesn't exist in your DB
        'total_students' => Admission::count(), 
        
        // TEMPORARY: Set these to 0 until you verify the column names in these tables
        'total_books' => 0, 
        'books_issued' => 0,
        'pending_fees' => 0,
        'total_fees_collected' => 0,
    ];

    // Get recent admissions
    $recent_admissions = Admission::with('class')
        // FIXED: Using 'created_at' specifically as 'latest()' often defaults to 'updated_at'
        ->orderBy('created_at', 'desc') 
        ->take(5)
        ->get();

    // Get overdue books - Keeping as is for now, but might need adjustment later
    $overdue_books = []; 

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
