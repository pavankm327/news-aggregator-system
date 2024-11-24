<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RunWorkerAndScheduler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:worker-scheduler';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run both queue worker and scheduler';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting queue worker and scheduler...');

        // Run the queue worker as a background process
        $this->runBackgroundProcess('php artisan queue:work --sleep=3 --tries=3');

        // Run the scheduler as a background process
        $this->runBackgroundProcess('php artisan schedule:work');

        $this->info('Both processes are running.');
    }

    protected function runBackgroundProcess($command)
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            pclose(popen("start /B $command", "r")); // Windows
        } else {
            shell_exec("$command > /dev/null 2>&1 &"); // Unix/Linux
        }
    }
}
