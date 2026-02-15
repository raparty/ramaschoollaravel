<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Account Category Model
 * 
 * Manages income and expense categories for financial tracking
 */
class AccountCategory extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'account_category';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'type',
        'description',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get income records for this category
     */
    public function incomes()
    {
        return $this->hasMany(Income::class, 'category_id');
    }

    /**
     * Get expense records for this category
     */
    public function expenses()
    {
        return $this->hasMany(Expense::class, 'category_id');
    }

    /**
     * Scope to get only income categories
     */
    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    /**
     * Scope to get only expense categories
     */
    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    /**
     * Scope to get only active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get type badge class
     */
    public function getTypeBadgeAttribute(): string
    {
        return match($this->type) {
            'income' => 'success',
            'expense' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeAttribute(): string
    {
        return $this->is_active ? 'success' : 'secondary';
    }

    /**
     * Get status text
     */
    public function getStatusTextAttribute(): string
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }
}
