<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * StudentFee Model (Enhanced for Phase 4)
 * 
 * Represents student fee payment records
 * Maps to student_fees_detail table
 * 
 * @property int $id
 * @property string $registration_no
 * @property string $receipt_no
 * @property int $fees_term
 * @property float $fees_amount
 * @property \Carbon\Carbon $payment_date
 * @property string|null $session
 */
class StudentFee extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'student_fees_detail';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'registration_no',
        'receipt_no',
        'fees_term',
        'fees_amount',
        'payment_date',
        'session',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'fees_amount' => 'decimal:2',
        'payment_date' => 'datetime',
        'fees_term' => 'integer',
    ];

    /**
     * Get the student for this fee record.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Admission::class, 'registration_no', 'reg_no');
    }

    /**
     * Get the term for this fee record.
     */
    public function term(): BelongsTo
    {
        return $this->belongsTo(FeeTerm::class, 'fees_term', 'id');
    }

    /**
     * Scope: Filter by registration number.
     */
    public function scopeForStudent($query, string $regNo)
    {
        return $query->where('registration_no', $regNo);
    }

    /**
     * Scope: Filter by term.
     */
    public function scopeForTerm($query, int $termId)
    {
        return $query->where('fees_term', $termId);
    }

    /**
     * Scope: Filter by session.
     */
    public function scopeForSession($query, string $session)
    {
        return $query->where('session', $session);
    }

    /**
     * Scope: Order by payment date (most recent first).
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('payment_date', 'desc');
    }

    /**
     * Generate unique receipt number.
     * Format: FEES-XXXX (e.g., FEES-1001)
     */
    public static function generateReceiptNo(): string
    {
        $latestFee = static::orderBy('id', 'desc')->first();
        $nextId = $latestFee ? $latestFee->id + 1 : 1;
        
        return 'FEES-' . str_pad((string)($nextId + 1000), 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get formatted amount.
     */
    public function getFormattedAmountAttribute(): string
    {
        return '₹' . number_format($this->fees_amount, 2);
    }

    /**
     * Get formatted payment date.
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->payment_date->format('d-M-Y');
    }
}
