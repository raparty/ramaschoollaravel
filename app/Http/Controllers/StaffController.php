<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\Department;
use App\Models\Position;
use App\Http\Requests\StoreStaffRequest;
use App\Http\Requests\UpdateStaffRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

/**
 * StaffController
 * 
 * Manages staff/employee operations including CRUD, search, and profile management
 */
class StaffController extends Controller
{
    /**
     * Display a listing of staff.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Staff::with(['department', 'position']);

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by department
        if ($request->filled('department')) {
            $query->byDepartment($request->department);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->inactive();
            }
        }

        $staff = $query->orderBy('created_at', 'desc')->paginate(20);
        $departments = Department::all();

        return view('staff.index', compact('staff', 'departments'));
    }

    /**
     * Show the form for creating a new staff member.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $departments = Department::all();
        $positions = Position::all();

        return view('staff.create', compact('departments', 'positions'));
    }

    /**
     * Store a newly created staff member in storage.
     *
     * @param \App\Http\Requests\StoreStaffRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreStaffRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            // Handle photo upload
            if ($request->hasFile('photo')) {
                $data['photo'] = $request->file('photo')->store('staff/photos', 'public');
            }

            $staff = Staff::create($data);

            DB::commit();

            return redirect()
                ->route('staff.index')
                ->with('success', 'Staff member added successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to add staff member. Please try again.');
        }
    }

    /**
     * Display the specified staff member.
     *
     * @param \App\Models\Staff $staff
     * @return \Illuminate\Http\Response
     */
    public function show(Staff $staff)
    {
        $staff->load(['department', 'position', 'salaries' => function ($query) {
            $query->orderBy('year', 'desc')->orderBy('month', 'desc')->limit(6);
        }, 'attendance' => function ($query) {
            $query->orderBy('date', 'desc')->limit(30);
        }]);

        // Calculate attendance statistics for current month
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        $monthlyAttendance = $staff->attendance()
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->get();

        $attendanceStats = [
            'present' => $monthlyAttendance->where('status', 'present')->count(),
            'absent' => $monthlyAttendance->where('status', 'absent')->count(),
            'leave' => $monthlyAttendance->where('status', 'leave')->count(),
            'half_day' => $monthlyAttendance->where('status', 'half-day')->count(),
        ];

        return view('staff.show', compact('staff', 'attendanceStats'));
    }

    /**
     * Show the form for editing the specified staff member.
     *
     * @param \App\Models\Staff $staff
     * @return \Illuminate\Http\Response
     */
    public function edit(Staff $staff)
    {
        $departments = Department::all();
        $positions = Position::all();

        return view('staff.edit', compact('staff', 'departments', 'positions'));
    }

    /**
     * Update the specified staff member in storage.
     *
     * @param \App\Http\Requests\UpdateStaffRequest $request
     * @param \App\Models\Staff $staff
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateStaffRequest $request, Staff $staff)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            // Handle photo upload
            if ($request->hasFile('photo')) {
                // Delete old photo if exists
                if ($staff->photo) {
                    Storage::disk('public')->delete($staff->photo);
                }
                $data['photo'] = $request->file('photo')->store('staff/photos', 'public');
            }

            $staff->update($data);

            DB::commit();

            return redirect()
                ->route('staff.show', $staff)
                ->with('success', 'Staff member updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update staff member. Please try again.');
        }
    }

    /**
     * Remove the specified staff member from storage.
     *
     * @param \App\Models\Staff $staff
     * @return \Illuminate\Http\Response
     */
    public function destroy(Staff $staff)
    {
        try {
            // Soft delete the staff member
            $staff->delete();

            return redirect()
                ->route('staff.index')
                ->with('success', 'Staff member deleted successfully!');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete staff member. Please try again.');
        }
    }

    /**
     * AJAX search for staff members.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $search = $request->get('q', '');

        $staff = Staff::with(['department', 'position'])
            ->search($search)
            ->active()
            ->limit(10)
            ->get()
            ->map(function ($member) {
                return [
                    'id' => $member->id,
                    'staff_id' => $member->staff_id,
                    'name' => $member->name,
                    'department' => $member->department->name ?? '',
                    'position' => $member->position->title ?? '',
                    'email' => $member->email,
                    'text' => "{$member->staff_id} - {$member->name} ({$member->department->name ?? 'N/A'})",
                ];
            });

        return response()->json($staff);
    }
}
