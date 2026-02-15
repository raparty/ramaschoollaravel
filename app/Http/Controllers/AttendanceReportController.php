<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Admission;
use App\Models\ClassModel;
use App\Http\Requests\AttendanceReportRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\Response;
use Carbon\Carbon;

/**
 * Attendance Report Controller
 * 
 * Handles attendance reports and analytics
 */
class AttendanceReportController extends Controller
{
    /**
     * Display report dashboard.
     *
     * @return View
     */
    public function index(): View
    {
        $classes = ClassModel::ordered()->get();
        
        return view('reports.attendance.index', compact('classes'));
    }

    /**
     * Generate attendance report.
     *
     * @param  AttendanceReportRequest  $request
     * @return View|Response
     */
    public function generate(AttendanceReportRequest $request)
    {
        $reportType = $request->input('report_type');
        $export = $request->input('export');
        
        switch ($reportType) {
            case 'student':
                return $this->studentReport($request);
            case 'class':
                return $this->classReport($request);
            case 'monthly':
                return $this->monthlyReport($request);
            case 'daterange':
                return $this->dateRangeReport($request);
            default:
                return back()->with('error', 'Invalid report type.');
        }
    }

    /**
     * Generate student-wise attendance report.
     *
     * @param  AttendanceReportRequest  $request
     * @return View
     */
    public function studentReport(AttendanceReportRequest $request): View
    {
        $studentId = $request->input('admission_id');
        $startDate = $request->input('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());
        
        $student = Admission::findOrFail($studentId);
        
        $attendance = Attendance::forStudent($studentId)
            ->dateRange($startDate, $endDate)
            ->orderBy('date')
            ->get();
        
        // Calculate statistics
        $totalDays = $attendance->count();
        $presentDays = $attendance->filter(fn($a) => $a->isPresent())->count();
        $absentDays = $attendance->filter(fn($a) => $a->isAbsent())->count();
        $lateDays = $attendance->where('status', Attendance::STATUS_LATE)->count();
        $leaveDays = $attendance->where('status', Attendance::STATUS_LEAVE)->count();
        
        $statistics = [
            'total' => $totalDays,
            'present' => $presentDays,
            'absent' => $absentDays,
            'late' => $lateDays,
            'leave' => $leaveDays,
            'percentage' => $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 2) : 0,
        ];
        
        if ($request->input('export') === 'csv') {
            return $this->exportStudentReportCsv($student, $attendance, $statistics);
        }
        
        return view('reports.attendance.student', compact('student', 'attendance', 'statistics', 'startDate', 'endDate'));
    }

    /**
     * Generate class-wise attendance report.
     *
     * @param  AttendanceReportRequest  $request
     * @return View
     */
    public function classReport(AttendanceReportRequest $request): View
    {
        $classId = $request->input('class_id');
        $startDate = $request->input('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());
        
        $class = ClassModel::findOrFail($classId);
        $students = Admission::where('class_id', $classId)
            ->orderBy('student_name')
            ->get();
        
        // Calculate attendance for each student
        $studentAttendance = [];
        foreach ($students as $student) {
            $attendance = Attendance::forStudent($student->id)
                ->dateRange($startDate, $endDate)
                ->get();
            
            $totalDays = $attendance->count();
            $presentDays = $attendance->filter(fn($a) => $a->isPresent())->count();
            $absentDays = $attendance->filter(fn($a) => $a->isAbsent())->count();
            
            $studentAttendance[] = [
                'student' => $student,
                'total' => $totalDays,
                'present' => $presentDays,
                'absent' => $absentDays,
                'percentage' => $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 2) : 0,
            ];
        }
        
        // Class statistics
        $classStats = [
            'total_students' => count($studentAttendance),
            'avg_attendance' => count($studentAttendance) > 0 ? round(collect($studentAttendance)->avg('percentage'), 2) : 0,
        ];
        
        if ($request->input('export') === 'csv') {
            return $this->exportClassReportCsv($class, $studentAttendance, $startDate, $endDate);
        }
        
        return view('reports.attendance.class', compact('class', 'studentAttendance', 'classStats', 'startDate', 'endDate'));
    }

