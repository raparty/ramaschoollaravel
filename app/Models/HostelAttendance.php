<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * HostelAttendance Model
 * 
 * Represents daily attendance records for hostel students
 */
class HostelAttendance extends Model
{
    use HasFactory;

    protected $table = 'hostel_attendance';

    protected $fillable = [
        'hostel_id',
        'student_id',
        'attendance_date',
        'status',
        'check_in_time',
        'check_out_time',
        'remarks',
        'submitted_by',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'hostel_id' => 'integer',
        'student_id' => 'integer',
        'attendance_date' => 'date',
        'submitted_by' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    /**
     * Get the hostel for this attendance.
     */
    public function hostel(): BelongsTo
    {
        return $this->belongsTo(Hostel::class, 'hostel_id');
    }

    /**
     * Get the student for this attendance.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Admission::class, 'student_id');
    }

    /**
     * Get the warden who submitted this attendance.
     */
    public function submitter(): BelongsTo
    {
        return $this->belongsTo(HostelWarden::class, 'submitted_by');
    }

    /**
     * Scope: Filter by date.
     */
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('attendance_date', $date);
    }

    /**
     * Scope: Filter by status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Order by attendance date.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('attendance_date', 'desc');
    }
}
