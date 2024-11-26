<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    // Define the attributes that are mass assignable
    protected $fillable = ['title', 'description', 'author', 'source', 'category', 'published_at'];

    // Specify the attributes to be treated as date instances
    protected $dates = ['deleted_at'];

    // Hide deleted_at from model results
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * Define the many-to-many relationship between articles and preferences.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function preferences() {
        return $this->belongsToMany(Preference::class);
    }

    /**
     * Filter articles based on various criteria.
     *
     * @param array $filters Filters to be applied (e.g., keyword, category, source, date).
     * @param int $perPage Number of articles to display per page (default: 10).
     * @param int $currentPage Current page for pagination (default: 1).
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator Paginated list of filtered articles.
     */
    public static function filterArticles($filters, $perPage = 10, $currentPage = 1)
    {
        $query = self::query();
        
        // Filter by keyword in title or description
        if (!empty($filters['keyword'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['keyword'] . '%')
                ->orWhere('description', 'like', '%' . $filters['keyword'] . '%');
            });
        }

        // Filter by category
        if (!empty($filters['category'])) {
            $query->whereIn('category', (array) $filters['category']);
        }

        // Filter by source
        if (!empty($filters['source'])) {
            $query->whereIn('source', (array) $filters['source']);
        }

        // Filter by authour
        if (!empty($filters['author'])) {
            $query->whereIn('author', (array) $filters['author']);
        }

        // Filter by published date
        if (!empty($filters['date'])) {
            $query->whereDate('published_at', date('Y-m-d',strtotime($filters['date'])));
        }

        // Filter by published month
        if (!empty($filters['month'])) {
            $query->whereMonth('published_at', $filters['month']);
        }

        // Filter by published year
        if (!empty($filters['year'])) {
            $query->whereYear('published_at', $filters['year']);
        }

        // Add ordering
        $query->orderBy('published_at', 'desc');
        
        // Paginate results
        return $query->paginate($perPage, ['*'], 'page', $currentPage);
    }

    /**
     * Retrieve a single article by its ID.
     *
     * @param int $id Article ID to fetch.
     * @return Article|null The article instance if found, or null if not.
     */
    public static function showArticle($id) {
        $article = self::find($id);
        if ($article) {
            return $article;
        } else {
            return null;
        }
    }
    
}
