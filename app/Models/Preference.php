<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Preference extends Model
{
    use SoftDeletes;
    
    // Define the attributes that are mass assignable
    protected $fillable = ['user_id', 'preferred_sources', 'preferred_categories', 'preferred_authors'];

    protected $casts = [
        'preferred_sources' => 'array',
        'preferred_categories' => 'array',
        'preferred_authors' => 'array',
    ];

    // Specify the attributes to be treated as date instances
    protected $dates = ['deleted_at'];

    // Hide deleted_at from model results
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * Define the relationship between Preference and User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() 
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Update the preferences for a user.
     *
     * @param int $userId
     * @param array $preferences
     * @return Preference|null
     */
    public static function updatePreferences(int $userId, array $preferences) : ?Preference
    {        
        // Sanitize preferences
        $sanitizedData = [
            'preferred_sources' => json_encode(array_map('strip_tags', $preferences['sources'])),
            'preferred_categories' => json_encode(array_map('strip_tags', $preferences['categories'])),
            'preferred_authors' => json_encode(array_map('strip_tags', $preferences['authors'])),
        ];
        
        // Check if the user already has preferences
        $existingPreferences = self::where('user_id', $userId)->first();

        if ($existingPreferences) {
            // Update existing preferences
            $existingPreferences->update($sanitizedData);

            return self::decodeJsonFields($existingPreferences);
        } else {
            // Create new preferences if none exist
            $sanitizedData['user_id'] = $userId;
            $newPreference = self::create($sanitizedData);

            return self::decodeJsonFields($newPreference);
        }
    }

    /**
     * Get preferences by user ID.
     *
     * @param int $userId
     * @return Preference|null
     */
    public static function getPreferencesByUser(int $userId) : ?Preference
    {
        $preferences = self::where('user_id', $userId)->first();
    
        if ($preferences) {
            // Decode JSON fields
            $preferences = self::decodeJsonFields($preferences);
        }

        return $preferences;
    }
    
    /**
     * Decode JSON fields to arrays.
     *
     * @param Preference $preferences
     * @return Preference
     */
    private static function decodeJsonFields(Preference $preferences) : Preference
    {
        // Check if JSON is valid
        $preferences->preferred_sources = json_decode($preferences->preferred_sources, true) ?? [];
        $preferences->preferred_categories = json_decode($preferences->preferred_categories, true) ?? [];
        $preferences->preferred_authors = json_decode($preferences->preferred_authors, true) ?? [];

        return $preferences;
    }
}
