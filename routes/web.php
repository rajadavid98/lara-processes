<?php

use Illuminate\Process\Pipe;
use Illuminate\Support\Facades\Process;
use Illuminate\Process\Pool;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {

    /**   1. Proper command output   */
    return Process::run('ls -al')->output();
    /**   sudo snap install http - for enable http   */
    /**   run in terminal for clear formatted output - http get http://127.0.0.1:8000/   */


    /**   2. Invalid command output   */
    return Process::run('abcd efgh')->output();

    /**   3. Check exit code   */
    return Process::run('abcd efgh')->exitCode();

    /**   4. Check error output message   */
    return Process::run('abcd efgh')->errorOutput();


    /**   5. Check command processed or not   */
    if (Process::run('ls')->successful()) {
        return 'Done!';
    } else {
        return 'failed';
    }


    /**   6. Throwing Exceptions   */
    return Process::run('ls -lasafhsakjhf')->throw();


    /**   7. Set Working Directory Path   */
    $process1 = Process::run('php artisan app:user-list');
    $process2 = Process::path(base_path())->run('php artisan app:user-list');
    info($process1->output());
    info($process2->output());


    /**   8. Set Working Directory Path from env   */
    $process = Process::forever()->env(['IMPORT_PATH'])->run('php artisan app:user-list');
    info($process->output());


    /**   9. quietly   */
    $process = Process::quietly()->run('ls');
//    info($process->output());
    info($process->exitCode());

    /**   10. Pipelines   */
    $process = Process::pipe(function (Pipe $pipe) {
        $pipe->command('ls');
        $pipe->command('php -v');
        $pipe->command('cat example.txt');
        $pipe->command('php -v');
    });
    info($process->output());


    return view('welcome');
});
