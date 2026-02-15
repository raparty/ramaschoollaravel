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
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'account_category';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'category_name',
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
     * Scope to get only active categories (returns all for legacy compatibility)
     */
    public function scopeActive($query)
    {
        return $query;
    }

    /**
     * Get name accessor for legacy column.
     */
    public function getNameAttribute(): string
    {
        return $this->category_name ?? '';
    }

    /**
     * Get type badge class (default for legacy compatibility)
     */
    public function getTypeBadgeAttribute(): string
    {
        return 'secondary';
    }

    /**
     * Get status badge class (default for legacy compatibility)
     */
    public function getStatusBadgeAttribute(): string
    {
        return 'success';
    }

    /**
     * Get status text (default for legacy compatibility)
     */
    public function getStatusTextAttribute(): string
    {
        return 'Active';
    }
}
