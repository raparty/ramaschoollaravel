<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Exam Model
 * 
 * Represents examination schedules and details
 * Matches migration schema: database/migrations/2026_02_14_072514_create_core_tables.php
 * 
 * @property int $id
 * @property string $name Exam name (Midterm, Final, Unit Test 1, etc.)
 * @property int $term_id Related term ID (academic term/year)
 * @property date $start_date Exam start date
 * @property date $end_date Exam end date
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Exam extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'term_id',
        'start_date',
        'end_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'term_id' => 'integer',
    ];

    /**
     * Get the term that this exam belongs to.
     */
    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class, 'term_id');
    }

    /**
     * Get the subjects assigned to this exam.
     */
    public function examSubjects(): HasMany
    {
        return $this->hasMany(ExamSubject::class);
    }

    /**
     * Scope to get exams for a specific term.
     */
    public function scopeForTerm($query, int $termId)
    {
        return $query->where('term_id', $termId);
    }

    /**
     * Get exam status badge class.
     */
    public function getStatusBadgeAttribute(): string
    {
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
        if ($this->end_date < now()) {
            return 'Completed';
        }
        
        if ($this->start_date > now()) {
            return 'Upcoming';
        }
        
        return 'Ongoing';
    }
}
