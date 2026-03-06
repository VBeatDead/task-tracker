<?php

namespace Tests\Feature\Feature;

use App\Models\Category;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Project $project;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user     = User::factory()->create();
        $this->project  = Project::factory()->create(['created_by' => $this->user->id]);
        $this->category = Category::factory()->create(['name' => 'Todo']);
    }

    public function test_can_list_tasks(): void
    {
        Task::factory()->count(3)->create([
            'project_id'  => $this->project->id,
            'category_id' => $this->category->id,
            'created_by'  => $this->user->id,
        ]);

        $this->actingAs($this->user)
            ->getJson('/api/tasks')
            ->assertStatus(200)
            ->assertJsonStructure(['data', 'message']);
    }

    public function test_soft_deleted_task_not_in_list(): void
    {
        Task::factory()->create([
            'project_id'  => $this->project->id,
            'category_id' => $this->category->id,
            'created_by'  => $this->user->id,
            'deleted_at'  => now(),
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/tasks')
            ->assertStatus(200);

        $this->assertCount(0, $response->json('data'));
    }

    public function test_can_create_task_with_future_due_date(): void
    {
        $this->actingAs($this->user)
            ->postJson('/api/tasks', [
                'title'       => 'Task Baru',
                'description' => 'Deskripsi task baru.',
                'due_date'    => now()->addDays(3)->format('Y-m-d'),
                'category_id' => $this->category->id,
                'project_id'  => $this->project->id,
            ])
            ->assertStatus(201)
            ->assertJsonPath('data.title', 'Task Baru');
    }

    public function test_cannot_create_task_with_past_due_date(): void
    {
        $this->actingAs($this->user)
            ->postJson('/api/tasks', [
                'title'       => 'Task Lampau',
                'description' => 'Deskripsi.',
                'due_date'    => now()->subDay()->format('Y-m-d'),
                'category_id' => $this->category->id,
                'project_id'  => $this->project->id,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['due_date']);
    }

    public function test_soft_delete_sets_deleted_at_and_deleted_by(): void
    {
        $task = Task::factory()->create([
            'project_id'  => $this->project->id,
            'category_id' => $this->category->id,
            'created_by'  => $this->user->id,
        ]);

        $this->actingAs($this->user)
            ->deleteJson("/api/tasks/{$task->id}")
            ->assertStatus(200)
            ->assertJson(['message' => 'Task berhasil dihapus.']);

        $this->assertNotNull($task->fresh()->deleted_at);
        $this->assertEquals($this->user->id, $task->fresh()->deleted_by);
    }

    public function test_can_update_task_category(): void
    {
        $task        = Task::factory()->create([
            'project_id'  => $this->project->id,
            'category_id' => $this->category->id,
            'created_by'  => $this->user->id,
        ]);
        $newCategory = Category::factory()->create(['name' => 'Done']);

        $this->actingAs($this->user)
            ->patchJson("/api/tasks/{$task->id}/category", ['category_id' => $newCategory->id])
            ->assertStatus(200)
            ->assertJsonPath('data.category_id', $newCategory->id);
    }

    public function test_can_edit_task_with_past_due_date(): void
    {
        $task = Task::factory()->create([
            'project_id'  => $this->project->id,
            'category_id' => $this->category->id,
            'created_by'  => $this->user->id,
            'due_date'    => now()->subDays(3)->format('Y-m-d'),
        ]);

        $this->actingAs($this->user)
            ->putJson("/api/tasks/{$task->id}", [
                'title'       => 'Updated Title',
                'description' => 'Updated desc.',
                'due_date'    => now()->subDays(3)->format('Y-m-d'),
                'category_id' => $this->category->id,
                'project_id'  => $this->project->id,
            ])
            ->assertStatus(200);
    }

    public function test_can_update_task_title_and_description(): void
    {
        $task = Task::factory()->create([
            'project_id'  => $this->project->id,
            'category_id' => $this->category->id,
            'created_by'  => $this->user->id,
        ]);

        $this->actingAs($this->user)
            ->putJson("/api/tasks/{$task->id}", [
                'title'       => 'Judul Baru',
                'description' => 'Deskripsi baru.',
                'due_date'    => now()->addDays(5)->format('Y-m-d'),
                'category_id' => $this->category->id,
                'project_id'  => $this->project->id,
            ])
            ->assertStatus(200)
            ->assertJsonPath('data.title', 'Judul Baru');
    }

    public function test_cannot_update_task_without_required_fields(): void
    {
        $task = Task::factory()->create([
            'project_id'  => $this->project->id,
            'category_id' => $this->category->id,
            'created_by'  => $this->user->id,
        ]);

        $this->actingAs($this->user)
            ->putJson("/api/tasks/{$task->id}", [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'description', 'due_date', 'category_id', 'project_id']);
    }

    public function test_can_filter_tasks_by_category(): void
    {
        $otherCategory = Category::factory()->create(['name' => 'Done']);

        Task::factory()->create([
            'project_id'  => $this->project->id,
            'category_id' => $this->category->id,
            'created_by'  => $this->user->id,
        ]);
        Task::factory()->create([
            'project_id'  => $this->project->id,
            'category_id' => $otherCategory->id,
            'created_by'  => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/tasks?category_id={$this->category->id}")
            ->assertStatus(200);

        foreach ($response->json('data') as $task) {
            $this->assertEquals($this->category->id, $task['category_id']);
        }
    }

    public function test_can_filter_tasks_by_project(): void
    {
        $otherProject = Project::factory()->create(['created_by' => $this->user->id]);

        Task::factory()->create([
            'project_id'  => $this->project->id,
            'category_id' => $this->category->id,
            'created_by'  => $this->user->id,
        ]);
        Task::factory()->create([
            'project_id'  => $otherProject->id,
            'category_id' => $this->category->id,
            'created_by'  => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/tasks?project_id={$this->project->id}")
            ->assertStatus(200);

        foreach ($response->json('data') as $task) {
            $this->assertEquals($this->project->id, $task['project_id']);
        }
    }

    public function test_can_search_tasks_by_title(): void
    {
        Task::factory()->create([
            'project_id'  => $this->project->id,
            'category_id' => $this->category->id,
            'created_by'  => $this->user->id,
            'title'       => 'Implementasi Login',
        ]);
        Task::factory()->create([
            'project_id'  => $this->project->id,
            'category_id' => $this->category->id,
            'created_by'  => $this->user->id,
            'title'       => 'Setup Database',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/tasks?search=Login')
            ->assertStatus(200);

        $titles = array_column($response->json('data'), 'title');
        $this->assertContains('Implementasi Login', $titles);
        $this->assertNotContains('Setup Database', $titles);
    }
}
