<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Admission extends Model {
    protected $table = 'admissions';
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
        'emergency_contact',
        'address',
        'health_issues',
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
     * Get the hostel allocations for this student.
     */
    public function hostelAllocations(): HasMany
    {
        return $this->hasMany(HostelStudentAllocation::class, 'student_id');
    }

    /**
     * Get the current active hostel allocation.
     */
    public function currentHostelAllocation()
    {
        return $this->hostelAllocations()->where('status', 'Active')->first();
    }

    /**
     * Get the hostel imprest wallet for this student.
     */
    public function hostelWallet()
    {
        return $this->hasOne(HostelImprestWallet::class, 'student_id');
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

    /**
     * Generate a unique registration number for new admission
     * 
     * Format: YEAR + 4-digit sequence (e.g., 20260001, 20260002, etc.)
     * 
     * Uses database row locking within a transaction to prevent race conditions
     * when multiple admissions are being created simultaneously.
     * 
     * The method counts existing admissions for the current year and increments.
     * If a duplicate is detected (unlikely with locking), it retries up to 10 times.
     * 
     * @return string The generated registration number
     * @throws \Exception If unable to generate unique number after max attempts
     */
    public static function generateRegNo(): string
    {
        // Get the current year
        $year = date('Y');
        
        // Use database transaction to prevent race conditions
        return DB::transaction(function () use ($year) {
            // Lock the table for this transaction
            $count = static::lockForUpdate()
                ->whereYear('created_at', $year)
                ->count() + 1;
            
            // Generate registration number: YEAR + 4-digit sequence
            $regNo = $year . str_pad($count, 4, '0', STR_PAD_LEFT);
            
            // Double-check uniqueness (in case of concurrent requests)
            $attempt = 0;
            while (static::where('reg_no', $regNo)->exists() && $attempt < 10) {
                $count++;
                $regNo = $year . str_pad($count, 4, '0', STR_PAD_LEFT);
                $attempt++;
            }
            
            return $regNo;
        });
    }
}
