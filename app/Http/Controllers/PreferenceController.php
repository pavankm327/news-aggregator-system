<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Preference;
use App\Models\Article;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\BaseController as BaseController;

/**
 * @OA\Tag(
 *     name="Articles Management",
 *     description="Articles endpoints"
 * )
 */
class PreferenceController extends BaseController
{
    /**
     * @OA\Post(
     *     path="/api/preferences",
     *     summary="Set preferences for the authenticated user",
     *     description="This endpoint allows the authenticated user to set their preferences for sources, categories, and authors.",
     *     operationId="setPreferences",
     *     tags={"Preferences"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"sources", "categories", "authors"},
     *             @OA\Property(
     *                 property="sources", 
     *                 type="array", 
     *                 items=@OA\Items(type="string"), 
     *                 description="List of preferred sources",
     *                 example={"Douglas and Sons", "Schmitt and Sons", "Kozey and Sons"}
     *             ),
     *             @OA\Property(
     *                 property="categories", 
     *                 type="array", 
     *                 items=@OA\Items(type="string"), 
     *                 description="List of preferred categories",
     *                 example={"Health", "Sports", "Technology"}
     *             ),
     *             @OA\Property(
     *                 property="authors", 
     *                 type="array", 
     *                 items=@OA\Items(type="string"), 
     *                 description="List of preferred authors",
     *                 example={"Vanessa Altenwerth", "Dr. Dino Hyatt II"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Preferences updated successfully.", 
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Preferences updated successfully..!"),
     *             @OA\Property(property="data",
     *                                
     *             type="object",
     *             @OA\Property(property="user_id", type="integer", example="10"),
     *             @OA\Property(property="id", type="integer", example="2"),
     *             @OA\Property(
     *                 property="preferred_sources", 
     *                 type="array", 
     *                 items=@OA\Items(type="string"), 
     *                 description="List of preferred sources",
     *                 example={"Douglas and Sons", "Schmitt and Sons", "Kozey and Sons"}
     *             ),
     *             @OA\Property(
     *                 property="preferred_categories", 
     *                 type="array", 
     *                 items=@OA\Items(type="string"), 
     *                 description="List of preferred categories",
     *                 example={"Health", "Sports", "Technology"}
     *             ),
     *             @OA\Property(
     *                 property="preferred_authors", 
     *                 type="array", 
     *                 items=@OA\Items(type="string"), 
     *                 description="List of preferred authors",
     *                 example={"Vanessa Altenwerth", "Dr. Dino Hyatt II"}
     *             )
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
     *         response=422,
     *         description="Error: Unprocessable Content",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation Error."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="sources", type="array", @OA\Items(type="string", example="The sources field is required.")),
     *                 @OA\Property(property="categories", type="array", @OA\Items(type="string", example="The categories field is required.")),
     *                 @OA\Property(property="authors", type="array", @OA\Items(type="string", example="The password authors is required."))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error: Bad Request",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Preferences update failed..!"),
     *             @OA\Property(property="data", type="object", example=null)
     *         )
     *     )
     * )
     */
    public function setPreferences(Request $request) : JsonResponse
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'sources' => 'array|required',
            'categories' => 'array|required',
            'authors' => 'array|required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error..!', $validator->errors(), 422);       
        }
        
        // Update preferences
        $preferences = Preference::updatePreferences(auth()->user()->id, $request->only('sources', 'categories', 'authors'));
        
        // Handle case where preferences are not updated
        if (!$preferences) {
            return $this->sendError('Preferences update failed..!', null, 400);
        }

        // Return the updated preferences
        return $this->sendResponse($preferences, 'Preferences updated successfully..!');
    }

    /**
     * @OA\Get(
     *     path="/api/preferences",
     *     summary="Get preferences for the authenticated user",
     *     description="This endpoint allows the authenticated user to retrieve their saved preferences for sources, categories, and authors.",
     *     operationId="getPreferences",
     *     tags={"Preferences"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Preferences fetched successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Preferences fetched successfully..!"),
     *             @OA\Property(property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example="2"),
     *                 @OA\Property(property="user_id", type="integer", example="10"),
     *                 @OA\Property(property="preferred_sources", type="array", 
     *                     items=@OA\Items(type="string"), 
     *                     description="List of preferred sources", 
     *                     example={"Douglas and Sons", "Schmitt and Sons", "Kozey and Sons"}
     *                 ),
     *                 @OA\Property(property="preferred_categories", type="array", 
     *                     items=@OA\Items(type="string"), 
     *                     description="List of preferred categories", 
     *                     example={"Health", "Sports", "Technology"}
     *                 ),
     *                 @OA\Property(property="preferred_authors", type="array", 
     *                     items=@OA\Items(type="string"), 
     *                     description="List of preferred authors", 
     *                     example={"Vanessa Altenwerth", "Dr. Dino Hyatt II"}
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Error: Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Error: Not Found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Preferences not found.")
     *         )
     *     )
     * )
     */
    public function getPreferences() : JsonResponse
    {
        // Fetch preferences
        $preferences = Preference::getPreferencesByUser(auth()->user()->id);
        
        if (!$preferences) {
            return $this->sendError('Preferences not found.', null, 404);
        }

        return $this->sendResponse($preferences, 'Preferences fetched successfully..!');
    }

    /**
     * @OA\Get(
     *     path="/api/preferences/feed",
     *     summary="Get personalized news feed based on user preferences",
     *     description="This endpoint allows the authenticated user to retrieve a personalized news feed based on their preferences for sources, categories, and authors.",
     *     operationId="fetchPersonalizedFeed",
     *     tags={"Preferences"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Filter by category",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="source",
     *         in="query",
     *         description="Filter by source",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="author",
     *         in="query",
     *         description="Filter by author",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of articles per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
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
     *             @OA\Property(property="first_page_url", type="string", example="http://127.0.0.1:8000/api/preferences/feed?page=1"),
     *             @OA\Property(property="from", type="integer", example=1),
     *             @OA\Property(property="last_page", type="integer", example=1),
     *             @OA\Property(property="last_page_url", type="string", example="http://127.0.0.1:8000/api/preferences/feed?page=1"),
     *             @OA\Property(
     *                 property="links",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="url", type="string", nullable=true, example=null),
     *                     @OA\Property(property="label", type="string", example="&laquo; Previous"),
     *                     @OA\Property(property="active", type="boolean", example=false)
     *                 ),
     *                 @OA\Items(
     *                     @OA\Property(property="url", type="string", example="http://127.0.0.1:8000/api/preferences/feed?page=1"),
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
     *             @OA\Property(property="path", type="string", example="http://127.0.0.1:8000/api/preferences/feed"),
     *             @OA\Property(property="per_page", type="integer", example=100),
     *             @OA\Property(property="prev_page_url", type="string", nullable=true, example=null),
     *             @OA\Property(property="to", type="integer", example=1),
     *             @OA\Property(property="total", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Error: Not Found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No articles found matching your preferences."),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Error: Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function fetchPersonalizedFeed(Request $request): JsonResponse
    {
        // Fetch preferences
        $preferences = Preference::getPreferencesByUser(auth()->user()->id);
        
        if (!$preferences) {
            return $this->sendError('Preferences not found.', null, 404);
        }

        $preferredCategories = $preferences->preferred_categories;
        $preferredSources = $preferences->preferred_sources;
        $preferredAuthors = $preferences->preferred_authors;
        
        // Collect filters from the request
        $filters = [
            'category' => $request->input('category'),
            'source' => $request->input('source'),
            'author' => $request->input('author'),
        ];

        if(empty($filters['category']) && empty($filters['source']) && empty($filters['author'])) {
            // Use stored preferences as defaults if filters are not provided
            if (isset($preferredCategories) && !empty($preferredCategories[0])) {
                $filters['category'] = $preferredCategories;
            }
            if (isset($preferredSources) && !empty($preferredSources[0])) {
                $filters['source'] = $preferredSources;
            }
            if (isset($preferredAuthors) && !empty($preferredAuthors[0])) {
                $filters['author'] = $preferredAuthors;
            }
        }
        
        // Handle pagination parameters with defaults
        $perPage = (int) $request->input('per_page', 10);
        $currentPage = (int) $request->input('page', 1);

        // Fetch filtered articles with pagination applied
        $articles = Article::filterArticles($filters, $perPage, $currentPage);
        
        // Handle case where no articles match preferences
        if ($articles->isEmpty()) {
            return $this->sendError('No articles found matching your preferences.', null, 404);
        }

        // Return personalized articles
        return response()->json($articles);
    }


}
