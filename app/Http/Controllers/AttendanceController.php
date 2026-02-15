<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Admission;
use App\Models\ClassModel;
use App\Http\Requests\MarkAttendanceRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Attendance Controller
 * 
 * Handles student attendance marking and viewing
 */
class AttendanceController extends Controller
{
    /**
     * Display attendance dashboard.
     *
     * @return View
     */
    public function index(): View
    {
        $classes = ClassModel::ordered()->get();
        $today = Carbon::today()->toDateString();
        
        // Get today's attendance statistics
        $todayStats = [
            'total' => Attendance::forDate($today)->count(),
            'present' => Attendance::forDate($today)->present()->count(),
            'absent' => Attendance::forDate($today)->absent()->count(),
            'late' => Attendance::forDate($today)->late()->count(),
        ];
        
        return view('attendance.index', compact('classes', 'todayStats', 'today'));
    }

    /**
     * Show attendance register for marking attendance.
     *
     * @param  Request  $request
     * @return View
     */
    public function register(Request $request): View
    {
        $classId = $request->input('class_id');
        $date = $request->input('date', Carbon::today()->toDateString());
        
        $classes = ClassModel::ordered()->get();
        $students = [];
        $existingAttendance = [];
        
        if ($classId) {
            // Get students for the class
            $students = Admission::where('class_id', $classId)
                ->orderBy('student_name')
                ->get();
            
            // Get existing attendance for the date, keyed by reg_no since attendance.user_id = admission.reg_no
            $attendanceRecords = Attendance::forDate($date)->get();
            foreach ($attendanceRecords as $record) {
                // Find the admission by reg_no
                $admission = Admission::where('reg_no', $record->user_id)->first();
                if ($admission) {
                    $existingAttendance[$admission->id] = $record;
                }
            }
        }
        
        return view('attendance.register', compact('classes', 'classId', 'date', 'students', 'existingAttendance'));
    }

    /**
     * Store bulk attendance.
     *
     * @param  MarkAttendanceRequest  $request
     * @return RedirectResponse
     */
    public function store(MarkAttendanceRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            
            $date = $request->input('date');
            $attendanceData = $request->input('attendance');
            
            foreach ($attendanceData as $data) {
                // Get the admission record to find the reg_no (which maps to user_id in attendance table)
                $admission = Admission::find($data['admission_id']);
                if (!$admission) {
                    continue;
                }
                
                // Update or create attendance using user_id (which is the reg_no)
                Attendance::updateOrCreate(
                    [
                        'user_id' => $admission->reg_no,
                        'attendance_date' => $date,
                    ],
                    [
                        'status' => $data['status'],
                        'marked_by' => auth()->user()->name ?? auth()->user()->user_id ?? 'Admin',
                    ]
                );
            }
            
            DB::commit();
            
            return redirect()->route('attendance.index')
                ->with('success', 'Attendance marked successfully.');
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
        $classId = $request->input('class_id');
        $date = $request->input('date');
        
        $classes = ClassModel::ordered()->get();
        $students = [];
        $attendance = [];
        
        if ($classId && $date) {
            $students = Admission::where('class_id', $classId)
                ->orderBy('student_name')
                ->get();
            
            // Get existing attendance for the date, keyed by admission id
            $attendanceRecords = Attendance::forDate($date)->get();
            foreach ($attendanceRecords as $record) {
                // Find the admission by reg_no
                $admission = Admission::where('reg_no', $record->user_id)->first();
                if ($admission) {
                    $attendance[$admission->id] = $record;
                }
            }
        }
        
        return view('attendance.edit', compact('classes', 'classId', 'date', 'students', 'attendance'));
    }

    /**
     * Update attendance.
     *
     * @param  MarkAttendanceRequest  $request
     * @return RedirectResponse
     */
    public function update(MarkAttendanceRequest $request): RedirectResponse
    {
        return $this->store($request); // Same logic as store
    }

