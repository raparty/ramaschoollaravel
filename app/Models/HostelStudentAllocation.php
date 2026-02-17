<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * HostelStudentAllocation Model
 * 
 * Represents a student's bed allocation in a hostel
 */
class HostelStudentAllocation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'bed_id',
        'locker_id',
        'check_in_date',
        'check_out_date',
        'status',
        'check_in_remarks',
        'check_out_remarks',
        'receipt_number',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'student_id' => 'integer',
        'bed_id' => 'integer',
        'locker_id' => 'integer',
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'is_active' => 'boolean',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    /**
     * Get the student for this allocation.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Admission::class, 'student_id');
    }

    /**
     * Get the bed for this allocation.
     */
    public function bed(): BelongsTo
    {
        return $this->belongsTo(HostelBed::class, 'bed_id');
    }

    /**
     * Get the locker for this allocation.
     */
    public function locker(): BelongsTo
    {
        return $this->belongsTo(HostelLocker::class, 'locker_id');
    }

    /**
     * Get the security deposit for this allocation.
     */
    public function securityDeposit(): HasOne
    {
        return $this->hasOne(HostelSecurityDeposit::class, 'allocation_id');
    }

    /**
     * Scope: Only active allocations.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    /**
     * Scope: Filter by status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Order by check-in date.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('check_in_date', 'desc');
    }

    /**
     * Check if allocation is currently active.
     */
    public function getIsCurrentAttribute(): bool
    {
        return $this->status === 'Active' && 
               $this->check_in_date <= now() && 
               ($this->check_out_date === null || $this->check_out_date >= now());
    }
}
