<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * HostelBlock Model
 * 
 * Represents a block within a hostel
 */
class HostelBlock extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hostel_id',
        'name',
        'total_floors',
        'description',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'hostel_id' => 'integer',
        'total_floors' => 'integer',
        'is_active' => 'boolean',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    /**
     * Get the hostel that owns this block.
     */
    public function hostel(): BelongsTo
    {
        return $this->belongsTo(Hostel::class);
    }

    /**
     * Get all floors in this block.
     */
    public function floors(): HasMany
    {
        return $this->hasMany(HostelFloor::class, 'block_id');
    }

    /**
     * Scope: Only active blocks.
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
}
