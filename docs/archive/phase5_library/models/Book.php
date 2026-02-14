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
 * 
 * @property int $id
 * @property string $book_name
 * @property string $book_no
 * @property string $author_name
 * @property int $book_cat_id
 * @property int $no_of_copies
 * @property string $book_edition
 * @property string $book_price
 * @property string $publisher
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Book extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'books';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'book_name',
        'book_no',
        'author_name',
        'book_cat_id',
        'no_of_copies',
        'book_edition',
        'book_price',
        'publisher',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'book_price' => 'decimal:2',
        'no_of_copies' => 'integer',
    ];

    /**
     * Get the category that owns the book.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(BookCategory::class, 'book_cat_id');
    }

    /**
     * Get all issues for this book.
     */
    public function issues(): HasMany
    {
        return $this->hasMany(BookIssue::class, 'book_id');
    }

    /**
     * Get active (not returned) issues.
     */
    public function activeIssues(): HasMany
    {
        return $this->hasMany(BookIssue::class, 'book_id')
            ->whereNull('return_date');
    }

    /**
     * Get available copies count.
     */
    public function getAvailableCopiesAttribute(): int
    {
        $issuedCount = $this->activeIssues()->count();
        return max(0, $this->no_of_copies - $issuedCount);
    }

    /**
     * Check if book is available for issue.
     */
    public function isAvailable(): bool
    {
        return $this->available_copies > 0;
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
              ->orWhere('author_name', 'LIKE', "%{$search}%")
              ->orWhere('book_no', 'LIKE', "%{$search}%");
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

        return $query->where('book_cat_id', $categoryId);
    }

    /**
     * Scope: Only available books.
     */
    public function scopeAvailable($query)
    {
        return $query->whereRaw('no_of_copies > (
            SELECT COUNT(*) FROM student_books 
            WHERE student_books.book_id = books.id 
            AND student_books.return_date IS NULL
        )');
    }

    /**
     * Get formatted price.
     */
    public function getFormattedPriceAttribute(): string
    {
        return '₹' . number_format($this->book_price, 2);
    }
}
