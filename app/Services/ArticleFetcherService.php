<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Article;
use Carbon\Carbon;
use Log;

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
        $url = config('services.news_api.url');
        $key = config('services.news_api.key');
        $response = Http::get($url, [
            'apiKey' => $key,
            'country' => 'us',
        ]);

        if ($response->failed()) {
            Log::error("API request failed: " . $response->body());
            return;
        }

        $this->storeArticles($response->json('articles'), 'NewsAPI');
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
                    'published_at' => isset($article['publishedAt']) ? Carbon::parse($article['publishedAt'])->format('Y-m-d H:i:s') : now(),
                ]
            );
        }
    }

    protected function fetchFromGuardianAPI()
    {
        $url = config('services.guardian_api.url');
        $key = config('services.guardian_api.key');
        $response = Http::get($url, [
            'api-key' => $key,
            'show-fields' => 'headline,byline,bodyText,',
        ]);

        if ($response->failed()) {
            Log::error("API request failed: " . $response->body());
            return;
        }

        $this->storeGuardianArticles($response->json('response.results'), 'The Guardian');
    }

    protected function storeGuardianArticles(array $articles, $source)
    {
        foreach ($articles as $article) {
            Article::updateOrCreate(
                ['title' => $article['webTitle']],
                [
                    'description' => $article['fields']['bodyText'] ?? 'No description available',
                    'author' => $article['fields']['byline'] ?? 'Unknown',
                    'source' => $source ?? 'Unknown',
                    'category' => $article['sectionName'] ?? 'Unknown',
                    'published_at' => isset($article['webPublicationDate']) ? Carbon::parse($article['webPublicationDate'])->format('Y-m-d H:i:s') : now(),
                ]
            );
        }
    }

    protected function fetchFromNYTAPI()
    {
        $url = config('services.nyt_api.url');
        $key = config('services.nyt_api.key');
        $response = Http::get($url, [
            'api-key' => $key,
        ]);

        if ($response->failed()) {
            Log::error("API request failed: " . $response->body());
            return;
        }

        $this->storeNytArticles($response->json('results'), 'New York Times');
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
                    'published_at' => isset($article['published_date']) ? Carbon::parse($article['published_date'])->format('Y-m-d H:i:s') : now(),
                ]
            );
        }
    }
}
