<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model {
    protected $table = 'account_exp_income_detail';
    public $timestamps = false;
    protected $fillable = ['id', 'category_id', 'amount', 'description', 'date_of_txn', 'type', 'title'];
    
    protected $casts = [
        'date_of_txn' => 'date',
    ];
    
    // Type constant
    const TYPE_EXPENSE = 'Expense';
    
    /**
     * Scope to filter by date range.
     */
    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date_of_txn', [$startDate, $endDate])
                     ->where('type', self::TYPE_EXPENSE);
    }
    
    /**
     * Relationship to category.
     */
    public function category()
    {
        return $this->belongsTo(AccountCategory::class, 'category_id');
    }
    
    /**
     * Accessor for date attribute (alias for date_of_txn).
     */
    public function getDateAttribute()
    {
        return $this->date_of_txn;
    }
}
