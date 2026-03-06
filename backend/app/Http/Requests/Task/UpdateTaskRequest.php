<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'due_date'    => 'required|date_format:Y-m-d',
            'category_id' => 'required|exists:categories,id',
            'project_id'  => 'required|exists:projects,id',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'       => 'Judul task wajib diisi.',
            'description.required' => 'Deskripsi task wajib diisi.',
            'due_date.required'    => 'Due date wajib diisi.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists'   => 'Kategori tidak valid.',
            'project_id.required'  => 'Project wajib dipilih.',
            'project_id.exists'    => 'Project tidak valid.',
        ];
    }
}
