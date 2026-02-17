<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * HostelExpense Model
 * 
 * Represents expense transactions from student wallets
 */
class HostelExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'category_id',
        'amount',
        'expense_date',
        'description',
        'bill_number',
        'status',
        'submitted_by',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'wallet_id' => 'integer',
        'category_id' => 'integer',
        'amount' => 'decimal:2',
        'expense_date' => 'date',
        'submitted_by' => 'integer',
        'approved_by' => 'integer',
        'approved_at' => 'datetime',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    /**
     * Get the wallet for this expense.
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(HostelImprestWallet::class, 'wallet_id');
    }

    /**
     * Get the category for this expense.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(HostelExpenseCategory::class, 'category_id');
    }

    /**
     * Get the warden who submitted this expense.
     */
    public function submitter(): BelongsTo
    {
        return $this->belongsTo(HostelWarden::class, 'submitted_by');
    }

    /**
     * Get the user who approved this expense.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope: Filter by status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Pending expenses.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    /**
     * Scope: Approved expenses.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'Approved');
    }

    /**
     * Scope: Filter by date range.
     */
    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('expense_date', [$from, $to]);
    }

    /**
     * Scope: Order by expense date.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('expense_date', 'desc');
    }
}
