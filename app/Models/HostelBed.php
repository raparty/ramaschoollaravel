<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * HostelBed Model
 * 
 * Represents a bed in a hostel room
 */
class HostelBed extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'room_id',
        'bed_number',
        'qr_code',
        'condition_status',
        'notes',
        'is_occupied',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'room_id' => 'integer',
        'is_occupied' => 'boolean',
        'is_active' => 'boolean',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    /**
     * Get the room that owns this bed.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(HostelRoom::class, 'room_id');
    }

    /**
     * Get all allocations for this bed.
     */
    public function allocations(): HasMany
    {
        return $this->hasMany(HostelStudentAllocation::class, 'bed_id');
    }

    /**
     * Get current active allocation.
     */
    public function currentAllocation()
    {
        return $this->allocations()->where('status', 'Active')->first();
    }

    /**
     * Scope: Only available beds.
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_occupied', false)
            ->where('is_active', true)
            ->where('condition_status', 'Good');
    }

    /**
     * Scope: Filter by condition.
     */
    public function scopeByCondition($query, string $condition)
    {
        return $query->where('condition_status', $condition);
    }

    /**
     * Scope: Order by bed number.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('bed_number', 'asc');
    }
}
