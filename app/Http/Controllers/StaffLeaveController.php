<?php

namespace App\Http\Controllers;

use App\Models\StaffLeave;
use App\Models\LeaveType;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StaffLeaveController extends Controller
{
    /**
     * Display a listing of staff leaves.
     */
    public function index(Request $request)
    {
        $query = StaffLeave::with(['staff', 'leaveType', 'approver'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by staff
        if ($request->has('staff_id') && $request->staff_id != '') {
            $query->where('staff_id', $request->staff_id);
        }

        // Filter by leave type
        if ($request->has('leave_type_id') && $request->leave_type_id != '') {
            $query->where('leave_type_id', $request->leave_type_id);
        }

        $leaves = $query->paginate(15);
        $leaveTypes = LeaveType::active()->get();
        $staff = Staff::orderBy('name')->get();

        return view('staff-leaves.index', compact('leaves', 'leaveTypes', 'staff'));
    }

    /**
     * Show the form for creating a new leave application.
     */
    public function create()
    {
        $leaveTypes = LeaveType::active()->get();
        $staff = Staff::orderBy('name')->get();

        return view('staff-leaves.create', compact('leaveTypes', 'staff'));
    }

    /**
     * Store a newly created leave application.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
        ]);

        // Calculate number of days (including both start and end date)
        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $days = $startDate->diffInDays($endDate) + 1;

        // Check for overlapping leaves
        $overlapping = StaffLeave::where('staff_id', $validated['staff_id'])
            ->where('status', '!=', 'rejected')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate)
                          ->where('end_date', '>=', $endDate);
                    });
            })
            ->exists();

        if ($overlapping) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Leave application overlaps with existing leave for this staff member.');
        }

        StaffLeave::create([
            'staff_id' => $validated['staff_id'],
            'leave_type_id' => $validated['leave_type_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'days' => $days,
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);

        return redirect()->route('staff-leaves.index')
            ->with('success', 'Leave application submitted successfully.');
    }

    /**
     * Display the specified leave application.
     */
    public function show(StaffLeave $staffLeave)
    {
        $staffLeave->load(['staff', 'leaveType', 'approver']);
        return view('staff-leaves.show', compact('staffLeave'));
    }

    /**
     * Show the form for editing the specified leave application.
     * Only pending leaves can be edited.
     */
    public function edit(StaffLeave $staffLeave)
    {
        if ($staffLeave->status !== 'pending') {
            return redirect()->route('staff-leaves.index')
                ->with('error', 'Only pending leave applications can be edited.');
        }

        $leaveTypes = LeaveType::active()->get();
        $staff = Staff::orderBy('name')->get();

        return view('staff-leaves.edit', compact('staffLeave', 'leaveTypes', 'staff'));
    }

    /**
     * Update the specified leave application.
     * Only pending leaves can be updated.
     */
    public function update(Request $request, StaffLeave $staffLeave)
    {
        if ($staffLeave->status !== 'pending') {
            return redirect()->route('staff-leaves.index')
                ->with('error', 'Only pending leave applications can be updated.');
        }

        $validated = $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
        ]);

        // Calculate number of days
        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $days = $startDate->diffInDays($endDate) + 1;

        // Check for overlapping leaves (excluding current leave)
        $overlapping = StaffLeave::where('staff_id', $validated['staff_id'])
            ->where('id', '!=', $staffLeave->id)
            ->where('status', '!=', 'rejected')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate)
                          ->where('end_date', '>=', $endDate);
                    });
            })
            ->exists();

        if ($overlapping) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Leave application overlaps with existing leave for this staff member.');
        }

        $staffLeave->update([
            'staff_id' => $validated['staff_id'],
            'leave_type_id' => $validated['leave_type_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'days' => $days,
            'reason' => $validated['reason'],
        ]);

        return redirect()->route('staff-leaves.index')
            ->with('success', 'Leave application updated successfully.');
    }

    /**
     * Remove the specified leave application.
     * Only pending leaves can be deleted.
     */
    public function destroy(StaffLeave $staffLeave)
    {
        if ($staffLeave->status !== 'pending') {
            return redirect()->route('staff-leaves.index')
                ->with('error', 'Only pending leave applications can be deleted.');
        }

        $staffLeave->delete();

        return redirect()->route('staff-leaves.index')
            ->with('success', 'Leave application deleted successfully.');
    }

    /**
     * Approve a leave application.
     */
    public function approve(Request $request, StaffLeave $staffLeave)
    {
        if ($staffLeave->status !== 'pending') {
            return redirect()->route('staff-leaves.index')
                ->with('error', 'Only pending leave applications can be approved.');
        }

        $validated = $request->validate([
            'admin_remarks' => 'nullable|string|max:500',
        ]);

        $staffLeave->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'admin_remarks' => $validated['admin_remarks'] ?? null,
        ]);

        return redirect()->route('staff-leaves.index')
            ->with('success', 'Leave application approved successfully.');
    }

    /**
     * Reject a leave application.
     */
    public function reject(Request $request, StaffLeave $staffLeave)
    {
        if ($staffLeave->status !== 'pending') {
            return redirect()->route('staff-leaves.index')
                ->with('error', 'Only pending leave applications can be rejected.');
        }

        $validated = $request->validate([
            'admin_remarks' => 'required|string|max:500',
        ]);

        $staffLeave->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'admin_remarks' => $validated['admin_remarks'],
        ]);

        return redirect()->route('staff-leaves.index')
            ->with('success', 'Leave application rejected.');
    }
}
