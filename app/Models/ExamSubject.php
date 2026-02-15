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
 * Matches migration schema: database/migrations/2026_02_14_072514_create_core_tables.php
 * 
 * @property int $id
 * @property int $exam_id Related exam ID
 * @property int $class_id Related class ID
 * @property int $subject_id Related subject ID
 * @property int $max_marks Maximum marks for this subject in exam
 * @property int $pass_marks Minimum pass marks for this subject
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
        'class_id',
        'subject_id',
        'max_marks',
        'pass_marks',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'exam_id' => 'integer',
        'class_id' => 'integer',
        'subject_id' => 'integer',
        'max_marks' => 'integer',
        'pass_marks' => 'integer',
    ];

    /**
     * Get the exam that this subject belongs to.
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Get the class that this exam subject belongs to.
     */
    public function classModel(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    /**
     * Get the subject details.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the marks for this exam subject.
     */
    public function marks(): HasMany
    {
        return $this->hasMany(Mark::class);
    }
}
