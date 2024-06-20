<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskFormRequest;
use App\Http\Requests\UpdateTaskFormRequest;
use App\Http\Resources\TaskResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\TaskService;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as HttpResponse;


class TaskController extends Controller
{
    use ApiResponseTrait;


    public function __construct(protected TaskService $taskService)
    {
    }

    /**
     * Display a listing of the task.
     */

    /**
     * @OA\Get(
     *     path="/api/tasks",
     *     summary="Get list of tasks",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="A list with tasks"
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        return $this->success('Success',
            array(TaskResource::collection($request->user()->tasks)));
    }

    /**
     * Store a newly created task in storage.
     */

    /**
     * @OA\Post(
     *     path="/api/tasks",
     *     summary="Create a new post",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title","status",},
     *             @OA\Property(property="title", type="string", maxLength=50 ,example="Task Title"),
     *             @OA\Property(property="description", type="string", minLength=10 ,example="Task description lorem ipson heri qes likj"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Task created successfully"
     *     )
     * )
     */
    public function store(StoreTaskFormRequest $request): JsonResponse
    {
        $task = $this->taskService->create(array_merge($request->safe()->only('title', 'description'),
            ['user_id' => $request->user()->id, 'status' => 'pending']));
        return $this->success('Create Task Successfully', array(TaskResource::make($task)));
    }

    /**
     * Display the specified task.
     */


    /**
     * @OA\Get(
     *     path="/api/tasks/{id}",
     *     summary="Get a task by id",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the task",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A single task"
     *     )
     * )
     */
    public function show($id, Request $request): JsonResponse
    {
        return $request->user()->tasks->find($id) ?
            $this->success('Success', array(TaskResource::make($request->user()->tasks->find($id)))) :
            $this->error('Not access', [], HttpResponse::HTTP_FORBIDDEN);
    }



    public function update(UpdateTaskFormRequest $request, Task $task): JsonResponse
    {
        if ($id = $request->user()->tasks()->findOrFail($task->id)->id) {
            $this->taskService->update($request->safe()->only('title', 'description', 'status'), $id);
            return $this->success('Update Task Successfully', array(TaskResource::make($task)));
        }

        return $this->error('Not access', [], HttpResponse::HTTP_FORBIDDEN);
    }

    /**
     * Remove the Task
     */

    /**
     * @OA\Delete(
     *     path="/api/tasks/{id}",
     *     summary="Delete a task",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the task",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Task deleted successfully"
     *     )
     * )
     */
    public function destroy(Task $task): JsonResponse
    {
        $this->taskService->delete($task->id);
        return $this->success('Deleted Task Successfully', [], HttpResponse::HTTP_NO_CONTENT);
    }
}
