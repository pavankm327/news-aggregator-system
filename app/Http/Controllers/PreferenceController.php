<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Preference;
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
            'sources.*' => 'string|max:255', // Each source must be a string
            'categories.*' => 'string|max:255', // Each source must be a string
            'authors.*' => 'string|max:255', // Each source must be a string
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
}
