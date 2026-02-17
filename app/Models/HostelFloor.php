<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * HostelFloor Model
 * 
 * Represents a floor within a hostel block
 */
class HostelFloor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'block_id',
        'floor_number',
        'name',
        'description',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'block_id' => 'integer',
        'floor_number' => 'integer',
        'is_active' => 'boolean',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    /**
     * Get the block that owns this floor.
     */
    public function block(): BelongsTo
    {
        return $this->belongsTo(HostelBlock::class, 'block_id');
    }

    /**
     * Get all rooms on this floor.
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(HostelRoom::class, 'floor_id');
    }

    /**
     * Scope: Only active floors.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Order by floor number.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('floor_number', 'asc');
    }
}
