<?php

namespace App\Http\Controllers;

use App\Models\HostelWarden;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * HostelWardenController
 * 
 * Manages hostel warden operations
 */
class HostelWardenController extends Controller
{
    /**
     * Display a listing of wardens.
     */
    public function index(Request $request)
    {
        $query = HostelWarden::query();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by gender
        if ($request->filled('gender')) {
            $query->byGender($request->gender);
        }

        // Search by name or employee code
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('employee_code', 'like', '%' . $request->search . '%');
            });
        }

        $wardens = $query->ordered()->paginate(20);

        return view('hostel.wardens.index', compact('wardens'));
    }

    /**
     * Show the form for creating a new warden.
     */
    public function create()
    {
        return view('hostel.wardens.create');
    }

    /**
     * Store a newly created warden in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'employee_code' => 'required|string|max:50|unique:hostel_wardens,employee_code',
            'email' => 'nullable|email|max:100',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:500',
            'gender' => 'required|in:Male,Female,Other',
            'date_of_joining' => 'nullable|date',
            'status' => 'required|in:Active,Inactive,On Leave',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['is_active'] = $request->has('is_active');

        HostelWarden::create($validated);

        return redirect()->route('hostel.wardens.index')
            ->with('success', 'Warden created successfully!');
    }

    /**
     * Display the specified warden.
     */
    public function show(HostelWarden $warden)
    {
        $warden->load([
            'hostelAssignments.hostel',
            'reportedIncidents',
            'submittedAttendance',
            'complaintResponses',
            'submittedExpenses'
        ]);

        return view('hostel.wardens.show', compact('warden'));
    }

    /**
     * Show the form for editing the specified warden.
     */
    public function edit(HostelWarden $warden)
    {
        return view('hostel.wardens.edit', compact('warden'));
    }

    /**
     * Update the specified warden in storage.
     */
    public function update(Request $request, HostelWarden $warden)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'employee_code' => 'required|string|max:50|unique:hostel_wardens,employee_code,' . $warden->id,
            'email' => 'nullable|email|max:100',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:500',
            'gender' => 'required|in:Male,Female,Other',
            'date_of_joining' => 'nullable|date',
            'status' => 'required|in:Active,Inactive,On Leave',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['updated_by'] = Auth::id();
        $validated['is_active'] = $request->has('is_active');

        $warden->update($validated);

        return redirect()->route('hostel.wardens.show', $warden)
            ->with('success', 'Warden updated successfully!');
    }

    /**
     * Remove the specified warden from storage.
     */
    public function destroy(HostelWarden $warden)
    {
        try {
            // Check if warden has active assignments
            $activeAssignments = $warden->hostelAssignments()
                ->active()
                ->current()
                ->count();

            if ($activeAssignments > 0) {
                return redirect()->route('hostel.wardens.index')
                    ->with('error', "Cannot delete warden. They have {$activeAssignments} active assignment(s).");
            }

            $warden->delete();

            return redirect()->route('hostel.wardens.index')
                ->with('success', 'Warden deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('hostel.wardens.index')
                ->with('error', 'Error deleting warden: ' . $e->getMessage());
        }
    }
}
