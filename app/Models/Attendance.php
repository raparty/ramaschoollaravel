<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

/**
 * Attendance Model
 * 
 * Manages daily student attendance records
 */
class Attendance extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'admission_id',
        'date',
        'status',
        'in_time',
        'out_time',
        'remarks',
        'recorded_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'in_time' => 'datetime',
        'out_time' => 'datetime',
    ];

    /**
     * Available attendance statuses
     */
    const STATUS_PRESENT = 'present';
    const STATUS_ABSENT = 'absent';
    const STATUS_LATE = 'late';
    const STATUS_HALF_DAY = 'half_day';
    const STATUS_LEAVE = 'leave';

    /**
     * Get the student (admission) that owns the attendance.
     *
     * @return BelongsTo
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Admission::class, 'admission_id');
    }

    /**
     * Get the staff who recorded the attendance.
     *
     * @return BelongsTo
     */
    public function recorder(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'recorded_by');
    }

    /**
     * Scope a query to only include attendance for a specific date.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForDate($query, string $date)
    {
        return $query->whereDate('date', $date);
    }

    /**
     * Scope a query to only include attendance for a specific student.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $studentId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForStudent($query, int $studentId)
    {
        return $query->where('admission_id', $studentId);
    }

    /**
     * Scope a query to only include attendance for a specific class.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $classId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForClass($query, int $classId)
    {
        return $query->whereHas('student', function ($q) use ($classId) {
            $q->where('class_id', $classId);
        });
    }

    /**
     * Scope a query to only include present attendance.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePresent($query)
    {
        return $query->where('status', self::STATUS_PRESENT);
    }

    /**
     * Scope a query to only include absent attendance.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAbsent($query)
    {
        return $query->where('status', self::STATUS_ABSENT);
    }

    /**
     * Scope a query to only include late attendance.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLate($query)
    {
        return $query->where('status', self::STATUS_LATE);
    }

    /**
     * Scope a query to only include leave attendance.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOnLeave($query)
    {
        return $query->where('status', self::STATUS_LEAVE);
    }

    /**
     * Scope a query for a date range.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $startDate
     * @param  string  $endDate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDateRange($query, string $startDate, string $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Get status badge class for UI.
     *
     * @return string
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PRESENT => 'bg-success',
            self::STATUS_ABSENT => 'bg-danger',
            self::STATUS_LATE => 'bg-warning',
            self::STATUS_HALF_DAY => 'bg-info',
            self::STATUS_LEAVE => 'bg-secondary',
            default => 'bg-secondary',
        };
    }

    /**
     * Get formatted status text.
     *
     * @return string
     */
    public function getStatusTextAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PRESENT => 'Present',
            self::STATUS_ABSENT => 'Absent',
            self::STATUS_LATE => 'Late',
            self::STATUS_HALF_DAY => 'Half Day',
            self::STATUS_LEAVE => 'Leave',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get attendance duration in minutes.
     *
     * @return int|null
     */
    public function getDurationAttribute(): ?int
    {
        if ($this->in_time && $this->out_time) {
            return $this->in_time->diffInMinutes($this->out_time);
        }
        return null;
    }

    /**
     * Get formatted duration.
     *
     * @return string|null
     */
    public function getFormattedDurationAttribute(): ?string
    {
        if ($this->duration) {
            $hours = floor($this->duration / 60);
            $minutes = $this->duration % 60;
            return sprintf('%dh %dm', $hours, $minutes);
        }
        return null;
    }

    /**
     * Check if student was present (present or late).
     *
     * @return bool
     */
    public function isPresent(): bool
    {
        return in_array($this->status, [self::STATUS_PRESENT, self::STATUS_LATE, self::STATUS_HALF_DAY]);
    }

    /**
     * Check if student was absent.
     *
     * @return bool
     */
    public function isAbsent(): bool
    {
        return $this->status === self::STATUS_ABSENT;
    }
}
