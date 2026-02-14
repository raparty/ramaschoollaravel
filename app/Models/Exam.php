<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Exam Model
 * 
 * Represents examination schedules and details
 * 
 * @property int $id
 * @property string $name Exam name (Midterm, Final, Unit Test 1, etc.)
 * @property int $class_id Related class ID
 * @property string $session Academic session (e.g., 2023-2024)
 * @property date $start_date Exam start date
 * @property date $end_date Exam end date
 * @property int $total_marks Total maximum marks
 * @property int $pass_marks Minimum pass marks
 * @property boolean $is_published Whether results are published
 * @property string|null $description Exam description
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 */
class Exam extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'class_id',
        'session',
        'start_date',
        'end_date',
        'total_marks',
        'pass_marks',
        'is_published',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_published' => 'boolean',
        'total_marks' => 'integer',
        'pass_marks' => 'integer',
    ];

    /**
     * Get the class that this exam belongs to.
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    /**
     * Get the subjects assigned to this exam.
     */
    public function examSubjects(): HasMany
    {
        return $this->hasMany(ExamSubject::class);
    }

    /**
     * Get the results for this exam.
     */
    public function results(): HasMany
    {
        return $this->hasMany(Result::class);
    }

    /**
     * Scope to get published exams.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope to get unpublished exams.
     */
    public function scopeUnpublished($query)
    {
        return $query->where('is_published', false);
    }

    /**
     * Scope to get exams for a specific session.
     */
    public function scopeForSession($query, string $session)
    {
        return $query->where('session', $session);
    }

    /**
     * Scope to get exams for a specific class.
     */
    public function scopeForClass($query, int $classId)
    {
        return $query->where('class_id', $classId);
    }

    /**
     * Get exam status badge class.
     */
    public function getStatusBadgeAttribute(): string
    {
        if ($this->is_published) {
            return 'success';
        }
        
        if ($this->end_date < now()) {
            return 'warning';
        }
        
        return 'info';
    }

    /**
     * Get exam status text.
     */
    public function getStatusTextAttribute(): string
    {
        if ($this->is_published) {
            return 'Published';
        }
        
        if ($this->end_date < now()) {
            return 'Completed';
        }
        
        if ($this->start_date > now()) {
            return 'Upcoming';
        }
        
        return 'Ongoing';
    }
}
