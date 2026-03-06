<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['id' => 1, 'name' => 'Todo'],
            ['id' => 2, 'name' => 'In Progress'],
            ['id' => 3, 'name' => 'Testing'],
            ['id' => 4, 'name' => 'Done'],
            ['id' => 5, 'name' => 'Pending'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
