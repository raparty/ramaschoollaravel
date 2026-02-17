<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Hostel Model
 * 
 * Represents a hostel building
 * 
 * @property int $id
 * @property string $name
 * @property string $type (Boys/Girls/Junior/Senior)
 * @property int $total_capacity
 * @property string|null $address
 * @property string|null $description
 * @property bool $is_active
 */
class Hostel extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'total_capacity',
        'address',
        'description',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'total_capacity' => 'integer',
        'is_active' => 'boolean',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    /**
     * Get all blocks for this hostel.
     */
    public function blocks(): HasMany
    {
        return $this->hasMany(HostelBlock::class);
    }

    /**
     * Get all warden assignments for this hostel.
     */
    public function wardenAssignments(): HasMany
    {
        return $this->hasMany(HostelWardenAssignment::class);
    }

    /**
     * Get all incidents for this hostel.
     */
    public function incidents(): HasMany
    {
        return $this->hasMany(HostelIncident::class);
    }

    /**
     * Get all attendance records for this hostel.
     */
    public function attendance(): HasMany
    {
        return $this->hasMany(HostelAttendance::class);
    }

    /**
     * Get all complaints for this hostel.
     */
    public function complaints(): HasMany
    {
        return $this->hasMany(HostelComplaint::class);
    }

    /**
     * Get all fee structures for this hostel.
     */
    public function feeStructures(): HasMany
    {
        return $this->hasMany(HostelFeeStructure::class);
    }

    /**
     * Scope: Filter by type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope: Only active hostels.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Order by name.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('name', 'asc');
    }

    /**
     * Get total occupied beds in this hostel.
     */
    public function getTotalOccupiedAttribute(): int
    {
        return $this->blocks()
            ->withCount(['floors.rooms.beds' => function ($query) {
                $query->where('is_occupied', true);
            }])
            ->get()
            ->sum('floors_rooms_beds_count');
    }

    /**
     * Get total available beds in this hostel.
     */
    public function getTotalAvailableAttribute(): int
    {
        return $this->total_capacity - $this->total_occupied;
    }
}