    /**
     * View student's attendance.
     *
     * @param  Request  $request
     * @return View
     */
    public function studentAttendance(Request $request): View
    {
        $studentId = $request->input('student_id');
        $startDate = $request->input('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());
        
        $student = null;
        $attendance = collect();
        $statistics = [];
        
        if ($studentId) {
            $student = Admission::findOrFail($studentId);
            
            // Query using user_id which maps to reg_no
            $attendance = Attendance::where('user_id', $student->reg_no)
                ->whereBetween('attendance_date', [$startDate, $endDate])
                ->orderBy('attendance_date', 'desc')
                ->paginate(30);
            
            // Calculate statistics
            $totalDays = Attendance::where('user_id', $student->reg_no)
                ->whereBetween('attendance_date', [$startDate, $endDate])
                ->count();
            $presentDays = Attendance::where('user_id', $student->reg_no)
                ->whereBetween('attendance_date', [$startDate, $endDate])
                ->whereIn('status', [Attendance::STATUS_PRESENT, Attendance::STATUS_LATE, Attendance::STATUS_HALF_DAY])
                ->count();
            $absentDays = Attendance::where('user_id', $student->reg_no)
                ->whereBetween('attendance_date', [$startDate, $endDate])
                ->where('status', Attendance::STATUS_ABSENT)
                ->count();
            $lateDays = Attendance::where('user_id', $student->reg_no)
                ->whereBetween('attendance_date', [$startDate, $endDate])
                ->where('status', Attendance::STATUS_LATE)
                ->count();
            $leaveDays = Attendance::where('user_id', $student->reg_no)
                ->whereBetween('attendance_date', [$startDate, $endDate])
                ->where('status', 'On Leave')
                ->count();
            
            $statistics = [
                'total' => $totalDays,
                'present' => $presentDays,
                'absent' => $absentDays,
                'late' => $lateDays,
                'leave' => $leaveDays,
                'percentage' => $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 2) : 0,
            ];
        }
        
        return view('attendance.student', compact('student', 'attendance', 'statistics', 'startDate', 'endDate'));
    }

    /**
     * View class attendance for a date.
     *
     * @param  Request  $request
     * @return View
     */
    public function classAttendance(Request $request): View
    {
        $classId = $request->input('class_id');
        $date = $request->input('date', Carbon::today()->toDateString());
        
        $classes = ClassModel::ordered()->get();
        $class = null;
        $attendance = collect();
        $statistics = [];
        
        if ($classId) {
            $class = ClassModel::findOrFail($classId);
            
            // Get all students in the class
            $students = Admission::where('class_id', $classId)->get();
            
            // Get attendance for the date
            $attendanceRecords = Attendance::forDate($date)->get()->keyBy('user_id');
            
            // Build attendance collection with student info
            $attendance = collect();
            foreach ($students as $student) {
                if (isset($attendanceRecords[$student->reg_no])) {
                    $record = $attendanceRecords[$student->reg_no];
                    $record->student = $student;
                    $attendance->push($record);
                }
            }
            
            $total = Admission::where('class_id', $classId)->count();
            $present = $attendance->filter(fn($a) => $a->isPresent())->count();
            $absent = $attendance->filter(fn($a) => $a->isAbsent())->count();
            
            $statistics = [
                'total' => $total,
                'present' => $present,
                'absent' => $absent,
                'percentage' => $total > 0 ? round(($present / $total) * 100, 2) : 0,
            ];
        }
        
        return view('attendance.class', compact('classes', 'class', 'date', 'attendance', 'statistics'));
    }

    /**
     * Search students for AJAX.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchStudents(Request $request)
    {
        $query = $request->input('q');
        $classId = $request->input('class_id');
        
        $students = Admission::query()
            ->when($classId, fn($q) => $q->where('class_id', $classId))
            ->where(function ($q) use ($query) {
                $q->where('student_name', 'like', "%{$query}%")
                  ->orWhere('reg_no', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get(['id', 'student_name', 'reg_no', 'class_id']);
        
        return response()->json($students);
    }
}
