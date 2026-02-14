<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Mark Model
 * 
 * Represents marks obtained by a student in an exam subject
 * 
 * @property int $id
 * @property int $student_id Related student (admission) ID
 * @property int $exam_subject_id Related exam subject ID
 * @property float $theory_marks Theory marks obtained
 * @property float $practical_marks Practical marks obtained
 * @property boolean $is_absent Whether student was absent
 * @property string|null $remarks Teacher remarks
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Mark extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'student_id',
        'exam_subject_id',
        'theory_marks',
        'practical_marks',
        'is_absent',
        'remarks',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'student_id' => 'integer',
        'exam_subject_id' => 'integer',
        'theory_marks' => 'float',
        'practical_marks' => 'float',
        'is_absent' => 'boolean',
    ];

    /**
     * Get the student that this mark belongs to.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Admission::class, 'student_id');
    }

    /**
     * Get the exam subject that this mark belongs to.
     */
    public function examSubject(): BelongsTo
    {
        return $this->belongsTo(ExamSubject::class);
    }

    /**
     * Get total marks obtained (theory + practical).
     */
    public function getTotalMarksAttribute(): float
    {
        if ($this->is_absent) {
            return 0;
        }
        
        return $this->theory_marks + $this->practical_marks;
    }

    /**
     * Get marks display (shows 'AB' if absent).
     */
    public function getMarksDisplayAttribute(): string
    {
        if ($this->is_absent) {
            return 'AB';
        }
        
        return (string) $this->total_marks;
    }

    /**
     * Check if student passed this subject.
     */
    public function isPassed(): bool
    {
        if ($this->is_absent) {
            return false;
        }
        
        return $this->total_marks >= $this->examSubject->pass_marks;
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeAttribute(): string
    {
        if ($this->is_absent) {
            return 'secondary';
        }
        
        return $this->isPassed() ? 'success' : 'danger';
    }

    /**
     * Get status text.
     */
    public function getStatusTextAttribute(): string
    {
        if ($this->is_absent) {
            return 'Absent';
        }
        
        return $this->isPassed() ? 'Pass' : 'Fail';
    }

    /**
     * Scope to get marks for a specific student.
     */
    public function scopeForStudent($query, int $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope to get marks for a specific exam.
     */
    public function scopeForExam($query, int $examId)
    {
        return $query->whereHas('examSubject', function ($q) use ($examId) {
            $q->where('exam_id', $examId);
        });
    }

    /**
     * Scope to get absent marks.
     */
    public function scopeAbsent($query)
    {
        return $query->where('is_absent', true);
    }

    /**
     * Scope to get present marks.
     */
    public function scopePresent($query)
    {
        return $query->where('is_absent', false);
    }
}
