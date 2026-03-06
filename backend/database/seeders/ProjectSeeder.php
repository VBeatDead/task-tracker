<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            [
                'created_by' => 1,
                'name' => 'Website E-Commerce',
                'description' => 'Pengembangan platform e-commerce dengan fitur katalog produk, keranjang belanja, dan pembayaran online.',
                'status' => 'active',
            ],
            [
                'created_by' => 1,
                'name' => 'Aplikasi Mobile',
                'description' => 'Aplikasi mobile cross-platform untuk iOS dan Android menggunakan Flutter.',
                'status' => 'active',
            ],
            [
                'created_by' => 1,
                'name' => 'Backend Services v2',
                'description' => 'Refactoring backend services ke arsitektur microservices dengan Docker dan Kubernetes.',
                'status' => 'archived',
            ],
        ];

        foreach ($projects as $project) {
            Project::create($project);
        }
    }
}
