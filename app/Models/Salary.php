<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Salary Model
 * 
 * ⚠️ WARNING: THIS MODEL IS NON-FUNCTIONAL ⚠️
 * The 'staff_salaries' table does NOT exist in the database migration.
 * To enable this model, execute: database/schema/missing-tables.sql
 * 
 * Manages staff salary records and payments
 * 
 * @property int $id
 * @property int $staff_id Staff ID
 * @property int $month Month (1-12)
 * @property int $year Year
 * @property float $basic_salary Basic salary amount
 * @property float $allowances Total allowances
 * @property float $deductions Total deductions
 * @property float $net_salary Net salary (calculated)
 * @property string $status Status (pending/paid)
 * @property string|null $payment_date Payment date
 * @property string|null $payment_method Payment method
 * @property string|null $notes Notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Salary extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'staff_salaries';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'staff_id',
        'month',
        'year',
        'basic_salary',
        'allowances',
        'deductions',
        'net_salary',
        'status',
        'payment_date',
        'payment_method',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
        'basic_salary' => 'decimal:2',
        'allowances' => 'decimal:2',
        'deductions' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'payment_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be appended to the model's array form.
     *
     * @var array
     */
    protected $appends = ['month_year_display'];

    /**
     * Get the staff member who receives this salary.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    /**
     * Scope a query to only include paid salaries.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope a query to only include pending salaries.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to filter by month and year.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $month
     * @param int $year
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForPeriod($query, int $month, int $year)
    {
        return $query->where('month', $month)->where('year', $year);
    }

    /**
     * Get the month-year display format.
     *
     * @return string
     */
    public function getMonthYearDisplayAttribute(): string
    {
        $monthName = date('F', mktime(0, 0, 0, $this->month, 1));
        return "{$monthName} {$this->year}";
    }

    /**
     * Calculate and update net salary.
     *
     * @return void
     */
    public function calculateNetSalary(): void
    {
        $this->net_salary = $this->basic_salary + $this->allowances - $this->deductions;
        $this->save();
    }

    /**
     * Mark salary as paid.
     *
     * @param string|null $paymentMethod
     * @return bool
     */
    public function markAsPaid(?string $paymentMethod = null): bool
    {
        $this->status = 'paid';
        $this->payment_date = now();
        if ($paymentMethod) {
            $this->payment_method = $paymentMethod;
        }
        return $this->save();
    }

    /**
     * Check if salary is paid.
     *
     * @return bool
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Check if salary is pending.
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
