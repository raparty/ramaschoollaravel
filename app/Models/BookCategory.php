<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Book Category Model
 * 
 * Represents book categories in the library
 * 
 * @property int $category_id
 * @property string $category_name
 */
class BookCategory extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'library_categories';
    
    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'category_id';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'category_name',
    ];
    
    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * Get all books in this category.
     */
    public function books(): HasMany
    {
        return $this->hasMany(Book::class, 'book_category_id', 'category_id');
    }

    /**
     * Get count of books in this category.
     */
    public function getBooksCountAttribute(): int
    {
        return $this->books()->count();
    }

    /**
     * Scope: Order by name.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('category_name');
    }
}
