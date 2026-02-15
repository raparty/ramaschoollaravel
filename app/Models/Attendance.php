<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model {
    protected $table = 'attendance';
    public $timestamps = false;
    protected $fillable = ['id', 'status', 'attendance_date', 'user_id', 'marked_by'];
    
    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'attendance_date' => 'date',
    ];
    
    // Status constants
    const STATUS_PRESENT = 'Present';
    const STATUS_ABSENT = 'Absent';
    const STATUS_LATE = 'Late';
    const STATUS_HALF_DAY = 'Half Day';
    
    /**
     * Scope to filter attendance by date.
     */
    public function scopeForDate($query, $date)
    {
        return $query->where('attendance_date', $date);
    }
    
    /**
     * Scope to filter present students.
     */
    public function scopePresent($query)
    {
        return $query->where('status', self::STATUS_PRESENT);
    }
    
    /**
     * Scope to filter absent students.
     */
    public function scopeAbsent($query)
    {
        return $query->where('status', self::STATUS_ABSENT);
    }
    
    /**
     * Scope to filter late students.
     */
    public function scopeLate($query)
    {
        return $query->where('status', self::STATUS_LATE);
    }
    
    /**
     * Scope to filter attendance by class.
     */
    public function scopeForClass($query, $classId)
    {
        return $query->whereHas('student', function($q) use ($classId) {
            $q->where('class_id', $classId);
        });
    }
    
    /**
     * Scope to filter attendance by student.
     */
    public function scopeForStudent($query, $studentId)
    {
        return $query->where('user_id', $studentId);
    }
    
    /**
     * Scope to filter attendance by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('attendance_date', [$startDate, $endDate]);
    }
    
    /**
     * Scope to filter students on leave.
     */
    public function scopeOnLeave($query)
    {
        return $query->where('status', 'On Leave');
    }
    
    /**
     * Get the student (admission) record via user_id.
     */
    public function student()
    {
        return $this->belongsTo(Admission::class, 'user_id', 'reg_no');
    }
    
    /**
     * Check if the attendance is present.
     */
    public function isPresent(): bool
    {
        return in_array($this->status, [self::STATUS_PRESENT, self::STATUS_LATE, self::STATUS_HALF_DAY]);
    }
    
    /**
     * Check if the attendance is absent.
     */
    public function isAbsent(): bool
    {
        return $this->status === self::STATUS_ABSENT;
    }
    
    /**
     * Accessor for date attribute (alias for attendance_date).
     */
    public function getDateAttribute()
    {
        return $this->attendance_date;
    }
    
    /**
     * Accessor for admission_id attribute (alias for user_id).
     */
    public function getAdmissionIdAttribute()
    {
        return $this->user_id;
    }
}
