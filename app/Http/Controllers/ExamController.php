<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExamRequest;
use App\Models\Exam;
use App\Models\ExamSubject;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

/**
 * ExamController
 * 
 * Handles exam schedule management
 */
class ExamController extends Controller
{
    /**
     * Error message for missing database tables/migrations
     */
    private const MISSING_TABLE_ERROR_MESSAGE = 'Database tables are not set up. Please run migrations first using: php artisan migrate';

    /**
     * Check if a QueryException is due to missing tables
     */
    private function isMissingTableError(QueryException $e): bool
    {
        // MySQL error code 1146: Table doesn't exist
        // PDO error codes are returned as strings
        // Also check for message patterns for other database drivers
        return $e->getCode() === '1146' 
            || $e->getCode() === 1146
            || str_contains($e->getMessage(), "doesn't exist") 
            || str_contains($e->getMessage(), 'Base table or view not found');
    }
    /**
     * Display a listing of exams.
     */
    public function index(Request $request)
    {
        try {
            $query = Exam::with('term')->latest();

            // Filter by term
            if ($request->filled('term_id')) {
                $query->forTerm($request->term_id);
            }

            $exams = $query->paginate(15);
            $terms = \App\Models\Term::orderBy('start_date', 'desc')->get();

            return view('exams.index', compact('exams', 'terms'));
        } catch (QueryException $e) {
            if ($this->isMissingTableError($e)) {
                return redirect()->route('dashboard')->with('error', self::MISSING_TABLE_ERROR_MESSAGE);
            }
            
            throw $e;
        }
    }

    /**
     * Show the form for creating a new exam.
     */
    public function create()
    {
        try {
            $terms = \App\Models\Term::orderBy('start_date', 'desc')->get();
            $classes = ClassModel::ordered()->get();

            return view('exams.create', compact('terms', 'classes'));
        } catch (QueryException $e) {
            if ($this->isMissingTableError($e)) {
                return redirect()->route('dashboard')->with('error', self::MISSING_TABLE_ERROR_MESSAGE);
            }
            
            throw $e;
        }
    }

    /**
     * Store a newly created exam.
     */
    public function store(StoreExamRequest $request)
    {
        try {
            DB::beginTransaction();

            $exam = Exam::create($request->validated());

            DB::commit();

            return redirect()
                ->route('exams.show', $exam)
                ->with('success', 'Exam created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to create exam: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified exam.
     */
    public function show(Exam $exam)
    {
        $exam->load(['term', 'examSubjects.classModel', 'examSubjects.subject']);
        
        return view('exams.show', compact('exam'));
    }

    /**
     * Show the form for editing the specified exam.
     */
    public function edit(Exam $exam)
    {
        try {
            $terms = \App\Models\Term::orderBy('start_date', 'desc')->get();
            $classes = ClassModel::ordered()->get();

            return view('exams.edit', compact('exam', 'terms', 'classes'));
        } catch (QueryException $e) {
            if ($this->isMissingTableError($e)) {
                return redirect()->route('dashboard')->with('error', self::MISSING_TABLE_ERROR_MESSAGE);
            }
            
            throw $e;
        }
    }

    /**
     * Update the specified exam.
     */
    public function update(StoreExamRequest $request, Exam $exam)
    {
        try {
            DB::beginTransaction();

            $exam->update($request->validated());

            DB::commit();

            return redirect()
                ->route('exams.show', $exam)
                ->with('success', 'Exam updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to update exam: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified exam.
     */
    public function destroy(Exam $exam)
    {
        try {
            // Check if exam has results
            if ($exam->results()->exists()) {
                return back()->with('error', 'Cannot delete exam with existing results.');
            }

            $exam->delete();

            return redirect()
                ->route('exams.index')
                ->with('success', 'Exam deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete exam: ' . $e->getMessage());
        }
    }

    /**
     * Show form to assign subjects to exam.
     */
    public function assignSubjects(Exam $exam)
    {
        $exam->load('examSubjects');
        
        return view('exams.subjects', compact('exam'));
    }

    /**
     * Store exam subject assignments.
     */
    public function storeSubjects(Request $request, Exam $exam)
    {
        $request->validate([
            'subjects' => 'required|array',
            'subjects.*.subject_id' => 'required|integer',
            'subjects.*.theory_marks' => 'required|integer|min:0',
            'subjects.*.practical_marks' => 'nullable|integer|min:0',
            'subjects.*.pass_marks' => 'required|integer|min:0',
            'subjects.*.exam_date' => 'nullable|date',
            'subjects.*.exam_time' => 'nullable',
            'subjects.*.duration_minutes' => 'nullable|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Delete existing subjects
            $exam->examSubjects()->delete();

            // Add new subjects
            foreach ($request->subjects as $subject) {
                ExamSubject::create([
                    'exam_id' => $exam->id,
                    'subject_id' => $subject['subject_id'],
                    'theory_marks' => $subject['theory_marks'],
                    'practical_marks' => $subject['practical_marks'] ?? 0,
                    'pass_marks' => $subject['pass_marks'],
                    'exam_date' => $subject['exam_date'] ?? null,
                    'exam_time' => $subject['exam_time'] ?? null,
                    'duration_minutes' => $subject['duration_minutes'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('exams.show', $exam)
                ->with('success', 'Subjects assigned successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to assign subjects: ' . $e->getMessage());
        }
    }

    /**
     * Show exam timetable.
     */
    public function timetable(Exam $exam)
    {
        $exam->load('examSubjects');

        return view('exams.timetable', compact('exam'));
    }

    /**
     * Publish or unpublish exam results.
     * 
     * ⚠️ DISABLED - is_published column doesn't exist in database
     * Uncomment after adding is_published column to exams table
     */
    /*
    public function togglePublish(Exam $exam)
    {
        try {
            $exam->update([
                'is_published' => !$exam->is_published
            ]);

            // Also update all results for this exam
            $exam->results()->update([
                'is_published' => $exam->is_published
            ]);

            $status = $exam->is_published ? 'published' : 'unpublished';

            return back()->with('success', "Exam results {$status} successfully.");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update publish status: ' . $e->getMessage());
        }
    }
    */
}
