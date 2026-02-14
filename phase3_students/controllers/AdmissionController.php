<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\ClassModel;
use App\Http\Requests\StoreAdmissionRequest;
use App\Http\Requests\UpdateAdmissionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

/**
 * AdmissionController
 * 
 * Handles student admission operations
 * Converts: add_admission.php, student_detail.php, edit_admission.php
 */
class AdmissionController extends Controller
{
    /**
     * Display a listing of student admissions.
     * Converts: student_detail.php
     */
    public function index(Request $request)
    {
        $query = Admission::with('class');

        // Search functionality (replaces searchby_name.php)
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by class
        if ($request->filled('class_id')) {
            $query->inClass($request->class_id);
        }

        // Filter by session
        if ($request->filled('session')) {
            $query->whereYear('admission_date', $request->session);
        }

        // Order by most recent
        $query->recent();

        // Paginate results
        $admissions = $query->paginate(30)->withQueryString();

        // Get classes for filter dropdown
        $classes = ClassModel::ordered()->get();

        return view('admissions.index', compact('admissions', 'classes'));
    }

    /**
     * Show the form for creating a new admission.
     * Converts: add_admission.php
     */
    public function create()
    {
        // Check permission
        $this->authorize('create-students');

        // Get all classes for dropdown
        $classes = ClassModel::ordered()->get();

        return view('admissions.create', compact('classes'));
    }

    /**
     * Store a newly created admission in database.
     * Converts: admission_process.php
     */
    public function store(StoreAdmissionRequest $request)
    {
        // Check permission
        $this->authorize('create-students');

        DB::beginTransaction();
        
        try {
            // Get validated data
            $validated = $request->validated();

            // Generate unique registration number
            $validated['reg_no'] = Admission::generateRegNo();

            // Handle student photo upload
            if ($request->hasFile('student_pic')) {
                $photoPath = $request->file('student_pic')->store('students/photos', 'public');
                $validated['student_pic'] = basename($photoPath);
            }

            // Handle Aadhaar document upload
            if ($request->hasFile('aadhaar_doc')) {
                $docPath = $request->file('aadhaar_doc')->store('students/aadhaar', 'public');
                $validated['aadhaar_doc_path'] = basename($docPath);
            }

            // Create admission record
            $admission = Admission::create($validated);

            DB::commit();

            return redirect()
                ->route('admissions.show', $admission)
                ->with('success', 'Student admission saved successfully! Registration No: ' . $admission->reg_no);

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Delete uploaded files if transaction fails
            if (isset($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }
            if (isset($docPath)) {
                Storage::disk('public')->delete($docPath);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to save admission: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified admission.
     * Converts: view_student_detail.php
     */
    public function show(Admission $admission)
    {
        // Check permission
        $this->authorize('view-students');

        // Load relationships
        $admission->load(['class', 'fees', 'transportFees', 'libraryBooks']);

        return view('admissions.show', compact('admission'));
    }

    /**
     * Show the form for editing the specified admission.
     * Converts: edit_admission.php
     */
    public function edit(Admission $admission)
    {
        // Check permission
        $this->authorize('edit-students');

        // Get all classes for dropdown
        $classes = ClassModel::ordered()->get();

        return view('admissions.edit', compact('admission', 'classes'));
    }

    /**
     * Update the specified admission in database.
     * Converts: process_edit_admission.php
     */
    public function update(UpdateAdmissionRequest $request, Admission $admission)
    {
        // Check permission
        $this->authorize('edit-students');

        DB::beginTransaction();
        
        try {
            // Get validated data
            $validated = $request->validated();

            // Handle student photo upload
            if ($request->hasFile('student_pic')) {
                // Delete old photo
                if ($admission->student_pic) {
                    Storage::disk('public')->delete('students/photos/' . $admission->student_pic);
                }
                
                $photoPath = $request->file('student_pic')->store('students/photos', 'public');
                $validated['student_pic'] = basename($photoPath);
            }

            // Handle Aadhaar document upload
            if ($request->hasFile('aadhaar_doc')) {
                // Delete old document
                if ($admission->aadhaar_doc_path) {
                    Storage::disk('public')->delete('students/aadhaar/' . $admission->aadhaar_doc_path);
                }
                
                $docPath = $request->file('aadhaar_doc')->store('students/aadhaar', 'public');
                $validated['aadhaar_doc_path'] = basename($docPath);
            }

            // Update admission record
            $admission->update($validated);

            DB::commit();

            return redirect()
                ->route('admissions.show', $admission)
                ->with('success', 'Student details updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Delete newly uploaded files if transaction fails
            if (isset($photoPath) && $photoPath !== $admission->student_pic) {
                Storage::disk('public')->delete($photoPath);
            }
            if (isset($docPath) && $docPath !== $admission->aadhaar_doc_path) {
                Storage::disk('public')->delete($docPath);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update admission: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified admission from database.
     * Converts: delete_admission.php
     */
    public function destroy(Admission $admission)
    {
        // Check permission
        $this->authorize('delete-students');

        // Check if student has pending fees
        if ($admission->fees()->where('status', 'pending')->exists()) {
            return redirect()
                ->back()
                ->with('error', 'Cannot delete student with pending fees. Please clear all fees first.');
        }

        // Check if student has library books
        if ($admission->libraryBooks()->whereNull('return_date')->exists()) {
            return redirect()
                ->back()
                ->with('error', 'Cannot delete student with unreturned library books. Please return all books first.');
        }

        DB::beginTransaction();
        
        try {
            // Delete associated files
            if ($admission->student_pic) {
                Storage::disk('public')->delete('students/photos/' . $admission->student_pic);
            }
            if ($admission->aadhaar_doc_path) {
                Storage::disk('public')->delete('students/aadhaar/' . $admission->aadhaar_doc_path);
            }

            // Delete admission record
            $admission->delete();

            DB::commit();

            return redirect()
                ->route('admissions.index')
                ->with('success', 'Student admission deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Failed to delete admission: ' . $e->getMessage());
        }
    }

    /**
     * Check if registration number is unique (AJAX).
     * Converts: checkregno.php
     */
    public function checkRegNo(Request $request)
    {
        $exists = Admission::where('reg_no', $request->reg_no)->exists();
        
        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'Registration number already exists' : 'Registration number is available'
        ]);
    }

    /**
     * Search students by name (AJAX).
     * Converts: searchby_name.php
     */
    public function searchByName(Request $request)
    {
        $students = Admission::search($request->q)
            ->with('class')
            ->limit(10)
            ->get(['id', 'reg_no', 'student_name', 'class_id']);

        return response()->json($students);
    }
}
