<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Model
 * 
 * Represents school classes/grades
 * Note: Named ClassModel because 'Class' is a reserved keyword in PHP
 * 
 * @property int $id
 * @property string $class_name
 */
class ClassModel extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'classes';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'class_name',
    ];

    /**
     * Get the students in this class.
     */
    public function students(): HasMany
    {
        return $this->hasMany(Admission::class, 'class_id');
    }

    /**
     * Get the sections allocated to this class.
     */
    public function sections()
    {
        return $this->belongsToMany(Section::class, 'allocate_sections', 'class_id', 'section_id');
    }

    /**
     * Get the subjects allocated to this class.
     */
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'allocate_subjects', 'class_id', 'subject_id');
    }

    /**
     * Scope: Order by class name.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('id', 'asc');
    }
}
