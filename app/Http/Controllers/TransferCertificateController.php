<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\ClassModel;
use Illuminate\Http\Request;

class TransferCertificateController extends Controller
{
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
        $classes = ClassModel::orderBy('name')->get();
        
        $query = Admission::with('class');

        // Filter by name if provided
        if ($request->filled('name')) {
            $query->where('student_name', 'like', '%' . $request->name . '%');
        }

        // Filter by class if provided
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        // Get students (limit to 50 results to prevent overwhelming the page)
        $students = $query->orderBy('student_name')->limit(50)->get();

        return view('transfer-certificate.index', [
            'classes' => $classes,
            'students' => $students,
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
