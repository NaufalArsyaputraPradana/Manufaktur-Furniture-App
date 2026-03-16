<?php

namespace App\Http\Requests\Production;

use App\Models\ProductionTodo;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductionTodoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:191'],
            'description' => ['nullable', 'string'],
            'deadline'    => ['nullable', 'date'],
            'status'      => ['required', 'in:' . implode(',', ProductionTodo::statuses())],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'  => 'Judul tugas wajib diisi.',
            'title.max'       => 'Judul tugas maksimal 191 karakter.',
            'deadline.date'   => 'Format tanggal deadline tidak valid.',
            'status.required' => 'Status tugas wajib diisi.',
            'status.in'       => 'Status tidak valid.',
        ];
    }
}

