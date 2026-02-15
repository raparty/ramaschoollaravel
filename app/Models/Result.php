<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Result Model
 * 
 * ⚠️ WARNING: THIS MODEL IS NON-FUNCTIONAL ⚠️
 * The 'results' table does NOT exist in the database migration.
 * To enable this model, execute: database/schema/missing-tables.sql
 * 
 * Represents compiled result of a student for an exam
 * 
 * @property int $id
 * @property int $student_id Related student (admission) ID
 * @property int $exam_id Related exam ID
 * @property float $total_marks_obtained Total marks obtained
 * @property int $total_max_marks Total maximum marks
 * @property float $percentage Percentage obtained
 * @property string|null $grade Grade (A+, A, B, C, etc.)
 * @property int|null $rank Rank in class
 * @property boolean $is_passed Whether student passed
 * @property boolean $is_published Whether result is published
 * @property string|null $remarks Overall remarks
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Result extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'student_id',
        'exam_id',
        'total_marks_obtained',
        'total_max_marks',
        'percentage',
        'grade',
        'rank',
        'is_passed',
        'is_published',
        'remarks',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'student_id' => 'integer',
        'exam_id' => 'integer',
        'total_marks_obtained' => 'float',
        'total_max_marks' => 'integer',
        'percentage' => 'float',
        'rank' => 'integer',
        'is_passed' => 'boolean',
        'is_published' => 'boolean',
    ];

    /**
     * Get the student that this result belongs to.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Admission::class, 'student_id');
    }

    /**
     * Get the exam that this result belongs to.
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Calculate percentage from marks.
     */
    public function calculatePercentage(): float
    {
        if ($this->total_max_marks == 0) {
            return 0;
        }
        
        return round(($this->total_marks_obtained / $this->total_max_marks) * 100, 2);
    }

    /**
     * Determine grade based on percentage.
     */
    public function determineGrade(): string
    {
        $percentage = $this->percentage;
        
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
     * Get grade badge class.
     */
    public function getGradeBadgeAttribute(): string
    {
        return match ($this->grade) {
            'A+', 'A' => 'success',
            'B+', 'B' => 'primary',
            'C+', 'C' => 'info',
            'D' => 'warning',
            'F' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeAttribute(): string
    {
        return $this->is_passed ? 'success' : 'danger';
    }

    /**
     * Get status text.
     */
    public function getStatusTextAttribute(): string
    {
        return $this->is_passed ? 'Pass' : 'Fail';
    }

    /**
     * Get formatted percentage.
     */
    public function getFormattedPercentageAttribute(): string
    {
        return number_format($this->percentage, 2) . '%';
    }

    /**
     * Scope to get published results.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope to get unpublished results.
     */
    public function scopeUnpublished($query)
    {
        return $query->where('is_published', false);
    }

    /**
     * Scope to get passed results.
     */
    public function scopePassed($query)
    {
        return $query->where('is_passed', true);
    }

    /**
     * Scope to get failed results.
     */
    public function scopeFailed($query)
    {
        return $query->where('is_passed', false);
    }

    /**
     * Scope to get results for a specific exam.
     */
    public function scopeForExam($query, int $examId)
    {
        return $query->where('exam_id', $examId);
    }

    /**
     * Scope to get results for a specific student.
     */
    public function scopeForStudent($query, int $studentId)
    {
        return $query->where('student_id', $studentId);
    }
}
