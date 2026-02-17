<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * HostelSecurityDeposit Model
 * 
 * Represents security deposits for hostel students
 */
class HostelSecurityDeposit extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'allocation_id',
        'deposit_amount',
        'deposit_date',
        'receipt_number',
        'refund_amount',
        'deduction_amount',
        'refund_date',
        'status',
        'deduction_remarks',
        'refund_remarks',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'student_id' => 'integer',
        'allocation_id' => 'integer',
        'deposit_amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'deduction_amount' => 'decimal:2',
        'deposit_date' => 'date',
        'refund_date' => 'date',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    /**
     * Get the student for this deposit.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Admission::class, 'student_id');
    }

    /**
     * Get the allocation for this deposit.
     */
    public function allocation(): BelongsTo
    {
        return $this->belongsTo(HostelStudentAllocation::class, 'allocation_id');
    }

    /**
     * Scope: Filter by status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Held deposits.
     */
    public function scopeHeld($query)
    {
        return $query->where('status', 'Held');
    }

    /**
     * Scope: Order by deposit date.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('deposit_date', 'desc');
    }

    /**
     * Get net refund amount.
     */
    public function getNetRefundAttribute(): float
    {
        return $this->deposit_amount - $this->deduction_amount;
    }
}
