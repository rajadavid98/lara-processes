<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Process;
use Illuminate\Process\Pool;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('test-processes', function () {

    /**   1. Get output after the process completed   */
    $this->info(Process::run('npm run build')->output());


    /**   2. Get Real-Time Process Output   */
    Process::run('npm run build', function ($type, $output) {
        $this->info($output);
    });


    /**   3. Start and wait methods   */
    $process = Process::start('npm run build');
    $process->wait();
    $this->info($process->output());


    /**   4. If want to display something on running? or check it is running?  */
    $process = Process::start('npm run build');
    while ($process->running()) {
        $this->info('running...');
        sleep(1);
    }
    $process->wait();
    $this->info($process->output());
    $this->info('All done!');


    /**   5. Asynchronous Process Output  */
    $process = Process::timeout(120)->path(base_path())->start('php artisan app:user-list');
    while ($process->running()) {
        echo $process->latestOutput();
        echo $process->latestErrorOutput();
        sleep(1);
    }
    $process->wait();
    $this->info($process->output());
    $this->info('All done!');

    /**   6. Concurrent Processes  */
    $pool = Process::pool(function (Pool $pool) {
        $pool->path(base_path())->command('php artisan app:user-list');
        $pool->path(base_path())->command('php artisan app:user-list id');
        $pool->path(base_path())->command('php artisan app:user-list email');
    })->start(function (string $type, string $output, int $key) {
        $this->info($output);
        $this->info($key);
    });
    $results = $pool->wait();
    $this->info($results[0]->output());
    $this->info($results[1]->output());
    $this->info($results[2]->output());


    /**   7. Concurrent Processes  */
    [$first, $second, $third] = Process::concurrently(function (Pool $pool) {
        $pool->path(base_path())->command('php artisan app:user-list');
        $pool->path(base_path())->command('php artisan app:user-list id');
        $pool->path(base_path())->command('php artisan app:user-list email');
    });
    $this->info($first->output());


    /**   8. Concurrent Processes  */
    $pool = Process::pool(function (Pool $pool) {
        $pool->as('one')->path(base_path())->command('php artisan app:user-list');
        $pool->as('two')->path(base_path())->command('php artisan app:user-list id');
        $pool->as('three')->path(base_path())->command('php artisan app:user-list email');
    })->start(function (string $type, string $output, string $key) {
        $this->info($output);
        $this->info($key);
    });
    $results = $pool->wait();
    $this->info($results['one']->output());
    $this->info($results['two']->output());

});
