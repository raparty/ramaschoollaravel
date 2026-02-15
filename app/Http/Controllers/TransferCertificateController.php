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
        // Validate input - at least one search criterion required
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'class_id' => 'nullable|integer|exists:classes,id',
        ]);

        // Require at least one search criterion
        if (empty($validated['name']) && empty($validated['class_id'])) {
            return redirect()->route('transfer-certificate.index')
                ->withErrors(['search' => 'Please provide at least one search criterion (name or class).']);
        }

        $classes = ClassModel::orderBy('name')->get();
        
        $query = Admission::with('class');

        // Filter by name if provided (Laravel handles SQL injection via parameter binding)
        if ($request->filled('name')) {
            $query->where('student_name', 'like', '%' . $validated['name'] . '%');
        }

        // Filter by class if provided
        if ($request->filled('class_id')) {
            $query->where('class_id', $validated['class_id']);
        }

        // Get students with limit + 1 to check if there are more results
        $students = $query->orderBy('student_name')->limit(self::SEARCH_RESULT_LIMIT + 1)->get();
        
        // Check if results were limited
        $isLimited = $students->count() > self::SEARCH_RESULT_LIMIT;
        
        // If limited, remove the extra record
        if ($isLimited) {
            $students = $students->take(self::SEARCH_RESULT_LIMIT);
        }

        return view('transfer-certificate.index', [
            'classes' => $classes,
            'students' => $students,
            'isLimited' => $isLimited,
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
