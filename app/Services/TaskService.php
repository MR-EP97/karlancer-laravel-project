<?php

namespace App\Services;

use App\Interfaces\TaskRepositoryInterface;

class TaskService
{
    public function __construct(
        protected TaskRepositoryInterface $taskRepository
    )
    {
    }

    public function create(array $data)
    {
        return $this->taskRepository->create($data);
    }

    public function update(array $data, $id)
    {
        return $this->taskRepository->update($data, $id);
    }

    public function delete($id)
    {
        return $this->taskRepository->delete($id);
    }

    public function all()
    {
        return $this->taskRepository->all();
    }

    public function find($id)
    {
        return $this->taskRepository->find($id);
    }
}
