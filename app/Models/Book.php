<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Book Model
 * 
 * Represents books in the library system
 * Maps to book_manager table in database
 * 
 * @property int $book_id
 * @property int $book_category_id
 * @property string $book_number
 * @property string $book_name
 * @property string|null $book_author
 * @property string|null $book_description
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Book extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'book_manager';
    
    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'book_id';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'book_category_id',
        'book_number',
        'book_name',
        'book_author',
        'book_description',
    ];
    
    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * Get the category that owns the book.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(BookCategory::class, 'book_category_id');
    }

    /**
     * Get all issues for this book.
     */
    public function issues(): HasMany
    {
        return $this->hasMany(BookIssue::class, 'book_number', 'book_number');
    }

    /**
     * Get active (not returned) issues.
     */
    public function activeIssues(): HasMany
    {
        return $this->hasMany(BookIssue::class, 'book_number', 'book_number')
            ->whereNull('return_date');
    }

    /**
     * Scope: Search books by name, author, or book number.
     */
    public function scopeSearch($query, ?string $search)
    {
        if (!$search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('book_name', 'LIKE', "%{$search}%")
              ->orWhere('book_author', 'LIKE', "%{$search}%")
              ->orWhere('book_number', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Scope: Filter by category.
     */
    public function scopeByCategory($query, ?int $categoryId)
    {
        if (!$categoryId) {
            return $query;
        }

        return $query->where('book_category_id', $categoryId);
    }
}
