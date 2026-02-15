<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Income extends Model {
    protected $table = 'account_exp_income_detail';
    public $timestamps = false;
    protected $fillable = ['id', 'category_id', 'amount', 'description', 'date_of_txn', 'type', 'title'];
    
    protected $casts = [
        'date_of_txn' => 'date',
    ];
    
    // Type constant
    const TYPE_INCOME = 'Income';
    
    /**
     * Scope to filter by date range.
     */
    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date_of_txn', [$startDate, $endDate])
                     ->where('type', self::TYPE_INCOME);
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
        return $this->attributes['date_of_txn'] ?? null;
    }
}
