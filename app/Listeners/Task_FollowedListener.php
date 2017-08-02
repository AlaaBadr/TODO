<?php

namespace App\Listeners;

use App\Events\Task_Followed;
use App\Notifications\TaskFollowedMail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Http\Controllers\Auth\LoginController;

class Task_FollowedListener
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
     * @param  Task_Followed  $event
     * @return void
     */
    public function handle(Task_Followed $event)
    {
        $user = LoginController::searchUsername($event->f->receiverId);
        $follow = $event->f;

        $user->notify(new TaskFollowedMail($follow));

        return response()->json(['Email sent successfully!'], 200);
    }
}
