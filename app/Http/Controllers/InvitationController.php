<?php

namespace App\Http\Controllers;

use App\Http\Requests\NotOwnerRequest;
use Illuminate\Http\Request;
use App\Invitation;
use App\Events\Test_Invitation;
use App\Task;


class InvitationController extends ApiController
{

    public function invite(Request $request, NotOwnerRequest $not)
    {
    	$user = Auth\LoginController::currentUser();

    	$inv = new \App\Invitation();
        $inv->senderId = $user->id;
        $inv->receiverId = $request->get('receiverId');
        $inv->taskId = $request->get('taskId');

        $task = Task::find($inv->taskId);

        if($task->private == 0)
        {
            return response()->json(['You can\'t invite anyone on a public task!'], 403);
        }

        $inv->save();

    	event(new Test_Invitation($inv));

    	return response()->json(['Invitation sent successfully!'], 200);
    }

    public function acceptInvitation(Invitation $inv)
    {
    	if($inv->acceptance == null)
    	{
    		$user = Auth\LoginController::searchId($inv->receiverId);
        	$task = Task::find($inv->taskId);
        
        	$task->followers()->attach($user);

        	return response()->json(['Task followed successfully!'], 200);
    	}
    	elseif($inv->acceptance == 0 || $inv->acceptance == 1)
    	{
    		return response()->json(['Invitation is not found!'], 404);
    	}
    }

    public function rejectInvitation(Invitation $inv)
    {
    	if($inv->acceptance == null)
    	{
    		return response()->json(['Task rejected!'], 200);
    	}
    	else
    	{
    		return response()->json(['Invitation is not found!'], 404);
    	}
    }

    public function getInvitation(Invitation $inv)
    {
    	return $inv;
    }
}
