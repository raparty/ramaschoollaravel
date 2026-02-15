<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model {
    protected $table = 'attendance';
    public $timestamps = false;
    protected $fillable = ['id', 'status', 'attendance_date', 'user_id', 'marked_by'];
    
    // Status constants
    const STATUS_PRESENT = 'Present';
    const STATUS_ABSENT = 'Absent';
    const STATUS_LATE = 'Late';
    
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
}
