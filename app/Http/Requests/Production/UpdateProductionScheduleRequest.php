<?php

namespace App\Http\Requests\Production;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductionScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'title'           => ['required', 'string', 'max:191'],
            'description'     => ['nullable', 'string'],
            'start_datetime'  => ['required', 'date'],
            'end_datetime'    => ['required', 'date', 'after_or_equal:start_datetime'],
            'location'        => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'          => 'Judul jadwal wajib diisi.',
            'title.max'               => 'Judul jadwal maksimal 191 karakter.',
            'start_datetime.required' => 'Waktu mulai jadwal wajib diisi.',
            'start_datetime.date'     => 'Format waktu mulai tidak valid.',
            'end_datetime.required'   => 'Waktu selesai jadwal wajib diisi.',
            'end_datetime.date'       => 'Format waktu selesai tidak valid.',
            'end_datetime.after_or_equal' => 'Waktu selesai tidak boleh lebih kecil dari waktu mulai.',
            'location.max'            => 'Lokasi maksimal 255 karakter.',
        ];
    }
}

