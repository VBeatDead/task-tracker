<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('project_id')->constrained('projects');
            $table->foreignId('category_id')->constrained('categories');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('deleted_by')->nullable()->constrained('users');
            $table->string('title', 255);
            $table->text('description');
            $table->date('due_date');
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
