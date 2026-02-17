<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * HostelExpenseCategory Model
 * 
 * Represents categories for hostel expenses
 */
class HostelExpenseCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'requires_approval',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'requires_approval' => 'boolean',
        'is_active' => 'boolean',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    /**
     * Get all expenses in this category.
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(HostelExpense::class, 'category_id');
    }

    /**
     * Scope: Only active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Categories requiring approval.
     */
    public function scopeRequiresApproval($query)
    {
        return $query->where('requires_approval', true);
    }

    /**
     * Scope: Order by name.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('name', 'asc');
    }
}
