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
 * Maps to student_books_details table in database
 * 
 * @property int $id
 * @property string $registration_no
 * @property string $book_number
 * @property date $issue_date
 * @property date|null $return_date
 * @property string $booking_status
 * @property string $session
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class BookIssue extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'student_books_details';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'registration_no',
        'book_number',
        'issue_date',
        'return_date',
        'booking_status',
        'session',
    ];
    
    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

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
        return $this->belongsTo(Admission::class, 'registration_no', 'reg_no');
    }

    /**
     * Get the book that was issued.
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class, 'book_number', 'book_number');
    }

    /**
     * Check if book is still issued.
     */
    public function isIssued(): bool
    {
        return $this->booking_status === '1' && is_null($this->return_date);
    }
    
    /**
     * Check if book is returned.
     */
    public function isReturned(): bool
    {
        return $this->booking_status === '0' || !is_null($this->return_date);
    }

    /**
     * Scope: Active issues (not returned).
     */
    public function scopeActive($query)
    {
        return $query->where('booking_status', '1')->whereNull('return_date');
    }

    /**
     * Scope: Returned issues.
     */
    public function scopeReturned($query)
    {
        return $query->where('booking_status', '0')->orWhereNotNull('return_date');
    }

    /**
     * Scope: For specific student.
     */
    public function scopeForStudent($query, string $regNo)
    {
        return $query->where('registration_no', $regNo);
    }
    
    /**
     * Scope: For specific session.
     */
    public function scopeForSession($query, string $session)
    {
        return $query->where('session', $session);
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
