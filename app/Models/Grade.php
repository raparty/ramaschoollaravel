<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Grade Model
 * 
 * ⚠️ WARNING: THIS MODEL IS NON-FUNCTIONAL ⚠️
 * The 'grades' table does NOT exist in the database migration.
 * To enable this model, execute: database/schema/missing-tables.sql
 * 
 * Represents grading system configuration
 * 
 * @property int $id
 * @property string $grade Grade name (A+, A, B+, B, C, D, F)
 * @property float $min_percentage Minimum percentage
 * @property float $max_percentage Maximum percentage
 * @property float $points Grade points
 * @property string|null $description Grade description
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Grade extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'grade',
        'min_percentage',
        'max_percentage',
        'points',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'min_percentage' => 'float',
        'max_percentage' => 'float',
        'points' => 'float',
    ];

    /**
     * Get formatted percentage range.
     */
    public function getRangeAttribute(): string
    {
        return $this->min_percentage . '% - ' . $this->max_percentage . '%';
    }

    /**
     * Check if a percentage falls in this grade range.
     */
    public function isInRange(float $percentage): bool
    {
        return $percentage >= $this->min_percentage && $percentage <= $this->max_percentage;
    }

    /**
     * Get grade badge class.
     */
    public function getBadgeClassAttribute(): string
    {
        return match ($this->grade) {
            'A+', 'A' => 'success',
            'B+', 'B' => 'primary',
            'C+', 'C' => 'info',
            'D' => 'warning',
            'F' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Scope to find grade for a given percentage.
     */
    public function scopeForPercentage($query, float $percentage)
    {
        return $query->where('min_percentage', '<=', $percentage)
                     ->where('max_percentage', '>=', $percentage)
                     ->first();
    }
}
