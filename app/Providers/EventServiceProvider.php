<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\Test_Invitation' => [
            'App\Listeners\Test_InvitationListener',
        ],
        'App\Events\Task_Followed' => [
            'App\Listeners\Task_FollowedListener',
        ],
        'App\Events\Deadline_Warning' => [
            'App\Listeners\Deadline_WarningListener',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
