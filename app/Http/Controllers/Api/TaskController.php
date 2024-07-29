<?php

namespace App\Http\Controllers\Api;

use App\Actions\Tasks\CreateTask;
use App\Actions\Tasks\UpdateTask;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;
use Knuckles\Scribe\Attributes\BodyParam;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;
use Knuckles\Scribe\Attributes\UrlParam;

#[Group('Tasks', 'APIs for tasks')]
class TaskController extends Controller
{

    #[QueryParam('category_id', 'int', 'The id of category.', example: 1)]
    #[ResponseFromApiResource(TaskResource::class, Task::class, 200, collection: true)]
    public function index(): AnonymousResourceCollection|JsonResponse
    {
        if (Gate::denies('viewAny', Task::class)) {
            return response()->json([]);
        }

        $tasks = Task::query()
            ->where('user_id', auth()->id())
            ->when(request('category_id'), fn($query) => $query->where('category_id', request('category_id')))
            ->get();

        return TaskResource::collection($tasks);
    }

    #[BodyParam('category_id', 'int', 'The id of category.', example: 1)]
    #[BodyParam('name', 'string', 'The name of task.', example: 'Top')]
    #[BodyParam('description', 'string', 'Description of task.', required: false, example: 'Description ...')]
    #[BodyParam('finished_at', 'string', 'Task completion date.', example: '2024-08-25 11:14:39')]
    #[ResponseFromApiResource(TaskResource::class, Task::class, 201)]
    public function store(Request $request): TaskResource|JsonResponse
    {
        if (Gate::denies('create', Task::class)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $task = app(CreateTask::class)->handle(auth()->user(), $request->input());

        return TaskResource::make($task);
    }

    #[UrlParam('id', 'int', 'The id of task.', example: 1)]
    #[ResponseFromApiResource(TaskResource::class, Task::class, 200)]
    #[Response(content: ['message' => 'Forbidden'], status: 403, description: 'Forbidden')]
    #[Response(content: ['message' => 'Task not found'], status: 404, description: 'task not found')]
    public function show(Task $task): TaskResource|JsonResponse
    {
        if (Gate::denies('view', [Task::class, $task])) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return TaskResource::make($task);
    }


    #[BodyParam('category_id', 'int', 'The id of category.', example: 1)]
    #[BodyParam('name', 'string', 'The name of task.', example: 'Top')]
    #[BodyParam('description', 'string', 'Description of task.', required: false, example: 'Description ...')]
    #[BodyParam('finished_at', 'string', 'Task completion date.', example: '2024-08-25 11:14:39')]
    #[ResponseFromApiResource(TaskResource::class, Task::class, 201)]
    #[Response(content: ['message' => 'Forbidden'], status: 403, description: 'Forbidden')]
    #[Response(content: ['message' => 'Task not found'], status: 404, description: 'task not found')]
    public function update(Request $request, Task $task): TaskResource|JsonResponse
    {
        if (Gate::denies('update', [Task::class, $task])) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $task = app(UpdateTask::class)->handle($task, $request->all());

        return TaskResource::make($task);
    }

    #[UrlParam('id', 'int', 'The id of task.', example: 1)]
    #[Response(content: ['success' => true], description: 'success')]
    #[Response(content: ['message' => 'Forbidden'], status: 403, description: 'Forbidden')]
    #[Response(content: ['message' => 'Task not found'], status: 404, description: 'Task not found')]
    public function destroy(Task $task): JsonResponse
    {
        if (Gate::denies('delete', [Task::class, $task])) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return response()->json([
            'success' => $task->delete(),
        ]);
    }
}
