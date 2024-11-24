<?php

namespace App\Jobs;

// use Illuminate\Foundation\Queue\Queueable;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\ArticleFetcherService;

class FetchArticlesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $api;

    /**
     * Create a new job instance.
     */
    public function __construct($api)
    {
        $this->api = $api;
    }

    /**
     * Execute the job.
     */
    public function handle(ArticleFetcherService $articleFetcherService)
    {
        // Fetch and store articles for the specified API
        $articleFetcherService->fetchAndStoreArticles($this->api);
    }
}
