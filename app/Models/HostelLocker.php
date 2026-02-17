<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * HostelLocker Model
 * 
 * Represents a locker in a hostel room
 */
class HostelLocker extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'room_id',
        'locker_number',
        'qr_code',
        'condition_status',
        'has_key',
        'notes',
        'is_assigned',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'room_id' => 'integer',
        'has_key' => 'boolean',
        'is_assigned' => 'boolean',
        'is_active' => 'boolean',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    /**
     * Get the room that owns this locker.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(HostelRoom::class, 'room_id');
    }

    /**
     * Scope: Only available lockers.
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_assigned', false)
            ->where('is_active', true)
            ->where('condition_status', 'Good');
    }

    /**
     * Scope: Order by locker number.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('locker_number', 'asc');
    }
}
