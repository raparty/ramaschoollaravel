<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Admission extends Model {
    protected $table = 'admissions';
    public $timestamps = false; 
    protected $fillable = [
        'reg_no',
        'student_name',
        'class_id',
        'admission_date',
        'dob',
        'gender',
        'blood_group',
        'section_id',
        'stream_id',
        'aadhaar_no',
        'guardian_name',
        'guardian_phone',
        'address',
        'past_school_info',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'admission_date' => 'date',
        'dob' => 'date',
    ];

    /**
     * Get the class that owns the admission.
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    /**
     * Get the fees for this student.
     */
    public function fees(): HasMany
    {
        return $this->hasMany(StudentFee::class, 'registration_no', 'reg_no');
    }

    /**
     * Get the transport fees for this student.
     */
    public function transportFees(): HasMany
    {
        return $this->hasMany(StudentTransportFee::class, 'registration_no', 'reg_no');
    }

    /**
     * Get the library books issued to this student.
     */
    public function libraryBooks(): HasMany
    {
        return $this->hasMany(BookIssue::class, 'registration_no', 'reg_no');
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

    /**
     * Accessor: Get name attribute (alias for student_name)
     */
    public function getNameAttribute(): ?string
    {
        return $this->attributes['student_name'] ?? null;
    }

    /**
     * Accessor: Get regno attribute (alias for reg_no)
     */
    public function getRegnoAttribute(): ?string
    {
        return $this->attributes['reg_no'] ?? null;
    }
}
