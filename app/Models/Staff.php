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
     * Note: The legacy DB doesn't have a 'status' column, so this returns all records.
     * If a status column is added in the future, update this scope accordingly.
     */
    public function scopeActive($query)
    {
        // Check if the status column exists by trying to use it
        // For now, return all records since legacy DB doesn't have status column
        return $query;
    }

    /**
     * Scope: Inactive staff.
     * Note: The legacy DB doesn't have a 'status' column, so this returns no records.
     * If a status column is added in the future, update this scope accordingly.
     */
    public function scopeInactive($query)
    {
        // Return empty result since legacy DB doesn't have status column
        return $query->whereRaw('0 = 1');
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
