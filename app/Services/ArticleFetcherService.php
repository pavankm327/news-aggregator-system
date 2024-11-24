<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Article;

use Carbon\Carbon;

class ArticleFetcherService
{
    public function fetchAndStoreArticles($api)
    {
        switch ($api) {
            case 'NewsAPI':
                $this->fetchFromNewsAPI();
                break;
            case 'The Guardian':
                $this->fetchFromGuardianAPI();
                break;
            case 'New York Times':
                $this->fetchFromNYTAPI();
                break;
        }
    }

    protected function fetchFromNewsAPI()
    {
        $response = Http::get(env('NEWS_API_URL'), [
            'apiKey' => env('NEWS_API_KEY'),
            'country' => 'us',
        ]);

        if ($response->successful()) {
            $this->storeArticles($response->json('articles'), 'NewsAPI');
        }
    }

    protected function storeArticles(array $articles, $source)
    {
        foreach ($articles as $article) {
            Article::updateOrCreate(
                ['title' => $article['title']],
                [
                    'description' => $article['description'] ?? 'No description available',
                    'author' => $article['author'] ?? 'Unknown',
                    'source' => $source,
                    'category' => $article['category'] ?? 'general',
                    'published_at' => Carbon::parse($article['publishedAt'])->format('Y-m-d H:i:s') ?? now(),
                ]
            );
        }
    }

    protected function fetchFromGuardianAPI()
    {
        $response = Http::get(env('GUARDIAN_API_URL'), [
            'api-key' => env('GUARDIAN_API_KEY'),
            // 'section' => 'world',
            'show-fields' => 'headline,byline,bodyText,',
        ]);
        if ($response->successful()) {
            $this->storeGuardianArticles($response->json('response.results'), 'The Guardian');
        }
    }  
    
    function storeGuardianArticles(array $articles, $source)
    {   
        foreach ($articles as $article) {
            Article::updateOrCreate(
                ['title' => $article['webTitle']],
                [
                    'description' => $article['fields']['bodyText'] ?? 'No description available',
                    'author' => $article['fields']['byline'] ?? 'Unknown',
                    'source' => $source ?? 'Unknown',
                    'category' => $article['sectionName'] ?? 'Unknown',
                    'published_at' => Carbon::parse($article['webPublicationDate'])->format('Y-m-d H:i:s') ?? now(),
                ]
            );
        }
    }


    protected function fetchFromNYTAPI()
    {
        $response = Http::get(env('NYT_API_URL'), [
            'api-key' => env('NYT_API_KEY'),
        ]);
        if ($response->successful()) {
            $this->storeNytArticles($response->json('results'), 'New York Times');
        }
    }

    public function storeNytArticles(array $articles, $source)
    {
        foreach ($articles as $article) {
            Article::updateOrCreate(
                ['title' => $article['title']],
                [
                    'description' => $article['abstract'] ?? 'No description available',
                    'author' => $article['byline'] ?? 'Unknown',
                    'source' => $source ?? 'Unknown',
                    'category' => $article['section'] ?? 'Unknown',
                    'published_at' => Carbon::parse($article['published_date'])->format('Y-m-d H:i:s') ?? now(),
                ]
            );
        }
    }
}
