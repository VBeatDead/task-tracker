<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $UPCOMING_DAYS_THRESHOLD = 7;  // PRD 4.6
        $UPCOMING_TASKS_LIMIT = 10;    // PRD 4.6

        $totalActiveProjects = Project::where('status', 'active')->count();

        $totalIncompleteTasks = Task::whereNull('deleted_at')
            ->whereHas('category', fn ($q) => $q->where('name', '!=', 'Done'))
            ->count();

        $upcomingTasks = Task::with(['project:id,name', 'category:id,name'])
            ->whereNull('deleted_at')
            ->whereHas('category', fn ($q) => $q->where('name', '!=', 'Done'))
            ->where('due_date', '<=', now()->addDays($UPCOMING_DAYS_THRESHOLD)->toDateString())
            ->orderBy('due_date', 'asc')
            ->limit($UPCOMING_TASKS_LIMIT)
            ->get();

        return response()->json([
            'data' => [
                'total_active_projects'  => $totalActiveProjects,
                'total_incomplete_tasks' => $totalIncompleteTasks,
                'upcoming_tasks'         => $upcomingTasks,
            ],
            'message' => 'OK',
        ], 200);
    }
}
