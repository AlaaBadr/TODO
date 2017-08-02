<?php

namespace App\Listeners;

use App\Http\Controllers\Auth\LoginController;
use App\Events\Deadline_Warning;
use App\Notifications\DeadlineWarningMail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class Deadline_WarningListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Deadline_Warning  $event
     * @return void
     */
    public function handle(Deadline_Warning $event)
    {
        $user = LoginController::searchId($event->task->user_id);
        $task = $event->task;

        $user->notify(new DeadlineWarningMail($task));

        return response()->json(['Email sent successfully!'], 200);
    }
}
