<?php

namespace Database\Seeders;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $today = Carbon::today();

        $tasks = [
            // Project 1 — Website E-Commerce
            [
                'project_id' => 1,
                'category_id' => 2,
                'created_by' => 1,
                'title' => 'Setup CI/CD Pipeline',
                'description' => 'Konfigurasi GitHub Actions untuk automated testing dan deployment ke staging server.',
                'due_date' => $today->copy()->subDays(3),
            ],
            [
                'project_id' => 1,
                'category_id' => 3,
                'created_by' => 1,
                'title' => 'Database Schema Review',
                'description' => 'Review dan validasi schema database untuk memastikan semua relasi dan index sudah optimal.',
                'due_date' => $today->copy()->subDays(1),
            ],
            [
                'project_id' => 1,
                'category_id' => 1,
                'created_by' => 1,
                'title' => 'Deploy Backend v1.0',
                'description' => 'Deploy versi pertama backend API ke server produksi dan lakukan smoke testing.',
                'due_date' => $today->copy(),
            ],
            [
                'project_id' => 1,
                'category_id' => 4,
                'created_by' => 1,
                'title' => 'Project Initialization',
                'description' => 'Setup repository, struktur folder proyek, dan konfigurasi development environment.',
                'due_date' => $today->copy()->addDays(1),
            ],
            // Project 2 — Aplikasi Mobile
            [
                'project_id' => 2,
                'category_id' => 1,
                'created_by' => 1,
                'title' => 'Login Page UI',
                'description' => 'Desain dan implementasi halaman login dengan validasi form dan integrasi auth API.',
                'due_date' => $today->copy()->addDays(2),
            ],
            [
                'project_id' => 2,
                'category_id' => 2,
                'created_by' => 1,
                'title' => 'API Authentication',
                'description' => 'Implementasi token-based authentication menggunakan Sanctum untuk semua endpoint API.',
                'due_date' => $today->copy()->addDays(5),
            ],
            [
                'project_id' => 2,
                'category_id' => 1,
                'created_by' => 1,
                'title' => 'Mobile App Home Screen',
                'description' => 'Implementasi halaman utama aplikasi mobile dengan daftar task dan navigasi bottom tab.',
                'due_date' => $today->copy()->addDays(10),
            ],
            // Project 3 — Backend Services v2
            [
                'project_id' => 3,
                'category_id' => 5,
                'created_by' => 1,
                'title' => 'User Testing Round 1',
                'description' => 'Pelaksanaan sesi user testing pertama dengan 5 pengguna representatif.',
                'due_date' => $today->copy()->addDays(14),
            ],
        ];

        foreach ($tasks as $task) {
            Task::create($task);
        }
    }
}
