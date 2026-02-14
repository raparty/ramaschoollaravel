<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * FeePackage Model
 * 
 * Represents fee packages in the school ERP system
 * Maps to fees_package table
 * 
 * @property int $id
 * @property string $package_name
 * @property float $total_amount
 */
class FeePackage extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'fees_package';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'package_name',
        'total_amount',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    /**
     * Scope: Search by package name.
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where('package_name', 'LIKE', "%{$search}%");
    }

    /**
     * Scope: Order by name.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('package_name', 'asc');
    }

    /**
     * Get formatted amount.
     */
    public function getFormattedAmountAttribute(): string
    {
        return '₹' . number_format($this->total_amount, 2);
    }
}
