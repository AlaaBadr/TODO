<?php
/**
 * Created by PhpStorm.
 * User: Alaa
 * Date: 03-Aug-17
 * Time: 10:41 AM
 */

namespace App\Transformers;


class TaskTransformer extends Transformer
{
    public function transform($task)
    {
        return [
            'id' => (integer) $task['id'],
            'owner' => (integer) $task['user_id'],
            'name' => $task['name'],
            'description' => $task['description'],
            'deadline' => $task['deadline'],
            'privacy' => (boolean) $task['private'],
            'completed' => (boolean) $task['completed']
        ];
    }
}