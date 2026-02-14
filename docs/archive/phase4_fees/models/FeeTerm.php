<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * FeeTerm Model
 * 
 * Represents fee terms/periods (e.g., Term 1, Term 2, Annual)
 * Maps to fees_term table
 * 
 * @property int $id
 * @property string $term_name
 */
class FeeTerm extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'fees_term';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'term_name',
    ];

    /**
     * Get the fees for this term.
     */
    public function studentFees(): HasMany
    {
        return $this->hasMany(StudentFee::class, 'fees_term', 'id');
    }

    /**
     * Scope: Order by name.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('term_name', 'asc');
    }
}
