<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskStoreFormRequest;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\TaskService;
use Illuminate\Support\Js;

class TaskController extends Controller
{
    use ApiResponseTrait;

    public function __construct(protected TaskService $taskService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {

        return $this->success('Success',
            $this->taskService->all()->paginate(10), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskStoreFormRequest $request): JsonResponse
    {
        $task = $this->taskService->create($request->only('title', 'description', 'time', 'status'));
        return $this->success('Create Task Successfully', $task->id);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $task)
    {

    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        //
    }
}
