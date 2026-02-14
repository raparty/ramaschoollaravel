<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Department Model
 * 
 * Manages school departments (Academic, Administration, etc.)
 * 
 * @property int $id
 * @property string $name Department name
 * @property string|null $description Department description
 * @property int|null $hod_id Head of Department staff ID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Department extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'departments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'hod_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get all staff in this department.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function staff()
    {
        return $this->hasMany(Staff::class);
    }

    /**
     * Get the head of department.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function hod()
    {
        return $this->belongsTo(Staff::class, 'hod_id');
    }

    /**
     * Get all positions in this department.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function positions()
    {
        return $this->hasMany(Position::class);
    }

    /**
     * Get the count of active staff in department.
     *
     * @return int
     */
    public function getActiveStaffCountAttribute(): int
    {
        return $this->staff()->active()->count();
    }

    /**
     * Get the total staff count.
     *
     * @return int
     */
    public function getTotalStaffAttribute(): int
    {
        return $this->staff()->count();
    }
}
