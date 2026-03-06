<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'description' => 'required|string',
            'status'      => 'required|in:active,archived',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'        => 'Nama project wajib diisi.',
            'name.max'             => 'Nama project maksimal 255 karakter.',
            'description.required' => 'Deskripsi project wajib diisi.',
            'status.required'      => 'Status tidak valid.',
            'status.in'            => 'Status tidak valid.',
        ];
    }
}
