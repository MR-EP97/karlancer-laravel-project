<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskStoreFormRequest;
use App\Http\Requests\UpdateTaskFormRequest;
use App\Http\Resources\TaskResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\TaskService;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

//use Illuminate\Support\Facades\Request;

class TaskController extends Controller
{
    use ApiResponseTrait;


    public function __construct(protected TaskService $taskService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        return $this->success('Success',
            array(TaskResource::collection($request->user()->tasks)));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskStoreFormRequest $request): JsonResponse
    {
        $task = $this->taskService->create(array_merge($request->safe()->only('title', 'description'),
            ['user_id' => $request->user()->id, 'status' => 'pending']));
        return $this->success('Create Task Successfully', array(TaskResource::make($task)));
    }

    /**
     * Display the specified resource.
     */
    public function show($id, Request $request): JsonResponse
    {
        return $request->user()->tasks->find($id) ?
            $this->success('Success', array(TaskResource::make($request->user()->tasks->find($id)))) :
            $this->error('Not access', [], HttpResponse::HTTP_FORBIDDEN);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskFormRequest $request, Task $task): JsonResponse
    {
        if ($id = $request->user()->tasks()->findOrFail($task->id)->id) {
            $this->taskService->update($request->safe()->only('title', 'description', 'status'), $id);
            return $this->success('Update Task Successfully', array(TaskResource::make($task)));
        }

        return $this->error('Not access', [], HttpResponse::HTTP_FORBIDDEN);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task): JsonResponse
    {
        $this->taskService->delete($task->id);
        return $this->success('Deleted Task Successfully');
    }
}
