<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Article;
use Illuminate\Http\JsonResponse;

use App\Http\Controllers\BaseController as BaseController;
/**
 * @OA\Tag(
 *     name="Articles Management",
 *     description="Articles endpoints"
 * )
 */
class ArticleController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/article/filters",
     *     summary="Fetch filter data for articles",
     *     description="Fetch distinct sources, authors, and categories from the articles table for filter options.",
     *     tags={"Articles"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Data retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="object", example=null),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="sources",
     *                     type="array",
     *                     @OA\Items(type="string", example="string1, string2,..")
     *                 ),
     *                 @OA\Property(
     *                     property="authors",
     *                     type="array",
     *                     @OA\Items(type="string", example="string1, string2,..")
     *                 ),
     *                 @OA\Property(
     *                     property="categories",
     *                     type="array",
     *                     @OA\Items(type="string", example="string1, string2,..")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Error: Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated."),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Data not found",
     *         @OA\JsonContent(
     *         @OA\Property(property="success", type="boolean", example=true),
     *         @OA\Property(property="message", type="null", example=null),
     *         @OA\Property(
     *              property="data",
     *              type="object",
     *              @OA\Property(
     *                  property="sources",
     *                  type="null",
     *                  example=null
     *              ),
     *              @OA\Property(
     *                  property="authors",
     *                  type="null",
     *                  example=null
     *              ),
     *              @OA\Property(
     *                  property="categories",
     *                  type="null",
     *                  example=null
     *               ), 
     *             )
     *         )
     *     )
     * )
     */

    public function fetchDataForFiltersFromArticle() : JsonResponse
    {
        // Fetch distinct values for sources, authors, and categories
        $sources = Article::distinct()->pluck('source')->filter()->values()->toArray();
        $authors = Article::distinct()->pluck('author')->filter()->values()->toArray();
        $categories = Article::distinct()->pluck('category')->filter()->values()->toArray();

        // Prepare the response data
        $filterData = [
            'sources' => null,
            'authors' => null,
            'categories' => null,
        ];

        if (empty($sources) && empty($authors) && empty($categories)) {
            return $this->sendError('Data not found..!', $filterData, 404);    
        }
        
        $filterData = [
            'sources' => !empty($sources) ? $sources : null,
            'authors' => !empty($authors) ? $authors : null,
            'categories' => !empty($categories) ? $categories : null,
        ];
        // Return the filter data in a successful response
        return $this->sendResponse($filterData, null);
    }

    /**
     * @OA\Get(
     *     path="/api/articles",
     *     summary="Fetch articles with filters or by ID",
     *     description="Fetch a list of articles based on filters or fetch a specific article by its ID.",
     *     tags={"Articles"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="keyword",
     *         in="query",
     *         description="Keyword to search in the article title or description",
     *         required=false,
     *         @OA\Schema(type="string", example="World")
     *     ),
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Filter by category",
     *         required=false,
     *         @OA\Schema(type="string", example="technology")
     *     ),
     *     @OA\Parameter(
     *         name="source",
     *         in="query",
     *         description="Filter by source",
     *         required=false,
     *         @OA\Schema(type="string", example="NewsAPI")
     *     ),
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         description="Filter by published date (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date", example="2024-11-01")
     *     ),
     *     @OA\Parameter(
     *         name="month",
     *         in="query",
     *         description="Filter by published month (1-12)",
     *         required=false,
     *         @OA\Schema(type="integer", example=11)
     *     ),
     *     @OA\Parameter(
     *         name="year",
     *         in="query",
     *         description="Filter by published year",
     *         required=false,
     *         @OA\Schema(type="integer", example=2024)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of articles per page",
     *         required=false,
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="current_page", type="integer", example=1),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=59),
     *                     @OA\Property(property="title", type="string", example="The Amsterdam Attacks and the Long Shadow of ‘Pogroms’"),
     *                     @OA\Property(property="description", type="string", example="Many have used an old word to refer to recent events. Is it accurate?"),
     *                     @OA\Property(property="author", type="string", example="By Marc Tracy"),
     *                     @OA\Property(property="source", type="string", example="New York Times"),
     *                     @OA\Property(property="category", type="string", example="world"),
     *                     @OA\Property(property="published_at", type="string", format="date-time", example="2024-11-26"),
     *                 )
     *             ),
     *             @OA\Property(property="first_page_url", type="string", example="http://127.0.0.1:8000/api/articles?page=1"),
     *             @OA\Property(property="from", type="integer", example=1),
     *             @OA\Property(property="last_page", type="integer", example=1),
     *             @OA\Property(property="last_page_url", type="string", example="http://127.0.0.1:8000/api/articles?page=1"),
     *             @OA\Property(
     *                 property="links",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="url", type="string", nullable=true, example=null),
     *                     @OA\Property(property="label", type="string", example="&laquo; Previous"),
     *                     @OA\Property(property="active", type="boolean", example=false)
     *                 ),
     *                 @OA\Items(
     *                     @OA\Property(property="url", type="string", example="http://127.0.0.1:8000/api/articles?page=1"),
     *                     @OA\Property(property="label", type="string", example="1"),
     *                     @OA\Property(property="active", type="boolean", example=true)
     *                 ),
     *                 @OA\Items(
     *                     @OA\Property(property="url", type="string", nullable=true, example=null),
     *                     @OA\Property(property="label", type="string", example="Next &raquo;"),
     *                     @OA\Property(property="active", type="boolean", example=false)
     *                 )
     *             ),
     *             @OA\Property(property="next_page_url", type="string", nullable=true, example=null),
     *             @OA\Property(property="path", type="string", example="http://127.0.0.1:8000/api/articles"),
     *             @OA\Property(property="per_page", type="integer", example=100),
     *             @OA\Property(property="prev_page_url", type="string", nullable=true, example=null),
     *             @OA\Property(property="to", type="integer", example=1),
     *             @OA\Property(property="total", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Error: Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated."),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Article not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Article not found..!"),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     )
     * )
     */
    public function index(Request $request) : JsonResponse
    {
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

        // Handle case when the article is not found
        if (!$articles || $articles->total() == 0) {
            return $this->sendError('Article not found..!', null, 404);    
        }
        // Return paginated articles as JSON response
        return response()->json($articles);
    }

    /**
     * @OA\Get(
     *     path="/api/articles/show/{id}",
     *     summary="Fetch an article by ID",
     *     description="Fetch a specific article by its ID.",
     *     tags={"Articles"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the article to fetch",
     *         @OA\Schema(type="string", example=59)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Article fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Article fetched successfully..!"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=59),
     *                 @OA\Property(property="title", type="string", example="The Amsterdam Attacks and the Long Shadow of ‘Pogroms’"),
     *                 @OA\Property(property="description", type="string", example="Many have used an old word to refer to recent events. Is it accurate?"),
     *                 @OA\Property(property="author", type="string", example="By Marc Tracy"),
     *                 @OA\Property(property="source", type="string", example="New York Times"),
     *                 @OA\Property(property="category", type="string", example="world"),
     *                 @OA\Property(property="published_at", type="string", format="date-time", example="2024-11-26"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Article not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Article not found..!"),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid ID provided",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid ID..!"),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Error: Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated."),
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error, ID is required",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error..!"),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     )
     * )
     */

    public function show($id) : JsonResponse
    {
        // Check if the request contains an 'id' query parameter
        if ($id) {
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
        } else {
            return $this->sendError('Error..!', null, 422);
        }
    }
}
