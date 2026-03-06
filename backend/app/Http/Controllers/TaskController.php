<?php

namespace App\Http\Controllers;

use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskCategoryRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    public function index(): JsonResponse
    {
        $tasks = Task::with(['project:id,name', 'category:id,name'])
            ->whereNull('deleted_at')
            ->when(request('search'), fn ($q, $search) => $q->where('title', 'ilike', "%{$search}%"))
            ->when(request('category_id'), fn ($q, $categoryId) => $q->where('category_id', $categoryId))
            ->when(request('project_id'), fn ($q, $projectId) => $q->where('project_id', $projectId))
            ->orderBy('due_date', 'asc')
            ->get();

        return response()->json(['data' => $tasks, 'message' => 'OK'], 200);
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = Task::create([
            'project_id'  => $request->project_id,
            'category_id' => $request->category_id,
            'created_by'  => auth()->id(),
            'title'       => $request->title,
            'description' => $request->description,
            'due_date'    => $request->due_date,
        ]);

        $task->load(['project:id,name', 'category:id,name']);

        return response()->json(['data' => $task, 'message' => 'Task berhasil dibuat.'], 201);
    }

    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        $task->update([
            'project_id'  => $request->project_id,
            'category_id' => $request->category_id,
            'title'       => $request->title,
            'description' => $request->description,
            'due_date'    => $request->due_date,
        ]);

        $task->load(['project:id,name', 'category:id,name']);

        return response()->json(['data' => $task, 'message' => 'Task berhasil diperbarui.'], 200);
    }

    public function updateCategory(UpdateTaskCategoryRequest $request, Task $task): JsonResponse
    {
        $task->update(['category_id' => $request->category_id]);
        $task->load('category');

        return response()->json(['data' => $task, 'message' => 'Kategori task berhasil diperbarui.'], 200);
    }

    public function destroy(Task $task): JsonResponse
    {
        $task->update([
            'deleted_at' => now(),
            'deleted_by' => auth()->id(),
        ]);

        return response()->json(['message' => 'Task berhasil dihapus.'], 200);
    }
}
