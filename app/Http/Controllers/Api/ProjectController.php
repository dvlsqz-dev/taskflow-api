<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Barryvdh\DomPDF\Facade\Pdf;

class ProjectController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $projects = Project::where('user_id', $request->user()->id)
            ->paginate(10);

        return ApiResponse::success(
            ProjectResource::collection($projects),
            'Projects retrieved successfully'
        );
    }

    public function store(StoreProjectRequest $request)
    {
        $project = Project::create([
            'name'        => $request->name,
            'description' => $request->description,
            'user_id'     => $request->user()->id,
        ]);

        return ApiResponse::success(
            new ProjectResource($project),
            'Project created successfully',
            201
        );
    }

    public function show(Project $project)
    {
        $this->authorize('view', $project);

        return ApiResponse::success(
            new ProjectResource($project),
            'Project retrieved successfully'
        );
    }

    public function update(UpdateProjectRequest $request, Project $project)
    {
        $this->authorize('update', $project);

        $project->update($request->validated());

        return ApiResponse::success(
            new ProjectResource($project),
            'Project updated successfully'
        );
    }

    public function destroy(Request $request, Project $project)
    {
        $this->authorize('delete', $project);

        $project->delete();

        return ApiResponse::success(null, 'Project deleted successfully');
    }

    public function exportReport(Project $project)
    {
        $this->authorize('view', $project);

        $project->load('tasks'); // carga las tareas relacionadas

        $pdf = Pdf::loadView('reports.project', [
            'project' => $project,
            'tasks' => $project->tasks,
        ]);

        return $pdf->download("proyecto-{$project->id}.pdf");
    }
}