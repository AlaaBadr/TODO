<?php

namespace App\Http\Controllers;

use App\Http\Requests\NotOwnerRequest;
use App\Invitation;
use Illuminate\Http\Request;
use App\User;
use App\Task;
use App\FollowContainer;
use App\Events\Task_Followed;

class FollowController extends ApiController
{
    public function follow(Request $request, NotOwnerRequest $not)
    {
    	$user = Auth\LoginController::currentUser();

    	$task = Task::find($request->only('taskId'));

        $invitation = Invitation::whereRaw("`receiverId` = \"$user->id\" and `taskId` = \"$task->id\"")->get();

    	if($task->private == 1 && $invitation == null)
        {
            return response()->json(["You're forbidden to follow this task"] ,403);
        }

        $task->followers()->attach($user);

        $fol = new FollowContainer;
        $fol->senderId = $user->username;
        $fol->receiverId = Auth\LoginController::searchId($task->user_id)->username;
        $fol->taskId = $task->name;

        event(new Task_Followed($fol));

        return response()->json(["Task followed successfully!"] ,200);

    }

    public function unfollow(Request $request, NotOwnerRequest $not)
    {
    	$user = Auth\LoginController::currentUser();

    	$task = Task::find($request->only('taskId'));
    	
    	if(in_array($task->toArray(), $user->followedTasks()->get()->toArray()))
    	{
    		$task->followers()->detach($user);
    		return response()->json(["Task unfollowed!"] ,200);
    	}

    	return response()->json(["You're not a follower"] ,403);
    }

    public function followedTasks(Request $request)
    {
    	$user = Auth\LoginController::currentUser($request);

    	return $user->followedTasks()->get();
    }
}
