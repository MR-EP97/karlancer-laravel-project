<?php

namespace App\Repositories;

use App\Interfaces\TaskRepositoryInterface;
use App\Models\Task;

class TaskRepository implements TaskRepositoryInterface
{

    public function all(): Task
    {
        return Task::all();
    }

    public function create(array $data): Task
    {
        return Task::create($data);
    }

    public function update(array $data, $id): Task
    {
        $user = Task::findOrFail($id);
        $user->update($data);
        return $user;
    }

    public function delete($id): void
    {
        $user = Task::findOrFail($id);
        $user->delete();
    }

    public function find($id): Task
    {
        return Task::findOrFail($id);
    }
}
