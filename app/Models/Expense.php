<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model {
    protected $table = 'account_exp_income_detail';
    public $timestamps = false;
    protected $fillable = ['id', 'category_id', 'amount', 'description', 'date_of_txn', 'type', 'title'];
    
    /**
     * Scope to filter by date range.
     */
    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date_of_txn', [$startDate, $endDate])
                     ->where('type', 'Expense');
    }
    
    /**
     * Relationship to category.
     */
    public function category()
    {
        return $this->belongsTo(AccountCategory::class, 'category_id');
    }
}
