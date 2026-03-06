<?php

namespace App\Http\Controllers;

use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Models\Project;
use Illuminate\Http\JsonResponse;

class ProjectController extends Controller
{
    public function index(): JsonResponse
    {
        $projects = Project::withCount(['tasks' => fn ($q) => $q->whereNull('deleted_at')])
            ->when(request('search'), fn ($q, $search) => $q->where('name', 'ilike', "%{$search}%"))
            ->when(request('status'), fn ($q, $status) => $q->where('status', $status))
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($project) => array_merge($project->toArray(), ['task_count' => $project->tasks_count]));

        return response()->json(['data' => $projects, 'message' => 'OK'], 200);
    }

    public function store(StoreProjectRequest $request): JsonResponse
    {
        $project = Project::create([
            'created_by'  => auth()->id(),
            'name'        => $request->name,
            'description' => $request->description,
            'status'      => $request->status,
        ]);

        return response()->json(['data' => $project, 'message' => 'Project berhasil dibuat.'], 201);
    }

    public function show(Project $project): JsonResponse
    {
        $project->load(['tasks' => fn ($q) => $q->whereNull('deleted_at')->with('category')]);

        return response()->json(['data' => $project, 'message' => 'OK'], 200);
    }

    public function update(UpdateProjectRequest $request, Project $project): JsonResponse
    {
        $project->update([
            'name'        => $request->name,
            'description' => $request->description,
            'status'      => $request->status,
        ]);

        return response()->json(['data' => $project, 'message' => 'Project berhasil diperbarui.'], 200);
    }
}
