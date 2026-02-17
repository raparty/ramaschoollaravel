<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * HostelImprestWallet Model
 * 
 * Represents a student's imprest wallet/account
 */
class HostelImprestWallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'opening_balance',
        'current_balance',
        'total_credited',
        'total_debited',
        'wallet_opened_date',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'student_id' => 'integer',
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'total_credited' => 'decimal:2',
        'total_debited' => 'decimal:2',
        'wallet_opened_date' => 'date',
        'is_active' => 'boolean',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    /**
     * Get the student for this wallet.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Admission::class, 'student_id');
    }

    /**
     * Get all expenses from this wallet.
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(HostelExpense::class, 'wallet_id');
    }

    /**
     * Scope: Only active wallets.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Wallets with low balance.
     */
    public function scopeLowBalance($query, $threshold = 100)
    {
        return $query->where('current_balance', '<', $threshold)
            ->where('current_balance', '>', 0);
    }

    /**
     * Scope: Order by student name.
     */
    public function scopeOrdered($query)
    {
        return $query->join('admissions', 'hostel_imprest_wallets.student_id', '=', 'admissions.id')
            ->orderBy('admissions.student_name', 'asc')
            ->select('hostel_imprest_wallets.*');
    }

    /**
     * Check if wallet has sufficient balance.
     */
    public function hasSufficientBalance(float $amount): bool
    {
        return $this->current_balance >= $amount;
    }

    /**
     * Credit amount to wallet.
     */
    public function credit(float $amount): void
    {
        $this->current_balance += $amount;
        $this->total_credited += $amount;
        $this->save();
    }

    /**
     * Debit amount from wallet.
     */
    public function debit(float $amount): void
    {
        if (!$this->hasSufficientBalance($amount)) {
            throw new \Exception('Insufficient balance in wallet');
        }
        
        $this->current_balance -= $amount;
        $this->total_debited += $amount;
        $this->save();
    }
}
