<?php

namespace App\Http\Controllers;

use App\Models\StaffAttendance;
use App\Models\Staff;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Staff Attendance Controller
 * 
 * Handles staff attendance marking and viewing
 */
class StaffAttendanceController extends Controller
{
    /**
     * Display staff attendance dashboard.
     *
     * @return View
     */
    public function index(): View
    {
        $departments = Department::withCount('staff')->get();
        $today = Carbon::today()->toDateString();
        
        // Get today's attendance statistics
        $todayStats = [
            'total' => StaffAttendance::forDate($today)->count(),
            'present' => StaffAttendance::forDate($today)->present()->count(),
            'absent' => StaffAttendance::forDate($today)->absent()->count(),
            'on_leave' => StaffAttendance::forDate($today)->onLeave()->count(),
        ];
        
        // Calculate total staff count
        $totalStaff = Staff::count();
        $todayStats['not_marked'] = $totalStaff - $todayStats['total'];
        
        return view('staff-attendance.index', compact('departments', 'todayStats', 'today', 'totalStaff'));
    }

    /**
     * Show attendance register for marking attendance.
     *
     * @param  Request  $request
     * @return View
     */
    public function register(Request $request): View
    {
        $departmentId = $request->input('department_id');
        $date = $request->input('date', Carbon::today()->toDateString());
        
        $departments = Department::withCount('staff')->get();
        $staffMembers = [];
        $existingAttendance = [];
        
        if ($departmentId) {
            // Get staff for the department
            $staffMembers = Staff::where('dept_id', $departmentId)
                ->orderBy('name')
                ->get();
            
            // Get existing attendance for the date
            $attendanceRecords = StaffAttendance::forDate($date)->get()->keyBy('staff_id');
            
            foreach ($staffMembers as $staff) {
                if (isset($attendanceRecords[$staff->id])) {
                    $existingAttendance[$staff->id] = $attendanceRecords[$staff->id];
                }
            }
        } else {
            // Get all staff if no department selected
            $staffMembers = Staff::orderBy('name')
                ->get();
            
            // Get existing attendance for the date
            $attendanceRecords = StaffAttendance::forDate($date)->get()->keyBy('staff_id');
            
            foreach ($staffMembers as $staff) {
                if (isset($attendanceRecords[$staff->id])) {
                    $existingAttendance[$staff->id] = $attendanceRecords[$staff->id];
                }
            }
        }
        
        return view('staff-attendance.register', compact('departments', 'departmentId', 'date', 'staffMembers', 'existingAttendance'));
    }

    /**
     * Store bulk attendance.
     *
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'attendance' => 'required|array',
            'attendance.*.staff_id' => 'required|exists:staff_employee,id',
            'attendance.*.status' => 'required|in:present,absent,leave,half-day',
        ]);
        
        try {
            DB::beginTransaction();
            
            $date = $request->input('date');
            $attendanceData = $request->input('attendance');
            
            foreach ($attendanceData as $data) {
                // Check if staff exists
                $staffExists = Staff::find($data['staff_id']);
                if (!$staffExists) {
                    continue;
                }
                
                // Update or create attendance
                StaffAttendance::updateOrCreate(
                    [
                        'staff_id' => $data['staff_id'],
                        'att_date' => $date,
                    ],
                    [
                        'status' => $data['status'],
                    ]
                );
            }
            
            DB::commit();
            
            return redirect()->route('staff-attendance.index')
                ->with('success', 'Staff attendance marked successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error marking attendance: ' . $e->getMessage());
        }
    }

    /**
     * Show edit form for attendance.
     *
     * @param  Request  $request
     * @return View
     */
    public function edit(Request $request): View
    {
        $departmentId = $request->input('department_id');
        $date = $request->input('date');
        
        $departments = Department::withCount('staff')->get();
        $staffMembers = [];
        $attendance = [];
        
        if ($date) {
            if ($departmentId) {
                $staffMembers = Staff::where('dept_id', $departmentId)
                    ->orderBy('name')
                    ->get();
            } else {
                $staffMembers = Staff::orderBy('name')
                    ->get();
            }
            
            // Get existing attendance for the date
            $attendanceRecords = StaffAttendance::forDate($date)->get()->keyBy('staff_id');
            
            foreach ($staffMembers as $staff) {
                if (isset($attendanceRecords[$staff->id])) {
                    $attendance[$staff->id] = $attendanceRecords[$staff->id];
                }
            }
        }
        
        return view('staff-attendance.edit', compact('departments', 'departmentId', 'date', 'staffMembers', 'attendance'));
    }

