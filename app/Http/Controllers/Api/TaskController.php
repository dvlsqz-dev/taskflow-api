<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse; 
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TaskController extends Controller
{

    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Project $project)
    {
       $tasks = Task::ofProject($project->id)
        ->when($request->query('status'), fn($q, $status) => $q->ofStatus($status))
        ->when($request->query('search'), fn($q, $search) => $q->where('title', 'like', "%{$search}%"))
        ->paginate($request->query('per_page', 10));

    return ApiResponse::success(
        TaskResource::collection($tasks),
        'Tasks retrieved successfully'
    );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request, Project $project)
    {
        $task = Task::create([
            'title'       => $request->title,
            'description' => $request->description,
            'status'      => $request->status ?? 'todo',
            'date_limit'  => $request->date_limit,
            'project_id'  => $project->id,
            'user_id'     => $request->user()->id,
        ]);

        return ApiResponse::success(new TaskResource($task), 'Task created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project, Task $task)
    {
        $this->authorize('view', $task);

        return ApiResponse::success(
            new TaskResource($task),
            'Task retrieved successfully'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Project $project, Task $task)
    {
        $this->authorize('update', $task);

        $task->update($request->validated());

        return ApiResponse::success(
            new TaskResource($task),
            'Task updated successfully'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project, Task $task)
    {
        $this->authorize('delete', $task);

        $task->delete();

        return ApiResponse::success(null, 'Task deleted successfully');
    }
}
