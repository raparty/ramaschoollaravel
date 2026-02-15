<?php

namespace App\Http\Controllers;

use App\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaveTypeController extends Controller
{
    /**
     * Display a listing of leave types.
     */
    public function index()
    {
        $leaveTypes = LeaveType::orderBy('is_active', 'desc')
            ->orderBy('name')
            ->paginate(15);

        return view('leave-types.index', compact('leaveTypes'));
    }

    /**
     * Show the form for creating a new leave type.
     */
    public function create()
    {
        return view('leave-types.create');
    }

    /**
     * Store a newly created leave type.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:leave_types,name',
            'description' => 'nullable|string',
            'max_days' => 'nullable|integer|min:1|max:365',
            'requires_approval' => 'required|boolean',
            'is_active' => 'required|boolean',
        ]);

        LeaveType::create($validated);

        return redirect()->route('leave-types.index')
            ->with('success', 'Leave type created successfully.');
    }

    /**
     * Show the form for editing the specified leave type.
     */
    public function edit(LeaveType $leaveType)
    {
        return view('leave-types.edit', compact('leaveType'));
    }

    /**
     * Update the specified leave type.
     */
    public function update(Request $request, LeaveType $leaveType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:leave_types,name,' . $leaveType->id,
            'description' => 'nullable|string',
            'max_days' => 'nullable|integer|min:1|max:365',
            'requires_approval' => 'required|boolean',
            'is_active' => 'required|boolean',
        ]);

        $leaveType->update($validated);

        return redirect()->route('leave-types.index')
            ->with('success', 'Leave type updated successfully.');
    }

    /**
     * Remove the specified leave type.
     */
    public function destroy(LeaveType $leaveType)
    {
        try {
            // Check if there are any staff leaves using this type
            if ($leaveType->staffLeaves()->exists()) {
                return redirect()->route('leave-types.index')
                    ->with('error', 'Cannot delete leave type. It is being used by staff leave applications.');
            }

            $leaveType->delete();

            return redirect()->route('leave-types.index')
                ->with('success', 'Leave type deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('leave-types.index')
                ->with('error', 'Failed to delete leave type: ' . $e->getMessage());
        }
    }

    /**
     * Toggle leave type status.
     */
    public function toggleStatus(LeaveType $leaveType)
    {
        $leaveType->update([
            'is_active' => !$leaveType->is_active
        ]);

        return redirect()->route('leave-types.index')
            ->with('success', 'Leave type status updated successfully.');
    }
}
