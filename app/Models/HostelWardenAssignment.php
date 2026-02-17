<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * HostelWardenAssignment Model
 * 
 * Represents a warden's assignment to a hostel
 */
class HostelWardenAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'warden_id',
        'hostel_id',
        'assigned_from',
        'assigned_to',
        'shift_start_time',
        'shift_end_time',
        'shift_type',
        'is_primary',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'warden_id' => 'integer',
        'hostel_id' => 'integer',
        'assigned_from' => 'date',
        'assigned_to' => 'date',
        'is_primary' => 'boolean',
        'is_active' => 'boolean',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    /**
     * Get the warden for this assignment.
     */
    public function warden(): BelongsTo
    {
        return $this->belongsTo(HostelWarden::class, 'warden_id');
    }

    /**
     * Get the hostel for this assignment.
     */
    public function hostel(): BelongsTo
    {
        return $this->belongsTo(Hostel::class, 'hostel_id');
    }

    /**
     * Scope: Only active assignments.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Only current assignments (within date range).
     */
    public function scopeCurrent($query)
    {
        return $query->where('assigned_from', '<=', now())
            ->where(function ($q) {
                $q->whereNull('assigned_to')
                    ->orWhere('assigned_to', '>=', now());
            });
    }

    /**
     * Scope: Filter by shift type.
     */
    public function scopeByShift($query, string $shift)
    {
        return $query->where('shift_type', $shift);
    }
}
