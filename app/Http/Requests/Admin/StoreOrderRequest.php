<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Store Order Request
 * 
 * Validates order creation data including products and customizations
 */
class StoreOrderRequest extends FormRequest
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
        $rules = [
            'user_id' => 'required|exists:users,id',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'nullable|integer|exists:products,id',
            'products.*.product_name' => 'required|string|max:255',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.is_custom' => 'nullable|boolean',
            'products.*.customizations' => 'nullable|array',
            'products.*.customizations.description' => 'nullable|string|max:1000',
            'order_date' => 'required|date',
            'estimated_delivery_date' => 'nullable|date|after_or_equal:order_date',
            'shipping_address' => 'required|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ];

        // Dynamic validation for unit price (required if not custom)
        foreach ($this->input('products', []) as $idx => $item) {
            if (empty($item['is_custom'])) {
                $rules["products.$idx.unit_price"] = 'required|numeric|min:0';
            } else {
                $rules["products.$idx.unit_price"] = 'nullable|numeric|min:0';
            }

            // Validate file upload only if file is present
            if ($this->hasFile("products.$idx.customizations.design_image")) {
                $rules["products.$idx.customizations.design_image"] = 'file|mimes:jpg,jpeg,png,pdf|max:2048';
            } else {
                $rules["products.$idx.customizations.design_image"] = 'nullable';
            }
        }

        return $rules;
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
            'products' => 'produk',
            'products.*.product_id' => 'ID produk',
            'products.*.product_name' => 'nama produk',
            'products.*.quantity' => 'jumlah',
            'products.*.unit_price' => 'harga satuan',
            'products.*.is_custom' => 'produk custom',
            'products.*.customizations.description' => 'deskripsi kustomisasi',
            'products.*.customizations.design_image' => 'gambar desain',
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
            'products.required' => 'Minimal 1 produk harus dipilih.',
            'products.min' => 'Minimal 1 produk harus dipilih.',
            'products.*.product_name.required' => 'Nama produk harus diisi.',
            'products.*.quantity.required' => 'Jumlah produk harus diisi.',
            'products.*.quantity.min' => 'Jumlah produk minimal 1.',
            'products.*.unit_price.required' => 'Harga satuan harus diisi.',
            'products.*.unit_price.numeric' => 'Harga satuan harus berupa angka.',
            'products.*.unit_price.min' => 'Harga satuan tidak boleh negatif.',
            'products.*.customizations.design_image.file' => 'File desain harus berupa file.',
            'products.*.customizations.design_image.mimes' => 'File desain harus berformat jpg, jpeg, png, atau pdf.',
            'products.*.customizations.design_image.max' => 'Ukuran file desain maksimal 2MB.',
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

