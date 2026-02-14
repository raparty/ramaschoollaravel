<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Position Model
 * 
 * Manages job positions/designations (Teacher, Principal, Clerk, etc.)
 * 
 * @property int $id
 * @property string $title Position title
 * @property int|null $department_id Department ID
 * @property string|null $description Position description
 * @property float|null $min_salary Minimum salary range
 * @property float|null $max_salary Maximum salary range
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Position extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'positions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'department_id',
        'description',
        'min_salary',
        'max_salary',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'min_salary' => 'decimal:2',
        'max_salary' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the department this position belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get all staff with this position.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function staff()
    {
        return $this->hasMany(Staff::class);
    }

    /**
     * Get the salary range as a formatted string.
     *
     * @return string
     */
    public function getSalaryRangeAttribute(): string
    {
        if ($this->min_salary && $this->max_salary) {
            return "₹" . number_format($this->min_salary, 2) . " - ₹" . number_format($this->max_salary, 2);
        }
        return 'Not specified';
    }

    /**
     * Get the count of staff in this position.
     *
     * @return int
     */
    public function getStaffCountAttribute(): int
    {
        return $this->staff()->count();
    }
}
