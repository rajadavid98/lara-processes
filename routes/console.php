<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Process;
use Illuminate\Process\Pool;
use function Laravel\Prompts\text;
use function Laravel\Prompts\password;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;

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

Artisan::command('test-prompts', function () {
    //1
//    $name = text('What is your name?');
//    $this->info("Welcome $name!");

    //2
//    $name = text(
//        label: 'What is your name?',
//        placeholder: 'E.g. Taylor Otwell',
//        default: 'Raja',
//        hint: 'This will be displayed on your profile.'
//    );
//    $this->info("Your profile name is $name!");


    //3.Required Values
//    $name = text(
//        label: 'What is your name?',
//        required: true
//    );
//    $this->info("Welcome $name!");

//    $name = text(
//        label: 'What is your name?',
//        required: 'Your name is required.'
//    );
//    $this->info("Welcome $name!");

    //4. Additional Validation
//    $name = text(
//        label: 'What is your name?',
//        validate: fn (string $value) => match (true) {
//            strlen($value) < 3 => 'The name must be at least 3 characters.',
//            strlen($value) > 255 => 'The name must not exceed 255 characters.',
//            default => null
//        }
//    );
//    $this->info("Welcome $name!");

    //5. Password
//    $password = password('What is your password?');
//    $this->info("your password is - $password");

    //6. confirm
//    $confirmed = confirm(
//        label: 'Do you accept the terms?',
//        default: false,
//        yes: 'I accept',
//        no: 'I decline',
//        hint: 'The terms must be accepted to continue.'
//    );
//    $this->info($confirmed);

    //7. select
//    $role = select(
//        'What role should the user have?',
//        ['Member', 'Contributor', 'Owner'],
//    );
//    $this->info($role);

    $role = select(
        label: 'Which category would you like to assign?',
        options: collect([
            ['Raja', 1],['Raja', 2],['Raja', 3],['Raja', 4],['Raja', 5],['Raja', 6],['Raja', 7],['Raja', 8],
        ]),
        scroll: 10
    );
    $this->info($role);



});
