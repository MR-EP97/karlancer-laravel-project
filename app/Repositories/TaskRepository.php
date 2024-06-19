<?php

namespace App\Repositories;

use App\Interfaces\TaskRepositoryInterface;
use App\Models\Task;

class TaskRepository implements TaskRepositoryInterface
{

    public function all(): \Illuminate\Database\Eloquent\Collection
    {
        return Task::all();
    }

    public function create(array $data): Task
    {
        return Task::create($data);
    }

    public function update(array $data, $id): Task
    {
        $task = Task::findOrFail($id);
        $task->update($data);
        $task->save();
        return $task;
    }

    public function delete($id): void
    {
        $task = Task::findOrFail($id);
        $task->delete();
    }

    public function find($id): Task
    {
        return Task::findOrFail($id);
    }
}
