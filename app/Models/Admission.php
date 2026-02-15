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
        return $query->where(function($q) use ($search) {
            $q->where('student_name', 'like', "%{$search}%")
              ->orWhere('reg_no', 'like', "%{$search}%")
              ->orWhere('guardian_name', 'like', "%{$search}%");
        });
    }

    /**
     * Scope: Filter by class
     */
    public function scopeInClass($query, $classId)
    {
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
