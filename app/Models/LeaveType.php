<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * LeaveType Model
 * 
 * Manages different types of leave that staff can apply for
 * 
 * @property int $id
 * @property string $name Leave type name
 * @property string $description Leave type description
 * @property int $max_days Maximum days allowed per year
 * @property bool $requires_approval Whether approval is required
 * @property bool $is_active Whether leave type is active
 */
class LeaveType extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'leave_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'max_days',
        'requires_approval',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'requires_approval' => 'boolean',
        'is_active' => 'boolean',
        'max_days' => 'integer',
    ];

    /**
     * Get the staff leaves for this leave type.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function staffLeaves()
    {
        return $this->hasMany(StaffLeave::class);
    }

    /**
     * Scope a query to only include active leave types.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive leave types.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }
}
