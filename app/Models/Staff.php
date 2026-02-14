<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Staff Model
 * 
 * Manages staff/employee records including teachers and non-teaching staff
 * Maps to staff_employee table in database
 * 
 * @property int $id
 * @property string $employee_id Staff unique identifier
 * @property string $name Full name
 * @property int $dept_id Department ID
 * @property int $cat_id Category ID
 * @property int $pos_id Position/Designation ID
 * @property int $qualification_id Qualification ID
 * @property float $salary Monthly salary
 * @property string|null $joining_date Joining date
 */
class Staff extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'staff_employee';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'name',
        'dept_id',
        'cat_id',
        'pos_id',
        'qualification_id',
        'salary',
        'joining_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'joining_date' => 'date',
        'salary' => 'decimal:2',
    ];
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the department that the staff belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'dept_id');
    }

    /**
     * Get the position/designation of the staff.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function position()
    {
        return $this->belongsTo(Position::class, 'pos_id');
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
     * Scope a query to filter by department.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $departmentId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByDepartment($query, int $departmentId)
    {
        return $query->where('dept_id', $departmentId);
    }

    /**
     * Scope a query to search staff by name or employee_id.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('employee_id', 'like', "%{$search}%");
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
     * Get the staff's years of service.
     *
     * @return int
     */
    public function getYearsOfServiceAttribute(): int
    {
        return $this->joining_date ? $this->joining_date->diffInYears(now()) : 0;
    }
}
