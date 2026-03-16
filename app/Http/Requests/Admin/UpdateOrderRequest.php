<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Update Order Request
 * 
 * Validates order update data
 */
class UpdateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Sudah dibatasi oleh middleware 'role:admin' pada route
        return (bool) $this->user();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'order_date' => 'required|date',
            'estimated_delivery_date' => 'nullable|date|after_or_equal:order_date',
            'shipping_address' => 'required|string|max:500',
            'notes' => 'nullable|string|max:1000',
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
            'user_id' => 'pelanggan',
            'order_date' => 'tanggal order',
            'estimated_delivery_date' => 'estimasi pengiriman',
            'shipping_address' => 'alamat pengiriman',
            'notes' => 'catatan',
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
            'user_id.required' => 'Pelanggan harus dipilih.',
            'user_id.exists' => 'Pelanggan tidak ditemukan.',
            'order_date.required' => 'Tanggal order harus diisi.',
            'order_date.date' => 'Format tanggal order tidak valid.',
            'estimated_delivery_date.date' => 'Format tanggal estimasi pengiriman tidak valid.',
            'estimated_delivery_date.after_or_equal' => 'Tanggal estimasi pengiriman tidak boleh sebelum tanggal order.',
            'shipping_address.required' => 'Alamat pengiriman harus diisi.',
            'shipping_address.max' => 'Alamat pengiriman maksimal 500 karakter.',
            'notes.max' => 'Catatan maksimal 1000 karakter.',
        ];
    }
}
