<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTaskRequest;
use App\Http\Requests\TaskOwnerRequest;
use App\Task;
use Illuminate\Http\Request;
use App\Transformers\TaskTransformer;

class TaskController extends ApiController
{
    protected $taskTransformer;

    public function __construct()
    {
        $this->taskTransformer = new TaskTransformer();
    }

    public function allTasks()
    {
        return Task::all();
    }

    public function getTask($name)
    {
    	$task = Task::where('name', $name)->first();
    	if(! $task)
        {
            return $this->respondNotFound('Task does not exist');
        }
        return $this->respond(['data' => $this->taskTransformer->transform($task)]);
    }

    public function allPublicTasks()
    {
    	$tasks = Task::public()->get();
        return $this->respond(['data' => $this->taskTransformer->transformCollection($tasks->all())]);
    }

    public function allIncompleteTasks()
    {
    	$tasks = Task::incomplete()->get();
        return $this->respond(['data' => $this->taskTransformer->transformCollection($tasks->all())]);
    }

    public function destroy(Task $task, TaskOwnerRequest $request)
    {
    	$task->delete();

        return $this->respondSuccess(["Task deleted!"]);
    }

    public function store(CreateTaskRequest $request)
    {
    	$task = new Task;

    	$task->user_id = auth()->id();
    	$task->name = request('name');
    	$task->description = request('description');
    	$task->deadline = \Carbon\Carbon::createFromFormat('d/m/Y', request('deadline'));

    	$task->save();

    	return $this->respondCreated("Task is successfully created!");
    }

    public function markPrivate(Task $task)
    {
        $task->private = 1;
        $task->save();

        return $this->respondSuccess("Task is now private!");
    }

    public function markCompleted(Task $task)
    {
        $task->completed = 1;
        $task->save();

        return $this->respondSuccess("Task is completed!");
    }

    public function myTasks()
    {
    	$user = Auth\LoginController::currentUser();

    	$mine = $user->tasks()->get()->toArray();
    	$followed = $user->followedTasks()->get()->toArray();

    	$mytasks = array_merge($mine, $followed);
        return $this->respond(['data' => $this->taskTransformer->transformCollection($mytasks)]);
    }

    public function toggleStatus(Task $task)
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

        return $this->respondSuccess("Toggled!");
    }

    public function attach(Request $request)
    {
        $file = $request->file('file');
        $ext = $file->extension();
        return $file->storeAs('files/'.Task::find($request->get('taskId'))->id, "file.{$ext}");
    }
}
