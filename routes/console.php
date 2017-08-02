<?php

use App\Task;
use Illuminate\Foundation\Inspiring;

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
})->describe('Display an inspiring quote');

Artisan::command('deadline_warning', function(){
    $tasks = Task::whereRaw("((  Timestamp(NOW()) - Timestamp(`created_at`)    ) >= ((  Timestamp(`deadline`) - Timestamp(`created_at`)    ) * 0.8)) and `warned` = 0")->get();

    foreach ($tasks as $task)
    {
        $task->warned = 1;
        $task->save();

        event(new \App\Events\Deadline_Warning($task));
    }
})->describe('Notifies user if 80% of deadline passed!');
