<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * StudentTransportFee Model
 * 
 * Represents student transport fee payment records
 * Maps to student_transport_fees_detail table
 * 
 * @property int $id
 * @property string $registration_no
 * @property float $fees_amount
 * @property int $month_id
 * @property string $session
 * @property date $payment_date
 */
class StudentTransportFee extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'student_transport_fees_detail';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'registration_no',
        'fees_amount',
        'month_id',
        'session',
        'payment_date',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'fees_amount' => 'decimal:2',
        'payment_date' => 'date',
        'month_id' => 'integer',
    ];

    /**
     * Get the student for this fee record.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Admission::class, 'registration_no', 'reg_no');
    }

    /**
     * Scope: Filter by registration number.
     */
    public function scopeForStudent($query, string $regNo)
    {
        return $query->where('registration_no', $regNo);
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
     * Scope: Filter by month.
     */
    public function scopeForMonth($query, int $monthId)
    {
        return $query->where('month_id', $monthId);
    }

    /**
     * Get formatted amount.
     */
    public function getFormattedAmountAttribute(): string
    {
        return '₹' . number_format($this->fees_amount, 2);
    }
}
