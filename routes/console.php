<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
// use App\Jobs\FetchArticlesJob;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote')->hourly();

Schedule::command('articles:fetch')
    ->everyMinute()
    ->runInBackground();

// $apis = ['NewsAPI', 'The Guardian', 'New York Times'];
// Schedule::job(new FetchArticlesJob($apis))->everySecond();