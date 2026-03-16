<?php

namespace App\Http\Requests\Production;

use App\Models\ProductionTodo;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductionTodoRequest extends FormRequest
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
            'status'      => ['nullable', 'in:' . implode(',', ProductionTodo::statuses())],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul tugas wajib diisi.',
            'title.max'      => 'Judul tugas maksimal 191 karakter.',
            'deadline.date'  => 'Format tanggal deadline tidak valid.',
            'status.in'      => 'Status tidak valid.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if (! $this->has('status') || $this->input('status') === null) {
            $this->merge([
                'status' => 'pending',
            ]);
        }
    }
}

