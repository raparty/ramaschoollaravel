<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\ClassModel;
use Illuminate\Http\Request;

class TransferCertificateController extends Controller
{
    /**
     * Maximum number of search results to display.
     */
    const SEARCH_RESULT_LIMIT = 50;

    /**
     * Display the transfer certificate entry/search page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get all classes for the search form
        $classes = ClassModel::orderBy('name')->get();

        return view('transfer-certificate.index', [
            'classes' => $classes,
        ]);
    }

    /**
     * Search for students and display results.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function search(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'class_id' => 'nullable|integer|exists:classes,id',
        ]);

        $classes = ClassModel::orderBy('name')->get();
        
        $query = Admission::with('class');

        // Filter by name if provided (use addcslashes to escape LIKE wildcards)
        if ($request->filled('name')) {
            $searchName = addcslashes($validated['name'], '%_');
            $query->where('student_name', 'like', '%' . $searchName . '%');
        }

        // Filter by class if provided
        if ($request->filled('class_id')) {
            $query->where('class_id', $validated['class_id']);
        }

        // Get total count before limiting
        $totalCount = $query->count();
        
        // Get students with limit to prevent overwhelming the page
        $students = $query->orderBy('student_name')->limit(self::SEARCH_RESULT_LIMIT)->get();

        return view('transfer-certificate.index', [
            'classes' => $classes,
            'students' => $students,
            'totalCount' => $totalCount,
            'isLimited' => $totalCount > self::SEARCH_RESULT_LIMIT,
        ]);
    }

    /**
     * Show transfer certificate by registration number from form.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function showByRegNo(Request $request)
    {
        $request->validate([
            'reg_no' => 'required|string',
        ]);

        // Redirect to the show route
        return redirect()->route('transfer-certificate.show', $request->reg_no);
    }

    /**
     * Display the transfer certificate for a specific student.
     *
     * @param string $regNo
     * @return \Illuminate\View\View
     */
    public function show($regNo)
    {
        // Find the student by registration number with their class
        $student = Admission::with('class')
            ->where('reg_no', $regNo)
            ->firstOrFail();

        return view('transfer-certificate.show', [
            'student' => $student,
        ]);
    }
}
