<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model {
    protected $table = 'attendance';
    public $timestamps = false;
    protected $fillable = ['id', 'status', 'attendance_date', 'user_id', 'marked_by'];
    
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
        return $query->where('status', 'Present');
    }
    
    /**
     * Scope to filter absent students.
     */
    public function scopeAbsent($query)
    {
        return $query->where('status', 'Absent');
    }
    
    /**
     * Scope to filter late students.
     */
    public function scopeLate($query)
    {
        return $query->where('status', 'Late');
    }
}
