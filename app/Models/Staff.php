<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Staff Model
 * 
 * Manages staff/employee records including teachers and non-teaching staff
 * 
 * @property int $id
 * @property string $staff_id Staff unique identifier
 * @property string $name Full name
 * @property string $email Email address
 * @property string|null $phone Phone number
 * @property string|null $photo Photo path
 * @property int $department_id Department ID
 * @property int $position_id Position/Designation ID
 * @property string|null $qualification Qualification details
 * @property string|null $address Address
 * @property string|null $date_of_birth Date of birth
 * @property string $joining_date Joining date
 * @property float $basic_salary Basic monthly salary
 * @property string $status Status (active/inactive)
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class Staff extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'staff';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'staff_id',
        'name',
        'email',
        'phone',
        'photo',
        'department_id',
        'position_id',
        'qualification',
        'address',
        'date_of_birth',
        'joining_date',
        'basic_salary',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_of_birth' => 'date',
        'joining_date' => 'date',
        'basic_salary' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the department that the staff belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the position/designation of the staff.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * Get all salaries for the staff.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function salaries()
    {
        return $this->hasMany(Salary::class);
    }

    /**
     * Get all attendance records for the staff.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attendance()
    {
        return $this->hasMany(StaffAttendance::class);
    }

    /**
     * Scope a query to only include active staff.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include inactive staff.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope a query to filter by department.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $departmentId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByDepartment($query, int $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Scope a query to search staff by name, email, or staff_id.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('staff_id', 'like', "%{$search}%");
        });
    }

    /**
     * Get the staff's full name.
     *
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return $this->name;
    }

    /**
     * Get the staff's photo URL or default.
     *
     * @return string
     */
    public function getPhotoUrlAttribute(): string
    {
        return $this->photo 
            ? asset('storage/' . $this->photo) 
            : asset('images/default-avatar.png');
    }

    /**
     * Get the staff's years of service.
     *
     * @return int
     */
    public function getYearsOfServiceAttribute(): int
    {
        return $this->joining_date->diffInYears(now());
    }

    /**
     * Check if staff is active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if staff is inactive.
     *
     * @return bool
     */
    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }
}
