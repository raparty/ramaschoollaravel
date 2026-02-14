<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

/**
 * Book Issue Model
 * 
 * Represents book issues to students
 * 
 * @property int $id
 * @property string $registration_no
 * @property int $book_id
 * @property string $issue_date
 * @property string $return_date
 * @property string $due_date
 * @property string $issue_by
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class BookIssue extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'student_books';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'registration_no',
        'book_id',
        'issue_date',
        'return_date',
        'due_date',
        'issue_by',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'issue_date' => 'date',
        'return_date' => 'date',
        'due_date' => 'date',
    ];

    /**
     * Get the student who issued this book.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Admission::class, 'registration_no', 'registration_no');
    }

    /**
     * Get the book that was issued.
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    /**
     * Check if book is overdue.
     */
    public function isOverdue(): bool
    {
        if ($this->return_date) {
            return false; // Already returned
        }

        return Carbon::parse($this->due_date)->isPast();
    }

    /**
     * Get number of days overdue.
     */
    public function getDaysOverdueAttribute(): int
    {
        if (!$this->isOverdue()) {
            return 0;
        }

        return Carbon::parse($this->due_date)->diffInDays(Carbon::now());
    }

    /**
     * Calculate fine amount (Rs. 5 per day).
     */
    public function calculateFine(float $finePerDay = 5.00): float
    {
        return $this->days_overdue * $finePerDay;
    }

    /**
     * Scope: Active issues (not returned).
     */
    public function scopeActive($query)
    {
        return $query->whereNull('return_date');
    }

    /**
     * Scope: Returned issues.
     */
    public function scopeReturned($query)
    {
        return $query->whereNotNull('return_date');
    }

    /**
     * Scope: Overdue issues.
     */
    public function scopeOverdue($query)
    {
        return $query->whereNull('return_date')
            ->where('due_date', '<', Carbon::now());
    }

    /**
     * Scope: For specific student.
     */
    public function scopeForStudent($query, string $regNo)
    {
        return $query->where('registration_no', $regNo);
    }

    /**
     * Get formatted issue date.
     */
    public function getFormattedIssueDateAttribute(): string
    {
        return $this->issue_date ? $this->issue_date->format('d-M-Y') : '';
    }

    /**
     * Get formatted return date.
     */
    public function getFormattedReturnDateAttribute(): string
    {
        return $this->return_date ? $this->return_date->format('d-M-Y') : 'Not Returned';
    }
}
