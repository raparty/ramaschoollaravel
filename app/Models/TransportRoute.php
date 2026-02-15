<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * TransportRoute Model
 * 
 * Represents a transport route/destination with monthly cost
 * Maps to transport_add_route table
 * 
 * @property int $route_id
 * @property string $route_name
 * @property float $cost
 * @property string $created_at
 */
class TransportRoute extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'transport_add_route';

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'route_id';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'route_name',
        'cost',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'cost' => 'decimal:2',
    ];

    /**
     * Get all student assignments for this route.
     */
    public function studentAssignments(): HasMany
    {
        return $this->hasMany(TransportStudentAssignment::class, 'route_id', 'route_id');
    }

    /**
     * Scope: Order routes by name.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('route_name', 'asc');
    }

    /**
     * Scope: Search routes by name.
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where('route_name', 'like', "%{$search}%");
    }

    /**
     * Accessor: Formatted cost with currency symbol.
     */
    public function getFormattedCostAttribute(): string
    {
        return '₹' . number_format($this->cost, 2);
    }
}
