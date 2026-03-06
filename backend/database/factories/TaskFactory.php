<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id'  => Project::factory(),
            'category_id' => Category::factory(),
            'created_by'  => User::factory(),
            'title'       => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'due_date'    => fake()->dateTimeBetween('now', '+30 days')->format('Y-m-d'),
            'deleted_at'  => null,
            'deleted_by'  => null,
        ];
    }
}
