<?php

namespace Tests\Feature\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_projects(): void
    {
        Project::factory()->count(3)->create(['created_by' => $this->user->id]);

        $this->actingAs($this->user)
            ->getJson('/api/projects')
            ->assertStatus(200)
            ->assertJsonStructure(['data', 'message']);
    }

    public function test_can_create_project(): void
    {
        $this->actingAs($this->user)
            ->postJson('/api/projects', [
                'name'        => 'Project Baru',
                'description' => 'Deskripsi project baru.',
                'status'      => 'active',
            ])
            ->assertStatus(201)
            ->assertJsonPath('data.name', 'Project Baru');
    }

    public function test_cannot_create_project_without_required_fields(): void
    {
        $this->actingAs($this->user)
            ->postJson('/api/projects', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'description', 'status']);
    }

    public function test_can_update_project_status_to_archived(): void
    {
        $project = Project::factory()->create(['created_by' => $this->user->id, 'status' => 'active']);

        $this->actingAs($this->user)
            ->putJson("/api/projects/{$project->id}", [
                'name'        => $project->name,
                'description' => $project->description,
                'status'      => 'archived',
            ])
            ->assertStatus(200)
            ->assertJsonPath('data.status', 'archived');
    }

    public function test_can_show_project_with_tasks(): void
    {
        $project = Project::factory()->create(['created_by' => $this->user->id]);

        $this->actingAs($this->user)
            ->getJson("/api/projects/{$project->id}")
            ->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'name', 'tasks']]);
    }

    public function test_can_search_projects_by_name(): void
    {
        Project::factory()->create(['created_by' => $this->user->id, 'name' => 'Alpha Project']);
        Project::factory()->create(['created_by' => $this->user->id, 'name' => 'Beta Project']);

        $response = $this->actingAs($this->user)
            ->getJson('/api/projects?search=Alpha')
            ->assertStatus(200);

        $names = array_column($response->json('data'), 'name');
        $this->assertContains('Alpha Project', $names);
        $this->assertNotContains('Beta Project', $names);
    }

    public function test_can_filter_projects_by_status(): void
    {
        Project::factory()->create(['created_by' => $this->user->id, 'status' => 'active']);
        Project::factory()->create(['created_by' => $this->user->id, 'status' => 'archived']);

        $response = $this->actingAs($this->user)
            ->getJson('/api/projects?status=archived')
            ->assertStatus(200);

        foreach ($response->json('data') as $project) {
            $this->assertEquals('archived', $project['status']);
        }
    }

    public function test_project_has_no_delete_endpoint(): void
    {
        $project = Project::factory()->create(['created_by' => $this->user->id]);

        $this->actingAs($this->user)
            ->deleteJson("/api/projects/{$project->id}")
            ->assertStatus(405);
    }
}
