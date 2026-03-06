<?php

namespace Tests\Feature\Feature;

use App\Models\Category;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_returns_correct_structure(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->getJson('/api/dashboard')
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'total_active_projects',
                    'total_incomplete_tasks',
                    'upcoming_tasks',
                ],
            ]);
    }

    public function test_dashboard_counts_active_projects_only(): void
    {
        $user = User::factory()->create();
        Project::factory()->count(2)->create(['created_by' => $user->id, 'status' => 'active']);
        Project::factory()->create(['created_by' => $user->id, 'status' => 'archived']);

        $response = $this->actingAs($user)
            ->getJson('/api/dashboard')
            ->assertStatus(200);

        $this->assertEquals(2, $response->json('data.total_active_projects'));
    }

    public function test_dashboard_upcoming_tasks_excludes_done(): void
    {
        $user     = User::factory()->create();
        $project  = Project::factory()->create(['created_by' => $user->id]);
        $doneCat  = Category::factory()->create(['name' => 'Done']);
        $todoCat  = Category::factory()->create(['name' => 'Todo']);

        // Task Done — tidak boleh muncul di upcoming
        Task::factory()->create([
            'project_id'  => $project->id,
            'category_id' => $doneCat->id,
            'created_by'  => $user->id,
            'due_date'    => now()->addDays(2)->format('Y-m-d'),
        ]);

        // Task Todo — harus muncul
        Task::factory()->create([
            'project_id'  => $project->id,
            'category_id' => $todoCat->id,
            'created_by'  => $user->id,
            'due_date'    => now()->addDays(2)->format('Y-m-d'),
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/dashboard')
            ->assertStatus(200);

        $upcomingTasks = $response->json('data.upcoming_tasks');
        $this->assertCount(1, $upcomingTasks);
        $this->assertEquals($todoCat->id, $upcomingTasks[0]['category_id']);
    }
}
