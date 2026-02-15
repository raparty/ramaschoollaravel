<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Admission extends Model {
    protected $table = 'admissions';
    public $timestamps = false; 
    protected $fillable = ['reg_no', 'student_name', 'class_id', 'admission_date'];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'admission_date' => 'date',
    ];

    /**
     * Get the class that owns the admission.
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    /**
     * Scope: Search by student name, reg_no, or guardian name
     */
    public function scopeSearch($query, $search)
    {
        // Escape wildcard characters in user input
        $escapedSearch = str_replace(['%', '_'], ['\%', '\_'], $search);
        
        return $query->where(function($q) use ($escapedSearch) {
            $q->where('student_name', 'like', "%{$escapedSearch}%")
              ->orWhere('reg_no', 'like', "%{$escapedSearch}%")
              ->orWhere('guardian_name', 'like', "%{$escapedSearch}%");
        });
    }

    /**
     * Scope: Filter by class
     */
    public function scopeInClass($query, $classId)
    {
        if (empty($classId)) {
            return $query;
        }
        
        return $query->where('class_id', $classId);
    }

    /**
     * Scope: Order by most recent (admission date or id)
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('admission_date', 'desc')->orderBy('id', 'desc');
    }
}
