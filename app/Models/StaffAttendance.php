<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * StaffAttendance Model
 * 
 * Manages daily staff attendance records
 * 
 * @property int $id
 * @property int $staff_id Staff ID
 * @property string $date Attendance date
 * @property string $status Status (present/absent/leave/half-day)
 * @property string|null $in_time Check-in time
 * @property string|null $out_time Check-out time
 * @property string|null $notes Notes/remarks
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class StaffAttendance extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'staff_attendance';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'staff_id',
        'date',
        'status',
        'in_time',
        'out_time',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the staff member for this attendance record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    /**
     * Scope a query to only include present attendance.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }

    /**
     * Scope a query to only include absent attendance.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAbsent($query)
    {
        return $query->where('status', 'absent');
    }

    /**
     * Scope a query to only include leave attendance.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOnLeave($query)
    {
        return $query->where('status', 'leave');
    }

    /**
     * Scope a query to filter by date range.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $startDate
     * @param string $endDate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDateRange($query, string $startDate, string $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Scope a query to filter by specific date.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForDate($query, string $date)
    {
        return $query->whereDate('date', $date);
    }

    /**
     * Check if staff was present.
     *
     * @return bool
     */
    public function isPresent(): bool
    {
        return $this->status === 'present';
    }

    /**
     * Check if staff was absent.
     *
     * @return bool
     */
    public function isAbsent(): bool
    {
        return $this->status === 'absent';
    }

    /**
     * Check if staff was on leave.
     *
     * @return bool
     */
    public function isOnLeave(): bool
    {
        return $this->status === 'leave';
    }

    /**
     * Get the status badge class for display.
     *
     * @return string
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'present' => 'badge bg-success',
            'absent' => 'badge bg-danger',
            'leave' => 'badge bg-warning',
            'half-day' => 'badge bg-info',
            default => 'badge bg-secondary',
        };
    }

    /**
     * Get the duration of attendance (hours).
     *
     * @return float|null
     */
    public function getDurationAttribute(): ?float
    {
        if ($this->in_time && $this->out_time) {
            $inTime = strtotime($this->in_time);
            $outTime = strtotime($this->out_time);
            return round(($outTime - $inTime) / 3600, 2);
        }
        return null;
    }
}
