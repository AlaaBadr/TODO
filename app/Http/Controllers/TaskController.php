<?php

namespace App\Http\Controllers;

use App\Task;
use App\User;
use App\Invitation;
use App\FollowContainer;
use App\Events\Test_Invitation;
use App\Events\Task_Followed;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    //
    public function allTasks()
    {
        return Task::all();
    }

    public function searchTask(Task $task)
    {
        return $task;
    }

    public static function getTask($name)
    {
    	return Task::where('name', $name)->first();
    }

    public function allPublicTasks()
    {
    	return Task::public()->get();
    }

    public function allIncompleteTasks()
    {
    	return Task::incomplete()->get();
    }

    public function destroy($name)
    {
        $user = Auth\LoginController::currentUser();

    	$task = TaskController::getTask($name);

    	if($user->id != $task->user_id)
        {
            return response()->json(["Task is not yours to delete!"] ,403);
        }

    	$task->delete();

        return response()->json(["Task deleted!"] ,200);
    }

    public function store()
    {
    	$this->validate(request(), [
            'name' => 'required',
            'description' => 'required',
            'deadline' => 'required'
        ]);

    	$task = new Task;

    	$task->user_id = auth()->id();
    	$task->name = request('name');
    	$task->description = request('description');
    	$task->deadline = \Carbon\Carbon::createFromFormat('d/m/Y', request('deadline'));

    	$task->save();

    	return response()->json(["Task added successfully!"] ,200);
    }

    public function markPrivate(Request $request)
    {
    	$user = Auth\LoginController::currentUser();

    	$task = TaskController::getTask($request->only('name'));

    	if($user->id == $task->user_id)
    	{
    		$task->private = 1;
    		$task->save();
    		return response()->json(["Task is now private!"] ,200);
    	}

    	return response()->json(["You're not authorized to access this task!"] ,401);
    }

    public function markCompleted(Request $request)
    {
    	$user = Auth\LoginController::currentUser();

    	$task = TaskController::getTask($request->only('name'));

    	if($user->id == $task->user_id)
    	{
    		$task->completed = 1;
    		$task->save();
    		return response()->json(["Task is completed!"] ,200);
    	}

    	return response()->json(["You're not authorized to access this task!"] ,401);
    }

    public function myTasks()
    {
    	$user = Auth\LoginController::currentUser();

    	$mine = $user->tasks()->get()->toArray();
    	$followed = $user->followedTasks()->get()->toArray();

    	return array_merge($mine, $followed);
    }

    public function toggleStatus(Request $request)
    {
    	$user = Auth\LoginController::currentUser();

    	$task = TaskController::getTask($request->only('name'));

    	if($user->id == $task->user_id)
    	{
    		if($task->completed == 0)
    		{
    			$task->completed = 1;
    			$task->save();
    		}
    		else
    		{
    			$task->completed = 0;
    			$task->save();
    		}

    		return response()->json(["Toggled!"] ,200);
    	}

    	return response()->json(["You're not authorized to access this task!"] ,401);
    }

    public function attach(Request $request)
    {
        $file = $request->file('file');
        $ext = $file->extension();
        return $file->storeAs('files/'.TaskController::getTask($request->get('name'))->id, "file.{$ext}");
    }
}
