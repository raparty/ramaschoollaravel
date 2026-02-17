<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * HostelFeeStructure Model
 * 
 * Represents fee structure for hostel services
 */
class HostelFeeStructure extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hostel_id',
        'fee_name',
        'amount',
        'fee_type',
        'category',
        'description',
        'is_mandatory',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'hostel_id' => 'integer',
        'amount' => 'decimal:2',
        'is_mandatory' => 'boolean',
        'is_active' => 'boolean',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    /**
     * Get the hostel for this fee structure.
     */
    public function hostel(): BelongsTo
    {
        return $this->belongsTo(Hostel::class, 'hostel_id');
    }

    /**
     * Get all student fees using this structure.
     */
    public function studentFees(): HasMany
    {
        return $this->hasMany(HostelStudentFee::class, 'fee_structure_id');
    }

    /**
     * Scope: Only active fee structures.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Filter by fee type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('fee_type', $type);
    }

    /**
     * Scope: Filter by category.
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope: Only mandatory fees.
     */
    public function scopeMandatory($query)
    {
        return $query->where('is_mandatory', true);
    }

    /**
     * Scope: Order by fee name.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('fee_name', 'asc');
    }
}
