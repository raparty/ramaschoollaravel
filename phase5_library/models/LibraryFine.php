<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Library Fine Model
 * 
 * Represents fines collected from students for late book returns
 * 
 * @property int $id
 * @property string $registration_no
 * @property int $book_issue_id
 * @property float $fine_amount
 * @property int $days_overdue
 * @property string $payment_date
 * @property string $collected_by
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class LibraryFine extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'library_fines';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'registration_no',
        'book_issue_id',
        'fine_amount',
        'days_overdue',
        'payment_date',
        'collected_by',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'fine_amount' => 'decimal:2',
        'days_overdue' => 'integer',
        'payment_date' => 'date',
    ];

    /**
     * Get the student who paid the fine.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Admission::class, 'registration_no', 'registration_no');
    }

    /**
     * Get the book issue related to this fine.
     */
    public function bookIssue(): BelongsTo
    {
        return $this->belongsTo(BookIssue::class, 'book_issue_id');
    }

    /**
     * Get formatted fine amount.
     */
    public function getFormattedAmountAttribute(): string
    {
        return '₹' . number_format($this->fine_amount, 2);
    }

    /**
     * Scope: For specific student.
     */
    public function scopeForStudent($query, string $regNo)
    {
        return $query->where('registration_no', $regNo);
    }

    /**
     * Scope: Recent fines first.
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('payment_date', 'desc');
    }
}
