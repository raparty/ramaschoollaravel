<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Staff Model
 * * Manages staff/employee records including teachers and non-teaching staff
 * Maps to staff_employee table in database
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
        'status', // Added to support active/inactive filtering
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
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'dept_id');
    }

    /**
     * Get the position/designation of the staff.
     */
    public function position()
    {
        return $this->belongsTo(Position::class, 'pos_id');
    }

    /**
     * Get all salaries for the staff.
     */
    public function salaries()
    {
        return $this->hasMany(Salary::class);
    }

    /**
     * Get all attendance records for the staff.
     */
    public function attendance()
    {
        return $this->hasMany(StaffAttendance::class);
    }

    /**
     * Scope: Filter by department.
     */
    public function scopeByDepartment($query, int $departmentId)
    {
        return $query->where('dept_id', $departmentId);
    }

    /**
     * Scope: Search staff by name or employee_id.
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('employee_id', 'like', "%{$search}%");
        });
    }

    /**
     * Scope: Active staff.
     * If 'status' column is missing in legacy DB, this returns all records.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')->orWhereRaw('1=1');
    }

    /**
     * Scope: Inactive staff.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Accessor: Full Name.
     */
    public function getFullNameAttribute(): string
    {
        return $this->name;
    }

    /**
     * Accessor: Years of Service.
     */
    public function getYearsOfServiceAttribute(): int
    {
        return $this->joining_date ? $this->joining_date->diffInYears(now()) : 0;
    }
}
