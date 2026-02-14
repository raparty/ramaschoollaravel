<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * ExamSubject Model
 * 
 * Represents subjects assigned to an exam with maximum marks
 * 
 * @property int $id
 * @property int $exam_id Related exam ID
 * @property int $subject_id Related subject ID (from classes_subjects or subjects table)
 * @property int $theory_marks Maximum theory marks
 * @property int $practical_marks Maximum practical marks
 * @property int $pass_marks Minimum pass marks for this subject
 * @property date|null $exam_date Exam date for this subject
 * @property time|null $exam_time Exam time
 * @property int|null $duration_minutes Exam duration in minutes
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class ExamSubject extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'exam_id',
        'subject_id',
        'theory_marks',
        'practical_marks',
        'pass_marks',
        'exam_date',
        'exam_time',
        'duration_minutes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'exam_id' => 'integer',
        'subject_id' => 'integer',
        'theory_marks' => 'integer',
        'practical_marks' => 'integer',
        'pass_marks' => 'integer',
        'duration_minutes' => 'integer',
        'exam_date' => 'date',
    ];

    /**
     * Get the exam that this subject belongs to.
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Get the marks for this exam subject.
     */
    public function marks(): HasMany
    {
        return $this->hasMany(Mark::class);
    }

    /**
     * Get total maximum marks (theory + practical).
     */
    public function getTotalMarksAttribute(): int
    {
        return $this->theory_marks + $this->practical_marks;
    }

    /**
     * Get formatted exam date and time.
     */
    public function getFormattedDateTimeAttribute(): string
    {
        if (!$this->exam_date) {
            return 'Not Scheduled';
        }
        
        $formatted = $this->exam_date->format('d M Y');
        
        if ($this->exam_time) {
            $formatted .= ' at ' . date('h:i A', strtotime($this->exam_time));
        }
        
        return $formatted;
    }

    /**
     * Check if practical marks are included.
     */
    public function hasPractical(): bool
    {
        return $this->practical_marks > 0;
    }
}
