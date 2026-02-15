<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * TransportStudentAssignment Model
 * 
 * Represents a student's transport assignment (vehicle and route)
 * Maps to transport_student_detail table
 * 
 * @property int $id
 * @property string $registration_no
 * @property int $route_id
 * @property int $vechile_id
 * @property int $class_id
 * @property int $stream_id
 * @property string $session
 */
class TransportStudentAssignment extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'transport_student_detail';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'registration_no',
        'route_id',
        'vechile_id',
        'class_id',
        'stream_id',
        'session',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'route_id' => 'integer',
        'vechile_id' => 'integer',
        'class_id' => 'integer',
        'stream_id' => 'integer',
    ];

    /**
     * Get the student for this assignment.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Admission::class, 'registration_no', 'reg_no');
    }

    /**
     * Get the route for this assignment.
     */
    public function route(): BelongsTo
    {
        return $this->belongsTo(TransportRoute::class, 'route_id', 'route_id');
    }

    /**
     * Get the vehicle for this assignment.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(TransportVehicle::class, 'vechile_id', 'vechile_id');
    }

    /**
     * Get the class for this assignment.
     * Note: This relationship may not work if class table doesn't exist.
     * TODO: Add proper class model relationship when classes table is properly defined.
     */
    public function classModel(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id', 'id');
    }

    /**
     * Scope: Filter by session.
     */
    public function scopeForSession($query, string $session)
    {
        return $query->where('session', $session);
    }

    /**
     * Scope: Filter by registration number.
     */
    public function scopeForStudent($query, string $regNo)
    {
        return $query->where('registration_no', $regNo);
    }

    /**
     * Scope: Filter by route.
     */
    public function scopeForRoute($query, int $routeId)
    {
        return $query->where('route_id', $routeId);
    }

    /**
     * Scope: Filter by vehicle.
     */
    public function scopeForVehicle($query, int $vehicleId)
    {
        return $query->where('vechile_id', $vehicleId);
    }

    /**
     * Scope: Filter by class.
     */
    public function scopeForClass($query, int $classId)
    {
        return $query->where('class_id', $classId);
    }
}
