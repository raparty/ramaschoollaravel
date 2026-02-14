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
 * @property string $student_id User ID of the student
 * @property string $subject_name Subject name
 * @property string $exam_type Exam type (Unit Test/Mid-Term/Final)
 * @property int $marks_obtained Marks obtained
 * @property int $total_marks Total marks (default 100)
 * @property string $academic_year Academic year
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
        'subject_name',
        'exam_type',
        'marks_obtained',
        'total_marks',
        'academic_year',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'marks_obtained' => 'integer',
        'total_marks' => 'integer',
    ];
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the student (user) that this mark belongs to.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id', 'user_id');
    }

    /**
     * Get percentage.
     */
    public function getPercentageAttribute(): float
    {
        return $this->total_marks > 0 ? ($this->marks_obtained / $this->total_marks) * 100 : 0;
    }

    /**
     * Check if student passed (assuming 40% pass mark).
     */
    public function isPassed(float $passPercentage = 40.0): bool
    {
        return $this->percentage >= $passPercentage;
    }

    /**
     * Scope to get marks for a specific student.
     */
    public function scopeForStudent($query, string $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope to get marks for a specific exam type.
     */
    public function scopeForExamType($query, string $examType)
    {
        return $query->where('exam_type', $examType);
    }
    
    /**
     * Scope to get marks for a specific academic year.
     */
    public function scopeForAcademicYear($query, string $year)
    {
        return $query->where('academic_year', $year);
    }
    
    /**
     * Scope to get marks for a specific subject.
     */
    public function scopeForSubject($query, string $subjectName)
    {
        return $query->where('subject_name', $subjectName);
    }
}
