<?php

namespace App\Listeners;

use App\Events\Test_Invitation;
use App\Notifications\InvitationMail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Http\Controllers\Auth\LoginController;

class Test_InvitationListener
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
     * @param  Test_Invitation  $event
     * @return void
     */
    public function handle(Test_Invitation $event)
    {
        $user = LoginController::searchUsername($event->i->receiverId);
        $invitation = $event->i;

        $user->notify(new InvitationMail($invitation));

        return response()->json(['Email sent successfully!'], 200);
    }
}
