<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Admission Model
 * 
 * Represents student admissions in the school ERP system
 * Converts: add_admission.php, student_detail.php, edit_admission.php
 * 
 * @property int $id
 * @property string $reg_no
 * @property string $student_name
 * @property string|null $student_pic
 * @property \Carbon\Carbon $dob
 * @property string|null $gender
 * @property string|null $blood_group
 * @property int $class_id
 * @property \Carbon\Carbon $admission_date
 * @property string|null $aadhaar_no
 * @property string|null $aadhaar_doc_path
 * @property string|null $guardian_name
 * @property string|null $guardian_phone
 * @property string|null $past_school_info
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class Admission extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'admissions';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'reg_no',
        'student_name',
        'student_pic',
        'dob',
        'gender',
        'blood_group',
        'class_id',
        'admission_date',
        'aadhaar_no',
        'aadhaar_doc_path',
        'guardian_name',
        'guardian_phone',
        'past_school_info',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'dob' => 'date',
        'admission_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the class that the student is admitted to.
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
        return $this->hasMany(LibraryStudentBook::class, 'registration_no', 'reg_no');
    }

    /**
     * Get the student's age.
     */
    public function getAgeAttribute(): int
    {
        return $this->dob ? $this->dob->age : 0;
    }

    /**
     * Get the full name attribute (alias).
     */
    public function getFullNameAttribute(): string
    {
        return $this->student_name;
    }

    /**
     * Get the student photo URL.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if (!$this->student_pic) {
            return null;
        }
        
        return asset('storage/students/photos/' . $this->student_pic);
    }

    /**
     * Get the Aadhaar document URL.
     */
    public function getAadhaarDocUrlAttribute(): ?string
    {
        if (!$this->aadhaar_doc_path) {
            return null;
        }
        
        return asset('storage/students/aadhaar/' . $this->aadhaar_doc_path);
    }

    /**
     * Scope: Filter by class.
     */
    public function scopeInClass($query, int $classId)
    {
        return $query->where('class_id', $classId);
    }

    /**
     * Scope: Search by name or registration number.
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('student_name', 'LIKE', "%{$search}%")
              ->orWhere('reg_no', 'LIKE', "%{$search}%")
              ->orWhere('guardian_name', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Scope: Order by most recent admissions.
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('admission_date', 'desc');
    }

    /**
     * Generate unique registration number.
     * Format: YEAR-XXXX (e.g., 2026-0001)
     */
    public static function generateRegNo(): string
    {
        $year = date('Y');
        $latestAdmission = static::where('reg_no', 'LIKE', $year . '-%')
            ->orderBy('reg_no', 'desc')
            ->first();

        if ($latestAdmission) {
            $lastNumber = (int) substr($latestAdmission->reg_no, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $year . '-' . str_pad((string)$newNumber, 4, '0', STR_PAD_LEFT);
    }
}
