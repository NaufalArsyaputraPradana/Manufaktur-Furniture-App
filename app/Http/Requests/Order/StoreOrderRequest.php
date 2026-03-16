<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'customer_notes' => ['nullable', 'string', 'max:1000'],
            'expected_completion_date' => ['nullable', 'date', 'after_or_equal:today'],
            
            // Order items validation
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['nullable', 'exists:products,id'],
            'items.*.product_name' => ['required', 'string', 'max:255'],
            'items.*.is_custom' => ['boolean'],
            'items.*.custom_specifications' => ['nullable', 'string', 'max:2000'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:1000'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'user_id' => 'Customer',
            'customer_notes' => 'Catatan Customer',
            'expected_completion_date' => 'Tanggal Estimasi Selesai',
            'items' => 'Item Pesanan',
            'items.*.product_name' => 'Nama Produk',
            'items.*.quantity' => 'Jumlah',
            'items.*.unit_price' => 'Harga Satuan',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'items.required' => 'Pesanan harus memiliki minimal 1 item.',
            'items.*.quantity.required' => 'Jumlah produk harus diisi.',
            'items.*.quantity.min' => 'Jumlah produk minimal 1.',
            'items.*.unit_price.required' => 'Harga satuan harus diisi.',
            'items.*.unit_price.min' => 'Harga satuan tidak boleh negatif.',
        ];
    }
}
