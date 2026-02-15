<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMarkRequest;
use App\Models\Mark;
use App\Models\Exam;
use App\Models\ExamSubject;
use App\Models\Admission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * MarkController
 * 
 * Handles mark entry and management
 */
class MarkController extends Controller
{
    /**
     * Display mark entry dashboard.
     */
    public function index(Request $request)
    {
        $exams = Exam::with('class')->latest()->get();

        return view('marks.index', compact('exams'));
    }

    /**
     * Show mark entry form.
     */
    public function entryForm(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'exam_subject_id' => 'required|exists:exam_subjects,id',
        ]);

        $examSubject = ExamSubject::with(['exam.class'])->findOrFail($request->exam_subject_id);
        $exam = $examSubject->exam;

        // Get students for this class
        $students = Admission::where('class_id', $exam->class_id)
            ->with(['marks' => function ($query) use ($request) {
                $query->where('exam_subject_id', $request->exam_subject_id);
            }])
            ->orderBy('student_name')
            ->get();

        return view('marks.entry', compact('examSubject', 'students', 'exam'));
    }

    /**
     * Store or update marks.
     */
    public function store(Request $request)
    {
        $request->validate([
            'exam_subject_id' => 'required|exists:exam_subjects,id',
            'marks' => 'required|array',
            'marks.*.student_id' => 'required|exists:admissions,id',
            'marks.*.theory_marks' => 'nullable|numeric|min:0',
            'marks.*.practical_marks' => 'nullable|numeric|min:0',
            'marks.*.is_absent' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            $examSubject = ExamSubject::findOrFail($request->exam_subject_id);

            foreach ($request->marks as $markData) {
                $isAbsent = isset($markData['is_absent']) && $markData['is_absent'];

                $data = [
                    'student_id' => $markData['student_id'],
                    'exam_subject_id' => $request->exam_subject_id,
                    'theory_marks' => $isAbsent ? 0 : ($markData['theory_marks'] ?? 0),
                    'practical_marks' => $isAbsent ? 0 : ($markData['practical_marks'] ?? 0),
                    'is_absent' => $isAbsent,
                    'remarks' => $markData['remarks'] ?? null,
                ];

                // Validate marks don't exceed maximum
                if (!$isAbsent) {
                    if ($data['theory_marks'] > $examSubject->theory_marks) {
                        throw new \Exception("Theory marks cannot exceed {$examSubject->theory_marks}");
                    }
                    if ($data['practical_marks'] > $examSubject->practical_marks) {
                        throw new \Exception("Practical marks cannot exceed {$examSubject->practical_marks}");
                    }
                }

                Mark::updateOrCreate(
                    [
                        'student_id' => $data['student_id'],
                        'exam_subject_id' => $request->exam_subject_id,
                    ],
                    $data
                );
            }

            DB::commit();

            return redirect()
                ->back()
                ->with('success', 'Marks saved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to save marks: ' . $e->getMessage());
        }
    }

    /**
     * View student's marks for an exam.
     */
    public function studentMarks(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:admissions,id',
            'exam_id' => 'required|exists:exams,id',
        ]);

        $student = Admission::findOrFail($request->student_id);
        $exam = Exam::with('examSubjects')->findOrFail($request->exam_id);

        $marks = Mark::with('examSubject')
            ->where('student_id', $request->student_id)
            ->whereHas('examSubject', function ($query) use ($request) {
                $query->where('exam_id', $request->exam_id);
            })
            ->get();

        return view('marks.student', compact('student', 'exam', 'marks'));
    }

    /**
     * View marks by subject.
     */
    public function subjectMarks(ExamSubject $examSubject)
    {
        $examSubject->load(['exam.class', 'marks.student']);

        $marks = $examSubject->marks()
            ->with('student')
            ->orderBy('theory_marks', 'desc')
            ->get();

        // Calculate statistics
        $statistics = [
            'total_students' => $marks->count(),
            'present' => $marks->where('is_absent', false)->count(),
            'absent' => $marks->where('is_absent', true)->count(),
            'passed' => $marks->where('is_absent', false)->filter(function ($mark) {
                return $mark->isPassed();
            })->count(),
            'failed' => $marks->where('is_absent', false)->filter(function ($mark) {
                return !$mark->isPassed();
            })->count(),
            'highest' => $marks->where('is_absent', false)->max('total_marks'),
            'lowest' => $marks->where('is_absent', false)->min('total_marks'),
            'average' => $marks->where('is_absent', false)->avg('total_marks'),
        ];

        return view('marks.subject', compact('examSubject', 'marks', 'statistics'));
    }

    /**
     * Search students for mark entry.
     */
    public function searchStudents(Request $request)
    {
        $query = Admission::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('student_name', 'like', "%{$search}%")
                  ->orWhere('reg_no', 'like', "%{$search}%");
            });
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        $students = $query->limit(20)->get();

        return response()->json($students);
    }
}
