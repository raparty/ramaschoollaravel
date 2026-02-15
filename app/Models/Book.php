<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Book Model
 * * Represents books in the library system.
 * Maps to the legacy 'book_manager' table.
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
     * Indicates if the model should be timestamped.
     * Set to true now that we added created_at/updated_at.
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'book_category_id',
        'book_number',
        'book_name',
        'book_author',
        'book_description',
        'book_edition',
        'book_price',
        'no_of_copies',
        'available_copies',
    ];

    /**
     * The attributes that should be cast.
     * Essential for the .format() method to work in the view.
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'book_price' => 'decimal:2',
    ];

    /**
     * Relationship: Get all book issues.
     * Maps to 'student_books_details' using 'book_number'.
     */
    public function issues(): HasMany
    {
        // Foreign Key: 'book_number' in student_books_details
        // Local Key: 'book_number' in book_manager
        return $this->hasMany(BookIssue::class, 'book_number', 'book_number');
    }

    /**
     * Relationship: Get active (not returned) issues.
     * Maps to 'student_books_details' using 'book_number'.
     */
    public function activeIssues(): HasMany
    {
        // Foreign Key: 'book_number' in student_books_details
        // Local Key: 'book_number' in book_manager
        return $this->hasMany(BookIssue::class, 'book_number', 'book_number')
                    ->whereNull('return_date');
    }

    /**
     * Check if the book can be issued (single instance).
     * Fixes BadMethodCallException in show.blade.php.
     */
    public function isAvailable(): bool
    {
        return $this->available_copies > 0;
    }

    /**
     * Query Scope: Book::available().
     * Fixes BadMethodCallException in BookIssueController.
     */
    public function scopeAvailable($query)
    {
        return $query->where('available_copies', '>', 0);
    }

    /**
     * Get the category that owns the book.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(BookCategory::class, 'book_category_id');
    }

    /**
     * Scope: Search books by name, author, or book number.
     */
    public function scopeSearch($query, ?string $search)
    {
        if (!$search) return $query;

        return $query->where(function ($q) use ($search) {
            $q->where('book_name', 'LIKE', "%{$search}%")
              ->orWhere('book_author', 'LIKE', "%{$search}%")
              ->orWhere('book_number', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Scope: Filter by category.
     */
    public function scopeByCategory($query, $categoryId)
    {
        if (empty($categoryId)) {
            return $query;
        }
        
        return $query->where('book_category_id', $categoryId);
    }
}
