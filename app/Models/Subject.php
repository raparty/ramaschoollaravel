<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Subject Model
 * 
 * Represents academic subjects
 * Matches migration schema: database/migrations/2026_02_14_072514_create_core_tables.php
 * 
 * @property int $id
 * @property string $name Subject name
 * @property string|null $code Subject code
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Subject extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'code',
    ];

    /**
     * Get the exam subjects for this subject.
     */
    public function examSubjects(): HasMany
    {
        return $this->hasMany(ExamSubject::class);
    }
}
