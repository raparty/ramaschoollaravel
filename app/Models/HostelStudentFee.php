<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * HostelStudentFee Model
 * 
 * Represents fee ledger entries for hostel students
 */
class HostelStudentFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'fee_structure_id',
        'amount_due',
        'amount_paid',
        'amount_balance',
        'due_date',
        'paid_date',
        'status',
        'fine_amount',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'student_id' => 'integer',
        'fee_structure_id' => 'integer',
        'amount_due' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'amount_balance' => 'decimal:2',
        'fine_amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_date' => 'date',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    /**
     * Get the student for this fee.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Admission::class, 'student_id');
    }

    /**
     * Get the fee structure for this fee.
     */
    public function feeStructure(): BelongsTo
    {
        return $this->belongsTo(HostelFeeStructure::class, 'fee_structure_id');
    }

    /**
     * Get all payments for this fee.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(HostelPayment::class, 'student_fee_id');
    }

    /**
     * Scope: Filter by status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Pending fees.
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', ['Pending', 'Partial']);
    }

    /**
     * Scope: Overdue fees.
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'Overdue')
            ->orWhere(function ($q) {
                $q->whereIn('status', ['Pending', 'Partial'])
                    ->where('due_date', '<', now());
            });
    }

    /**
     * Scope: Order by due date.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('due_date', 'asc');
    }

    /**
     * Check if fee is overdue.
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->status !== 'Paid' && 
               $this->status !== 'Waived' && 
               $this->due_date < now();
    }
}
