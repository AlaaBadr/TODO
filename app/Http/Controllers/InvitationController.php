<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Invitation;
use App\Events\Test_Invitation;


class InvitationController extends Controller
{

    public function invite(Request $request)
    {
    	$user = Auth\LoginController::currentUser();

    	$inv = new \App\Invitation();
        $inv->senderId = $user->username;
        $inv->receiverId = $request->get('receiverId');
        $inv->taskId = $request->get('taskId');

        $task = TaskController::getTask($inv->taskId);
        if($user->id != $task->user_id || $task->private == 0)
        {
            return response()->json(['You can\'t invite anyone on this task!'], 403);
        }

        $inv->save();

    	event(new Test_Invitation($inv));

    	return response()->json(['Invitation sent successfully!'], 200);
    }

    public function acceptInvitation(Invitation $inv)
    {
    	if($inv->acceptance == null)
    	{
    		$user = Auth\LoginController::searchUsername($inv->receiverId);
        	$task = TaskController::getTask($inv->taskId);
        
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
