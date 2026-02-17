<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * HostelFurniture Model
 * 
 * Represents furniture items in a hostel room
 */
class HostelFurniture extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'room_id',
        'asset_code',
        'item_name',
        'furniture_type',
        'quantity',
        'condition_status',
        'purchase_date',
        'purchase_value',
        'notes',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'room_id' => 'integer',
        'quantity' => 'integer',
        'purchase_date' => 'date',
        'purchase_value' => 'decimal:2',
        'is_active' => 'boolean',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    /**
     * Get the room that owns this furniture.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(HostelRoom::class, 'room_id');
    }

    /**
     * Scope: Filter by furniture type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('furniture_type', $type);
    }

    /**
     * Scope: Filter by condition.
     */
    public function scopeByCondition($query, string $condition)
    {
        return $query->where('condition_status', $condition);
    }

    /**
     * Scope: Order by asset code.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('asset_code', 'asc');
    }
}
