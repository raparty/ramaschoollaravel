<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * StudentFee Model
 * 
 * Represents student fee records
 * Maps to student_fees_detail table
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
        'fees_term',
        'fees_amount',
        'payment_date',
        'session',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'fees_amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    /**
     * Get the student for this fee record.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Admission::class, 'registration_no', 'reg_no');
    }

    /**
     * Scope: Filter by pending status.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Filter by paid status.
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope: Filter by session.
     */
    public function scopeForSession($query, string $session)
    {
        return $query->where('session', $session);
    }
}
