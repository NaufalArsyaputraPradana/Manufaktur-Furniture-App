<?php

namespace App\Http\Requests\Production;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Update Production Status Request
 * 
 * Validates production process status update
 */
class UpdateProductionStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $role = Auth::user()->role->name ?? '';
        return in_array($role, ['admin', 'production_staff']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => 'required|in:pending,in_progress,quality_check,completed,on_hold',
            'progress_percentage' => 'nullable|integer|min:0|max:100',
            'notes' => 'nullable|string|max:1000',
            'quality_check_notes' => 'nullable|string|max:1000',
            'actual_completion_date' => 'nullable|date',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'status' => 'status produksi',
            'progress_percentage' => 'persentase progress',
            'notes' => 'catatan',
            'quality_check_notes' => 'catatan quality check',
            'actual_completion_date' => 'tanggal selesai',
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'status.required' => 'Status produksi harus dipilih.',
            'status.in' => 'Status produksi tidak valid. Pilih: pending, in_progress, quality_check, completed, atau on_hold.',
            'progress_percentage.integer' => 'Persentase progress harus berupa angka bulat.',
            'progress_percentage.min' => 'Persentase progress minimal 0%.',
            'progress_percentage.max' => 'Persentase progress maksimal 100%.',
            'notes.max' => 'Catatan maksimal 1000 karakter.',
            'quality_check_notes.max' => 'Catatan quality check maksimal 1000 karakter.',
            'actual_completion_date.date' => 'Format tanggal selesai tidak valid.',
        ];
    }
}
