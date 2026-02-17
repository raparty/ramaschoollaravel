<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * HostelWarden Model
 * 
 * Represents a hostel warden/staff member
 */
class HostelWarden extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'employee_code',
        'email',
        'phone',
        'address',
        'gender',
        'date_of_joining',
        'status',
        'notes',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'date_of_joining' => 'date',
        'is_active' => 'boolean',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    /**
     * Get all hostel assignments for this warden.
     */
    public function hostelAssignments(): HasMany
    {
        return $this->hasMany(HostelWardenAssignment::class, 'warden_id');
    }

    /**
     * Get all incidents reported by this warden.
     */
    public function reportedIncidents(): HasMany
    {
        return $this->hasMany(HostelIncident::class, 'reported_by');
    }

    /**
     * Get all attendance submitted by this warden.
     */
    public function submittedAttendance(): HasMany
    {
        return $this->hasMany(HostelAttendance::class, 'submitted_by');
    }

    /**
     * Get all complaint responses by this warden.
     */
    public function complaintResponses(): HasMany
    {
        return $this->hasMany(HostelComplaint::class, 'responded_by');
    }

    /**
     * Get all expenses submitted by this warden.
     */
    public function submittedExpenses(): HasMany
    {
        return $this->hasMany(HostelExpense::class, 'submitted_by');
    }

    /**
     * Scope: Only active wardens.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Active')->where('is_active', true);
    }

    /**
     * Scope: Filter by gender.
     */
    public function scopeByGender($query, string $gender)
    {
        return $query->where('gender', $gender);
    }

    /**
     * Scope: Order by name.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('name', 'asc');
    }
}
