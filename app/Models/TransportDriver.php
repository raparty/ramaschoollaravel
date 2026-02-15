<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * TransportDriver Model
 * 
 * Represents a school transport driver
 * 
 * @property int $driver_id
 * @property string $driver_name
 * @property string|null $license_number
 * @property string|null $aadhar_number
 * @property string|null $contact_number
 * @property string|null $address
 * @property string $status
 */
class TransportDriver extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'transport_drivers';

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'driver_id';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'driver_name',
        'license_number',
        'aadhar_number',
        'contact_number',
        'address',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get all vehicles assigned to this driver.
     */
    public function vehicles(): HasMany
    {
        return $this->hasMany(TransportVehicle::class, 'driver_id', 'driver_id');
    }

    /**
     * Scope: Order drivers by name.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('driver_name', 'asc');
    }

    /**
     * Scope: Search drivers by name, license, or contact.
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('driver_name', 'like', "%{$search}%")
              ->orWhere('license_number', 'like', "%{$search}%")
              ->orWhere('contact_number', 'like', "%{$search}%");
        });
    }

    /**
     * Scope: Filter by status.
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Accessor: Get assigned vehicle numbers as a comma-separated string.
     */
    public function getVehicleNumbersAttribute(): string
    {
        return $this->vehicles->pluck('vechile_no')->join(', ');
    }
}
