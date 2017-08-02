<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('invites.{username}', function ($user, $username) {
    return $user->username === $username;
});

Broadcast::channel('follows.{username}', function ($user, $username) {
    return $user->username === $username;
});

Broadcast::channel('deadline.{username}',function ($user, $username) {
    return $user->username === $username;
});