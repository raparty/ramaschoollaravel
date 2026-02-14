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
        $classes = ClassModel::orderBy('name')->get();
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
        
        $classes = ClassModel::orderBy('name')->get();
        $students = [];
        $existingAttendance = [];
        
        if ($classId) {
            // Get students for the class
            $students = Admission::where('class_id', $classId)
                ->where('status', 'active')
                ->orderBy('name')
                ->get();
            
            // Get existing attendance for the date
            $existingAttendance = Attendance::forDate($date)
                ->forClass($classId)
                ->get()
                ->keyBy('admission_id');
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
                // Update or create attendance
                Attendance::updateOrCreate(
                    [
                        'admission_id' => $data['admission_id'],
                        'date' => $date,
                    ],
                    [
                        'status' => $data['status'],
                        'in_time' => isset($data['in_time']) ? Carbon::parse($date . ' ' . $data['in_time']) : null,
                        'out_time' => isset($data['out_time']) ? Carbon::parse($date . ' ' . $data['out_time']) : null,
                        'remarks' => $data['remarks'] ?? null,
                        'recorded_by' => auth()->id(),
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
        
        $classes = ClassModel::orderBy('name')->get();
        $students = [];
        $attendance = [];
        
        if ($classId && $date) {
            $students = Admission::where('class_id', $classId)
                ->where('status', 'active')
                ->orderBy('name')
                ->get();
            
            $attendance = Attendance::forDate($date)
                ->forClass($classId)
                ->get()
                ->keyBy('admission_id');
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
            
            $attendance = Attendance::forStudent($studentId)
                ->dateRange($startDate, $endDate)
                ->orderBy('date', 'desc')
                ->paginate(30);
            
            // Calculate statistics
            $totalDays = Attendance::forStudent($studentId)->dateRange($startDate, $endDate)->count();
            $presentDays = Attendance::forStudent($studentId)->dateRange($startDate, $endDate)->whereIn('status', [Attendance::STATUS_PRESENT, Attendance::STATUS_LATE, Attendance::STATUS_HALF_DAY])->count();
            $absentDays = Attendance::forStudent($studentId)->dateRange($startDate, $endDate)->absent()->count();
            $lateDays = Attendance::forStudent($studentId)->dateRange($startDate, $endDate)->late()->count();
            $leaveDays = Attendance::forStudent($studentId)->dateRange($startDate, $endDate)->onLeave()->count();
            
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
        
        $classes = ClassModel::orderBy('name')->get();
        $class = null;
        $attendance = collect();
        $statistics = [];
        
        if ($classId) {
            $class = ClassModel::findOrFail($classId);
            
            $attendance = Attendance::forDate($date)
                ->forClass($classId)
                ->with('student')
                ->orderBy('created_at')
                ->get();
            
            $total = Admission::where('class_id', $classId)->where('status', 'active')->count();
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
        
        $students = Admission::where('status', 'active')
            ->when($classId, fn($q) => $q->where('class_id', $classId))
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('regno', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'regno', 'class_id']);
        
        return response()->json($students);
    }
}
