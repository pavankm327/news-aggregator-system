<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Preference;
use App\Models\Article;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\BaseController as BaseController;

class PreferenceController extends BaseController
{
    /**
     * Set preferences for the authenticated user.
     *
     * @param Request $request
     * @return JsonResponse
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
     * Get preferences for the authenticated user.
     *
     * @return JsonResponse
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
     * Get personalized news feed based on the authenticated user preferences.
     *
     * @return JsonResponse
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
        return $this->sendResponse($articles, 'Personalized news feed retrieved successfully..!');
    }


}
