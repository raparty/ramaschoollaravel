<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

/**
 * Expense Model
 * 
 * Manages expense transactions for the school
 */
class Expense extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'category_id',
        'amount',
        'date',
        'receipt_number',
        'description',
        'payment_method',
        'recorded_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
    ];

    /**
     * Get the category for this expense
     */
    public function category()
    {
        return $this->belongsTo(AccountCategory::class, 'category_id');
    }

    /**
     * Get the staff who recorded this expense
     */
    public function recorder()
    {
        return $this->belongsTo(Staff::class, 'recorded_by');
    }

    /**
     * Scope to filter by category
     */
    public function scopeForCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeForDateRange($query, string $startDate, string $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Scope to filter by month
     */
    public function scopeForMonth($query, int $month, int $year)
    {
        return $query->whereMonth('date', $month)
                     ->whereYear('date', $year);
    }

    /**
     * Scope to filter by year
     */
    public function scopeForYear($query, int $year)
    {
        return $query->whereYear('date', $year);
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute(): string
    {
        return '₹' . number_format($this->amount, 2);
    }

    /**
     * Get formatted date
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->date->format('d M, Y');
    }
}
