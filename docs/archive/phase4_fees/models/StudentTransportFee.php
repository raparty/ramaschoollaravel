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
 * @property string $reciept_no
 * @property float $fees_amount
 * @property \Carbon\Carbon $payment_date
 * @property string|null $session
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
        'reciept_no',
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
     * Generate unique receipt number.
     * Format: TFEES-XXXX (e.g., TFEES-1001)
     */
    public static function generateReceiptNo(): string
    {
        $latestFee = static::orderBy('id', 'desc')->first();
        $nextId = $latestFee ? $latestFee->id + 1 : 1;
        
        return 'TFEES-' . str_pad((string)($nextId + 1000), 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get formatted amount.
     */
    public function getFormattedAmountAttribute(): string
    {
        return '₹' . number_format($this->fees_amount, 2);
    }
}
