<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * TransportVehicle Model
 * 
 * Represents a school transport vehicle
 * Maps to transport_add_vechile table (note: legacy spelling maintained)
 * 
 * @property int $vechile_id
 * @property string $vechile_no
 * @property string $route_id (comma-separated route IDs)
 * @property int $no_of_seats
 */
class TransportVehicle extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'transport_add_vechile';

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'vechile_id';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'vechile_no',
        'route_id',
        'no_of_seats',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'no_of_seats' => 'integer',
    ];

    /**
     * Get all student assignments for this vehicle.
     */
    public function studentAssignments(): HasMany
    {
        return $this->hasMany(TransportStudentAssignment::class, 'vehicle_id', 'vechile_id');
    }

    /**
     * Get the routes assigned to this vehicle.
     * Note: route_id is stored as comma-separated values in legacy DB
     */
    public function routes()
    {
        if (empty($this->route_id)) {
            return collect();
        }
        
        $routeIds = explode(',', $this->route_id);
        return TransportRoute::whereIn('route_id', $routeIds)->get();
    }

    /**
     * Scope: Order vehicles by vehicle number.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('vechile_no', 'asc');
    }

    /**
     * Scope: Search vehicles by vehicle number.
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where('vechile_no', 'like', "%{$search}%");
    }

    /**
     * Accessor: Get available seats.
     */
    public function getAvailableSeatsAttribute(): int
    {
        $occupied = $this->studentAssignments()->count();
        return max(0, $this->no_of_seats - $occupied);
    }

    /**
     * Accessor: Get route names as a comma-separated string.
     */
    public function getRouteNamesAttribute(): string
    {
        $routes = $this->routes();
        return $routes->pluck('route_name')->join(', ');
    }
}
