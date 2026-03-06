<?php

namespace Tests\Feature\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_categories(): void
    {
        $user = User::factory()->create();
        Category::factory()->count(5)->create();

        $this->actingAs($user)
            ->getJson('/api/categories')
            ->assertStatus(200)
            ->assertJsonStructure(['data', 'message'])
            ->assertJsonCount(5, 'data');
    }

    public function test_categories_ordered_by_id(): void
    {
        $user = User::factory()->create();
        Category::factory()->create(['id' => 3, 'name' => 'Testing']);
        Category::factory()->create(['id' => 1, 'name' => 'Todo']);
        Category::factory()->create(['id' => 2, 'name' => 'In Progress']);

        $response = $this->actingAs($user)
            ->getJson('/api/categories')
            ->assertStatus(200);

        $names = array_column($response->json('data'), 'name');
        $this->assertEquals(['Todo', 'In Progress', 'Testing'], $names);
    }

    public function test_unauthenticated_cannot_list_categories(): void
    {
        $this->getJson('/api/categories')
            ->assertStatus(401);
    }
}
