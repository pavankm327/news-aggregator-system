<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\FetchArticlesJob;

class FetchArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'articles:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch articles from external news APIs';

    protected $articleFetcher;

    public function handle()
    {
        $apis = ['NewsAPI', 'The Guardian', 'New York Times'];

        foreach ($apis as $api) {
            // Dispatch a job for each API
            FetchArticlesJob::dispatch($api);
        }

        $this->info('Fetch jobs dispatched for all APIs.');
    }
}