    /**
     * Generate monthly attendance report.
     *
     * @param  AttendanceReportRequest  $request
     * @return View
     */
    public function monthlyReport(AttendanceReportRequest $request): View
    {
        $month = $request->input('month');
        $year = $request->input('year');
        $classId = $request->input('class_id');
        
        $startDate = Carbon::create($year, $month, 1)->startOfMonth()->toDateString();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();
        
        $classes = ClassModel::ordered()->get();
        $class = $classId ? ClassModel::findOrFail($classId) : null;
        
        // Get daily attendance statistics for the month
        $dailyStats = [];
        $currentDate = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        
        while ($currentDate <= $end) {
            $date = $currentDate->toDateString();
            
            $query = Attendance::forDate($date);
            if ($classId) {
                $query->forClass($classId);
            }
            
            $total = $query->count();
            $present = $query->whereIn('status', [Attendance::STATUS_PRESENT, Attendance::STATUS_LATE, Attendance::STATUS_HALF_DAY])->count();
            $absent = (clone $query)->where('status', Attendance::STATUS_ABSENT)->count();
            
            $dailyStats[] = [
                'date' => $date,
                'day' => $currentDate->format('D'),
                'total' => $total,
                'present' => $present,
                'absent' => $absent,
                'percentage' => $total > 0 ? round(($present / $total) * 100, 2) : 0,
            ];
            
            $currentDate->addDay();
        }
        
        // Monthly summary
        $monthlyStats = [
            'total_records' => array_sum(array_column($dailyStats, 'total')),
            'total_present' => array_sum(array_column($dailyStats, 'present')),
            'total_absent' => array_sum(array_column($dailyStats, 'absent')),
            'avg_attendance' => count($dailyStats) > 0 ? round(collect($dailyStats)->avg('percentage'), 2) : 0,
        ];
        
        return view('reports.attendance.monthly', compact('dailyStats', 'monthlyStats', 'month', 'year', 'classes', 'class'));
    }

    /**
     * Generate date range attendance report.
     *
     * @param  AttendanceReportRequest  $request
     * @return View
     */
    public function dateRangeReport(AttendanceReportRequest $request): View
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $classId = $request->input('class_id');
        
        $classes = ClassModel::ordered()->get();
        $class = $classId ? ClassModel::findOrFail($classId) : null;
        
        // Get daily attendance for the range
        $dailyStats = [];
        $currentDate = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        
        while ($currentDate <= $end) {
            $date = $currentDate->toDateString();
            
            $query = Attendance::forDate($date);
            if ($classId) {
                $query->forClass($classId);
            }
            
            $total = $query->count();
            $present = $query->whereIn('status', [Attendance::STATUS_PRESENT, Attendance::STATUS_LATE, Attendance::STATUS_HALF_DAY])->count();
            $absent = (clone $query)->where('status', Attendance::STATUS_ABSENT)->count();
            
            $dailyStats[] = [
                'date' => $date,
                'total' => $total,
                'present' => $present,
                'absent' => $absent,
                'percentage' => $total > 0 ? round(($present / $total) * 100, 2) : 0,
            ];
            
            $currentDate->addDay();
        }
        
        return view('reports.attendance.daterange', compact('dailyStats', 'startDate', 'endDate', 'classes', 'class'));
    }

    /**
     * Export student report to CSV.
     *
     * @param  Admission  $student
     * @param  \Illuminate\Support\Collection  $attendance
     * @param  array  $statistics
     * @return Response
     */
    private function exportStudentReportCsv($student, $attendance, $statistics): Response
    {
        $filename = 'attendance_' . $student->regno . '_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function () use ($student, $attendance, $statistics) {
            $file = fopen('php://output', 'w');
            
            // Student info
            fputcsv($file, ['Student Attendance Report']);
            fputcsv($file, ['Name', $student->name]);
            fputcsv($file, ['Reg No', $student->regno]);
            fputcsv($file, ['Class', $student->class->name ?? '']);
            fputcsv($file, []);
            
            // Statistics
            fputcsv($file, ['Summary']);
            fputcsv($file, ['Total Days', $statistics['total']]);
            fputcsv($file, ['Present', $statistics['present']]);
            fputcsv($file, ['Absent', $statistics['absent']]);
            fputcsv($file, ['Late', $statistics['late']]);
            fputcsv($file, ['Leave', $statistics['leave']]);
            fputcsv($file, ['Attendance %', $statistics['percentage'] . '%']);
            fputcsv($file, []);
            
            // Attendance records
            fputcsv($file, ['Date', 'Status', 'In Time', 'Out Time', 'Remarks']);
            foreach ($attendance as $record) {
                fputcsv($file, [
                    $record->date->format('Y-m-d'),
                    $record->status_text,
                    $record->in_time ? $record->in_time->format('H:i') : '',
                    $record->out_time ? $record->out_time->format('H:i') : '',
                    $record->remarks ?? '',
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export class report to CSV.
     *
     * @param  ClassModel  $class
     * @param  array  $studentAttendance
     * @param  string  $startDate
     * @param  string  $endDate
     * @return Response
     */
    private function exportClassReportCsv($class, $studentAttendance, $startDate, $endDate): Response
    {
        $filename = 'attendance_class_' . $class->name . '_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function () use ($class, $studentAttendance, $startDate, $endDate) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, ['Class Attendance Report']);
            fputcsv($file, ['Class', $class->name]);
            fputcsv($file, ['Period', $startDate . ' to ' . $endDate]);
            fputcsv($file, []);
            
            // Student records
            fputcsv($file, ['Reg No', 'Name', 'Total Days', 'Present', 'Absent', 'Attendance %']);
            foreach ($studentAttendance as $data) {
                fputcsv($file, [
                    $data['student']->regno,
                    $data['student']->name,
                    $data['total'],
                    $data['present'],
                    $data['absent'],
                    $data['percentage'] . '%',
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