    /**
     * Update attendance.
     *
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function update(Request $request): RedirectResponse
    {
        return $this->store($request); // Same logic as store
    }

    /**
     * View individual staff member's attendance.
     *
     * @param  Request  $request
     * @return View
     */
    public function staffAttendance(Request $request): View
    {
        $staffId = $request->input('staff_id');
        $startDate = $request->input('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());
        
        $staff = null;
        $attendance = collect();
        $statistics = [];
        
        if ($staffId) {
            $staff = Staff::findOrFail($staffId);
            
            // Get attendance records
            $attendance = StaffAttendance::where('staff_id', $staff->id)
                ->whereBetween('att_date', [$startDate, $endDate])
                ->orderBy('att_date', 'desc')
                ->paginate(30);
            
            // Calculate statistics
            $totalDays = StaffAttendance::where('staff_id', $staff->id)
                ->whereBetween('att_date', [$startDate, $endDate])
                ->count();
            $presentDays = StaffAttendance::where('staff_id', $staff->id)
                ->whereBetween('att_date', [$startDate, $endDate])
                ->whereIn('status', ['present', 'half-day'])
                ->count();
            $absentDays = StaffAttendance::where('staff_id', $staff->id)
                ->whereBetween('att_date', [$startDate, $endDate])
                ->where('status', 'absent')
                ->count();
            $leaveDays = StaffAttendance::where('staff_id', $staff->id)
                ->whereBetween('att_date', [$startDate, $endDate])
                ->where('status', 'leave')
                ->count();
            
            $statistics = [
                'total' => $totalDays,
                'present' => $presentDays,
                'absent' => $absentDays,
                'leave' => $leaveDays,
                'percentage' => $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 2) : 0,
            ];
        }
        
        $staffList = Staff::orderBy('name')->get();
        
        return view('staff-attendance.staff', compact('staff', 'staffList', 'attendance', 'statistics', 'startDate', 'endDate'));
    }

    /**
     * View department attendance for a date.
     *
     * @param  Request  $request
     * @return View
     */
    public function departmentAttendance(Request $request): View
    {
        $departmentId = $request->input('department_id');
        $date = $request->input('date', Carbon::today()->toDateString());
        
        $departments = Department::withCount('staff')->get();
        $department = null;
        $attendance = collect();
        $statistics = [];
        
        if ($departmentId) {
            $department = Department::findOrFail($departmentId);
            
            // Get all staff in the department
            $staffMembers = Staff::where('dept_id', $departmentId)->get()->keyBy('id');
            
            // Get attendance for the date
            $attendanceRecords = StaffAttendance::forDate($date)
                ->whereIn('staff_id', $staffMembers->pluck('id'))
                ->get();
            
            // Build attendance collection with staff info
            foreach ($attendanceRecords as $record) {
                if (isset($staffMembers[$record->staff_id])) {
                    $item = (object)[
                        'attendance' => $record,
                        'staff' => $staffMembers[$record->staff_id],
                        'status' => $record->status,
                    ];
                    $item->isPresent = function() use ($record) { return $record->isPresent(); };
                    $item->isAbsent = function() use ($record) { return $record->isAbsent(); };
                    $item->isOnLeave = function() use ($record) { return $record->isOnLeave(); };
                    $attendance->push($item);
                }
            }
            
            $total = $staffMembers->count();
            $present = $attendance->filter(fn($a) => ($a->isPresent)())->count();
            $absent = $attendance->filter(fn($a) => ($a->isAbsent)())->count();
            $onLeave = $attendance->filter(fn($a) => ($a->isOnLeave)())->count();
            
            $statistics = [
                'total' => $total,
                'present' => $present,
                'absent' => $absent,
                'on_leave' => $onLeave,
                'percentage' => $total > 0 ? round(($present / $total) * 100, 2) : 0,
            ];
        }
        
        return view('staff-attendance.department', compact('departments', 'department', 'date', 'attendance', 'statistics'));
    }

    /**
     * Search staff for AJAX.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchStaff(Request $request)
    {
        $query = $request->input('q');
        $departmentId = $request->input('department_id');
        
        $staff = Staff::query()
            ->when($departmentId, fn($q) => $q->where('dept_id', $departmentId))
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('employee_id', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'employee_id', 'dept_id']);
        
        return response()->json($staff);
    }
}
