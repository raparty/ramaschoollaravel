<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the departments.
     */
    public function index()
    {
        $departments = Department::withCount('staff')->orderBy('name')->paginate(20);

        return view('departments.index', compact('departments'));
    }

    /**
     * Show the form for creating a new department.
     */
    public function create()
    {
        return view('departments.create');
    }

    /**
     * Store a newly created department in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:staff_departments,name',
            'description' => 'nullable|string',
        ]);

        try {
            Department::create($request->only(['name', 'description']));
            
            return redirect()->route('departments.index')
                ->with('success', 'Department created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to create department. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified department.
     */
    public function edit(Department $department)
    {
        return view('departments.edit', compact('department'));
    }

    /**
     * Update the specified department in storage.
     */
    public function update(Request $request, Department $department)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:staff_departments,name,' . $department->id,
            'description' => 'nullable|string',
        ]);

        try {
            $department->update($request->only(['name', 'description']));
            
            return redirect()->route('departments.index')
                ->with('success', 'Department updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to update department. Please try again.');
        }
    }

    /**
     * Remove the specified department from storage.
     */
    public function destroy(Department $department)
    {
        try {
            // Check if department has staff members
            if ($department->staff()->count() > 0) {
                return back()->with('error', 'Cannot delete department with existing staff members.');
            }
            
            $department->delete();
            
            return redirect()->route('departments.index')
                ->with('success', 'Department deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete department. Please try again.');
        }
    }
}
