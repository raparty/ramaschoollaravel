<?php

namespace App\Http\Controllers;

use App\Models\TransportStudentAssignment;
use App\Models\TransportRoute;
use App\Models\TransportVehicle;
use App\Models\Admission;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * TransportStudentController
 * 
 * Manages student transport assignments
 */
class TransportStudentController extends Controller
{
    /**
     * Display a listing of student assignments.
     */
    public function index(Request $request)
    {
        $query = TransportStudentAssignment::with(['student', 'route', 'vehicle']);

        // Get current session (you may need to adjust this based on your session management)
        $currentSession = session('current_session', date('Y'));
        
        // Filter by session
        if ($request->filled('session')) {
            $query->forSession($request->session);
        } else {
            $query->forSession($currentSession);
        }

        // Filter by class
        if ($request->filled('class_id')) {
            $query->forClass($request->class_id);
        }

        // Filter by route
        if ($request->filled('route_id')) {
            $query->forRoute($request->route_id);
        }

        // Search by registration number or student name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function($q) use ($search) {
                $q->where('reg_no', 'like', "%{$search}%")
                  ->orWhere('student_name', 'like', "%{$search}%");
            });
        }

        $assignments = $query->paginate(20);
        
        // Get filter options
        $routes = TransportRoute::ordered()->get();
        $vehicles = TransportVehicle::ordered()->get();
        
        try {
            $classes = ClassModel::ordered()->get();
        } catch (\Exception $e) {
            $classes = collect();
        }

        return view('transport.students.index', compact('assignments', 'routes', 'vehicles', 'classes', 'currentSession'));
    }

    /**
     * Show the form for creating a new assignment.
     */
    public function create()
    {
        $routes = TransportRoute::ordered()->get();
        $vehicles = TransportVehicle::ordered()->get();
        
        try {
            $classes = ClassModel::ordered()->get();
        } catch (\Exception $e) {
            $classes = collect();
        }
        
        // Get students who might not have transport assigned
        $students = Admission::orderBy('student_name')->get();
        
        $currentSession = session('current_session', date('Y'));

        return view('transport.students.create', compact('routes', 'vehicles', 'classes', 'students', 'currentSession'));
    }

    /**
     * Store a newly created assignment in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'registration_no' => 'required|string|exists:admissions,reg_no',
            'route_id' => 'required|exists:transport_add_route,route_id',
            'vehicle_id' => 'required|exists:transport_add_vechile,vechile_id',
            'class_id' => 'nullable|integer',
            'stream_id' => 'nullable|integer',
            'session' => 'required|string|max:50',
        ]);

        // Check if student already has transport assignment for this session
        $existing = TransportStudentAssignment::forStudent($validated['registration_no'])
            ->forSession($validated['session'])
            ->first();

        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Student already has transport assignment for this session.');
        }

        TransportStudentAssignment::create($validated);

        return redirect()->route('transport.students.index')
            ->with('success', 'Student transport assignment added successfully!');
    }

    /**
     * Show the form for editing the specified assignment.
     */
    public function edit(TransportStudentAssignment $student)
    {
        $routes = TransportRoute::ordered()->get();
        $vehicles = TransportVehicle::ordered()->get();
        
        try {
            $classes = ClassModel::ordered()->get();
        } catch (\Exception $e) {
            $classes = collect();
        }

        return view('transport.students.edit', compact('student', 'routes', 'vehicles', 'classes'));
    }

    /**
     * Update the specified assignment in storage.
     */
    public function update(Request $request, TransportStudentAssignment $student)
    {
        $validated = $request->validate([
            'route_id' => 'required|exists:transport_add_route,route_id',
            'vehicle_id' => 'required|exists:transport_add_vechile,vechile_id',
            'class_id' => 'nullable|integer',
            'stream_id' => 'nullable|integer',
        ]);

        $student->update($validated);

        return redirect()->route('transport.students.index')
            ->with('success', 'Student transport assignment updated successfully!');
    }

    /**
     * Remove the specified assignment from storage.
     */
    public function destroy(TransportStudentAssignment $student)
    {
        try {
            $student->delete();

            return redirect()->route('transport.students.index')
                ->with('success', 'Student transport assignment removed successfully!');
        } catch (\Exception $e) {
            return redirect()->route('transport.students.index')
                ->with('error', 'Error removing assignment: ' . $e->getMessage());
        }
    }
}
