<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * HostelRoom Model
 * 
 * Represents a room in a hostel
 */
class HostelRoom extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'floor_id',
        'room_number',
        'room_type',
        'max_strength',
        'area_sqft',
        'has_attached_bathroom',
        'description',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'floor_id' => 'integer',
        'max_strength' => 'integer',
        'area_sqft' => 'decimal:2',
        'has_attached_bathroom' => 'boolean',
        'is_active' => 'boolean',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    /**
     * Get the floor that owns this room.
     */
    public function floor(): BelongsTo
    {
        return $this->belongsTo(HostelFloor::class, 'floor_id');
    }

    /**
     * Get all beds in this room.
     */
    public function beds(): HasMany
    {
        return $this->hasMany(HostelBed::class, 'room_id');
    }

    /**
     * Get all lockers in this room.
     */
    public function lockers(): HasMany
    {
        return $this->hasMany(HostelLocker::class, 'room_id');
    }

    /**
     * Get all furniture in this room.
     */
    public function furniture(): HasMany
    {
        return $this->hasMany(HostelFurniture::class, 'room_id');
    }

    /**
     * Scope: Only active rooms.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Filter by room type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('room_type', $type);
    }

    /**
     * Scope: Order by room number.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('room_number', 'asc');
    }

    /**
     * Get current occupancy count.
     */
    public function getCurrentOccupancyAttribute(): int
    {
        return $this->beds()->where('is_occupied', true)->count();
    }

    /**
     * Get available beds count.
     */
    public function getAvailableBedsAttribute(): int
    {
        return $this->beds()->where('is_occupied', false)->where('is_active', true)->count();
    }

    /**
     * Check if room is fully occupied.
     */
    public function getIsFullAttribute(): bool
    {
        return $this->current_occupancy >= $this->max_strength;
    }
}
