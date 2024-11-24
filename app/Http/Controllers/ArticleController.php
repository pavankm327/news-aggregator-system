<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Article;
use Illuminate\Http\JsonResponse;

use App\Http\Controllers\BaseController as BaseController;

class ArticleController extends BaseController
{
    /**
     * Handle fetching source, author and cotegories from articles table
     *
     * @return JsonResponse
     */
    public function fetchDataForFiltersFromArticle() : JsonResponse
    {
        // Fetch distinct values for sources, authors, and categories
        $sources = Article::distinct()->pluck('source')->filter()->values();
        $authors = Article::distinct()->pluck('author')->filter()->values();
        $categories = Article::distinct()->pluck('category')->filter()->values();


        // Prepare the response data
        $filterData = [
            'sources' => $sources,
            'authors' => $authors,
            'categories' => $categories,
        ];

        if (empty($filterData)) {
            return $this->sendError('Data not found..!', null, 404);    
        }

        // Return the filter data in a successful response
        return $this->sendResponse($filterData, 'data retrieved successfully.');
    }

    /**
     * Handle fetching articles with or without filters and query parameters.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request) : JsonResponse
    {
        // Check if the request contains an 'id' query parameter
        if ($request->has('id')) {
            $id = $request->query('id');

            // Fallback to extracting the query string if 'id' is not explicitly present
            if (!$id && $request->getQueryString()) {
                $id = $request->getQueryString(); // Extracts the value after '?'
            }
            
            // Validate the ID to ensure it is numeric
            if (!is_numeric($id)) {
                return $this->sendError('Invalid ID..!', null, 400);      
            }
        
            // Fetch the article by ID
            $article = Article::showArticle($id);
            
            // Handle case when the article is not found
            if (!$article) {
                return $this->sendError('Article not found..!', null, 404);    
            }
            // Return the fetched article
            return $this->sendResponse($article, 'Article fetched successfully..!');
        }

        // Collect filters from the request
        $filters = [
            'keyword' => $request->input('keyword'),
            'category' => $request->input('category'),
            'source' => $request->input('source'),
            'date' => $request->input('date'),
            'month' => $request->input('month'),
            'year' => $request->input('year'),
        ];
        
        // Handle pagination parameters with defaults
        $perPage = (int) $request->input('per_page', 10);
        $currentPage = (int) $request->input('page', 1);

        // Fetch filtered articles with pagination applied
        $articles = Article::filterArticles($filters, $perPage, $currentPage);

        // Return paginated articles as JSON response
        return response()->json($articles);
    }

}
