<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateResultRequest;
use App\Models\Result;
use App\Models\Exam;
use App\Models\Mark;
use App\Models\Admission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * ResultController
 * 
 * Handles result generation and management
 */
class ResultController extends Controller
{
    /**
     * Display results list.
     */
    public function index(Request $request)
    {
        $query = Result::with(['student', 'exam'])->latest();

        // Filter by exam
        if ($request->filled('exam_id')) {
            $query->forExam($request->exam_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'published') {
                $query->published();
            } elseif ($request->status === 'unpublished') {
                $query->unpublished();
            }
        }

        $results = $query->paginate(20);
        $exams = Exam::latest()->get();

        return view('results.index', compact('results', 'exams'));
    }

    /**
     * Show result generation form.
     */
    public function generateForm()
    {
        $exams = Exam::with('class')->latest()->get();

        return view('results.generate', compact('exams'));
    }

    /**
     * Generate results for an exam.
     */
    public function generate(GenerateResultRequest $request)
    {
        try {
            DB::beginTransaction();

            $exam = Exam::with('examSubjects')->findOrFail($request->exam_id);

            // Get students - either specific IDs or all students in class
            if ($request->filled('student_ids')) {
                $students = Admission::whereIn('id', $request->student_ids)->get();
            } else {
                $students = Admission::where('class_id', $exam->class_id)
                    ->where('status', 'active')
                    ->get();
            }

            $resultsGenerated = 0;

            foreach ($students as $student) {
                // Get all marks for this student in this exam
                $marks = Mark::whereIn('exam_subject_id', $exam->examSubjects->pluck('id'))
                    ->where('student_id', $student->id)
                    ->get();

                // Check if all marks are entered
                if ($marks->count() !== $exam->examSubjects->count()) {
                    continue; // Skip students with incomplete marks
                }

                // Calculate totals
                $totalMarksObtained = $marks->sum('total_marks');
                $totalMaxMarks = $exam->examSubjects->sum('total_marks');

                // Calculate percentage
                $percentage = $totalMaxMarks > 0 
                    ? round(($totalMarksObtained / $totalMaxMarks) * 100, 2) 
                    : 0;

                // Determine grade
                $grade = $this->calculateGrade($percentage);

                // Determine if passed (no subject should be failed)
                $isPassed = true;
                foreach ($marks as $mark) {
                    if (!$mark->isPassed()) {
                        $isPassed = false;
                        break;
                    }
                }

                // Create or update result
                Result::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'exam_id' => $exam->id,
                    ],
                    [
                        'total_marks_obtained' => $totalMarksObtained,
                        'total_max_marks' => $totalMaxMarks,
                        'percentage' => $percentage,
                        'grade' => $grade,
                        'is_passed' => $isPassed,
                        'is_published' => $request->boolean('publish'),
                    ]
                );

                $resultsGenerated++;
            }

            // Calculate ranks for this exam
            $this->calculateRanks($exam);

            DB::commit();

            return redirect()
                ->route('results.index')
                ->with('success', "Results generated for {$resultsGenerated} students.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to generate results: ' . $e->getMessage());
        }
    }

    /**
     * View a specific result/marksheet.
     */
    public function view(Result $result)
    {
        $result->load(['student', 'exam.examSubjects']);

        // Get marks for this result
        $marks = Mark::with('examSubject')
            ->where('student_id', $result->student_id)
            ->whereIn('exam_subject_id', $result->exam->examSubjects->pluck('id'))
            ->get();

        return view('results.marksheet', compact('result', 'marks'));
    }

    /**
     * View class results.
     */
    public function classResults(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
        ]);

        $exam = Exam::with('class')->findOrFail($request->exam_id);
        
        $results = Result::with('student')
            ->forExam($exam->id)
            ->orderBy('rank')
            ->get();

        // Calculate statistics
        $statistics = [
            'total_students' => $results->count(),
            'passed' => $results->where('is_passed', true)->count(),
            'failed' => $results->where('is_passed', false)->count(),
            'highest_percentage' => $results->max('percentage'),
            'lowest_percentage' => $results->min('percentage'),
            'average_percentage' => $results->avg('percentage'),
            'highest_marks' => $results->max('total_marks_obtained'),
        ];

        return view('results.class', compact('exam', 'results', 'statistics'));
    }

    /**
     * Publish or unpublish a specific result.
     */
    public function togglePublish(Result $result)
    {
        try {
            $result->update([
                'is_published' => !$result->is_published
            ]);

            $status = $result->is_published ? 'published' : 'unpublished';

            return back()->with('success', "Result {$status} successfully.");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update publish status: ' . $e->getMessage());
        }
    }

    /**
     * Calculate grade based on percentage.
     *
     * @param float $percentage
     * @return string
     */
    private function calculateGrade(float $percentage): string
    {
        if ($percentage >= 90) {
            return 'A+';
        } elseif ($percentage >= 80) {
            return 'A';
        } elseif ($percentage >= 70) {
            return 'B+';
        } elseif ($percentage >= 60) {
            return 'B';
        } elseif ($percentage >= 50) {
            return 'C+';
        } elseif ($percentage >= 40) {
            return 'C';
        } elseif ($percentage >= 33) {
            return 'D';
        } else {
            return 'F';
        }
    }

    /**
     * Calculate ranks for an exam.
     *
     * @param Exam $exam
     */
    private function calculateRanks(Exam $exam): void
    {
        $results = Result::forExam($exam->id)
            ->orderBy('percentage', 'desc')
            ->get();

        $rank = 1;
        foreach ($results as $result) {
            $result->update(['rank' => $rank]);
            $rank++;
        }
    }
}
