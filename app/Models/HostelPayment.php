<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * HostelPayment Model
 * 
 * Represents payment transactions for hostel fees
 */
class HostelPayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'student_fee_id',
        'receipt_number',
        'amount',
        'payment_date',
        'payment_mode',
        'transaction_reference',
        'remarks',
        'received_by',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'student_id' => 'integer',
        'student_fee_id' => 'integer',
        'amount' => 'decimal:2',
        'payment_date' => 'date',
        'received_by' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    /**
     * Get the student for this payment.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Admission::class, 'student_id');
    }

    /**
     * Get the student fee for this payment.
     */
    public function studentFee(): BelongsTo
    {
        return $this->belongsTo(HostelStudentFee::class, 'student_fee_id');
    }

    /**
     * Get the user who received this payment.
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    /**
     * Scope: Filter by payment mode.
     */
    public function scopeByMode($query, string $mode)
    {
        return $query->where('payment_mode', $mode);
    }

    /**
     * Scope: Filter by date range.
     */
    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('payment_date', [$from, $to]);
    }

    /**
     * Scope: Order by payment date.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('payment_date', 'desc');
    }
}
