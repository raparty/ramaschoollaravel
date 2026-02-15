<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Mark Model
 * 
 * Represents marks obtained by a student in an exam subject
 * Matches migration schema: database/migrations/2026_02_14_072514_create_core_tables.php
 * Table: student_marks
 * 
 * @property int $id
 * @property int $admission_id Student (admission) ID
 * @property int $exam_subject_id Exam subject ID (links to exam, class, subject)
 * @property decimal $marks_obtained Marks obtained by student
 * @property string|null $grade Grade (A+, A, B+, B, C+, C, D, F)
 * @property boolean $is_absent Whether student was absent
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Mark extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'student_marks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'admission_id',
        'exam_subject_id',
        'marks_obtained',
        'grade',
        'is_absent',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'admission_id' => 'integer',
        'exam_subject_id' => 'integer',
        'marks_obtained' => 'decimal:2',
        'is_absent' => 'boolean',
    ];

    /**
     * Get the student (admission) that this mark belongs to.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Admission::class, 'admission_id');
    }

    /**
     * Get the exam subject that this mark belongs to.
     */
    public function examSubject(): BelongsTo
    {
        return $this->belongsTo(ExamSubject::class);
    }

    /**
     * Get percentage based on exam subject max marks.
     */
    public function getPercentageAttribute(): float
    {
        if (!$this->examSubject || $this->examSubject->max_marks == 0) {
            return 0;
        }
        
        return ($this->marks_obtained / $this->examSubject->max_marks) * 100;
    }

    /**
     * Check if student passed (using exam subject pass marks).
     */
    public function isPassed(): bool
    {
        if ($this->is_absent) {
            return false;
        }
        
        if (!$this->examSubject) {
            return false;
        }
        
        return $this->marks_obtained >= $this->examSubject->pass_marks;
    }

    /**
     * Scope to get marks for a specific student.
     */
    public function scopeForStudent($query, int $admissionId)
    {
        return $query->where('admission_id', $admissionId);
    }

    /**
     * Scope to get marks for a specific exam subject.
     */
    public function scopeForExamSubject($query, int $examSubjectId)
    {
        return $query->where('exam_subject_id', $examSubjectId);
    }

    /**
     * Scope to get marks where student was present.
     */
    public function scopePresent($query)
    {
        return $query->where('is_absent', false);
    }

    /**
     * Scope to get marks where student was absent.
     */
    public function scopeAbsent($query)
    {
        return $query->where('is_absent', true);
    }
}
