<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

/**
 * StoreCustomOrderRequest
 * 
 * Validates custom order form input from customer
 * Extracted from OrderTrackingController::storeCustomOrder() for better maintainability
 */
class StoreCustomOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Only authenticated users can create custom orders
        // Customer can create their own custom orders
        // Admin can create custom orders for any customer
        return (bool) $this->user();
    }

    public function rules(): array
    {
        $rules = [
            'products' => 'required|array|min:1',
            'products.*.product_id' => ['nullable', 'sometimes'],
            'products.*.product_name' => 'required|string|max:255',
            'products.*.quantity' => 'required|integer|min:1|max:1000',
            'products.*.is_custom' => 'nullable|boolean',
            'products.*.customizations' => 'nullable|array',
            'products.*.customizations.description' => 'nullable|string|max:1000',
            'products.*.customizations.dimensions' => 'nullable|string|max:255',
            'products.*.customizations.material_type' => 'nullable|string|max:100',
            'products.*.customizations.color_finishing' => 'nullable|string|max:100',
            'shipping_address' => 'required|string|max:500',
            'phone' => 'required|string|max:20',
            'notes' => 'nullable|string|max:1000',
        ];

        // Add dynamic validation for each product item
        $products = $this->input('products', []);
        foreach ($products as $idx => $item) {
            $isCatalogCustom = ($item['product_id'] ?? null) === 'custom';
            $isCustomItem = !empty($item['is_custom']);

            // Unit price required only for custom items
            if ($isCustomItem || $isCatalogCustom) {
                $rules["products.$idx.unit_price"] = 'nullable|numeric|min:0';
            } else {
                $rules["products.$idx.unit_price"] = 'required|numeric|min:100';
            }

            // Design image file validation
            if ($this->hasFile("products.$idx.customizations.design_image")) {
                $rules["products.$idx.customizations.design_image"] = [
                    'file',
                    'mimes:jpeg,jpg,png,webp,pdf',
                    'max:5120', // 5MB
                ];
            }
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'products.required' => 'Minimal ada 1 produk yang dipilih.',
            'products.*.product_name.required' => 'Nama produk wajib diisi.',
            'products.*.quantity.required' => 'Jumlah produk wajib diisi.',
            'products.*.quantity.min' => 'Jumlah minimal 1 produk.',
            'products.*.quantity.max' => 'Jumlah maksimal 1000 produk.',
            'products.*.unit_price.required' => 'Harga satuan wajib diisi.',
            'products.*.unit_price.numeric' => 'Harga harus berupa angka.',
            'products.*.unit_price.min' => 'Harga minimal Rp 100.',
            'products.*.customizations.design_image.file' => 'Desain harus berupa file.',
            'products.*.customizations.design_image.mimes' => 'Format file desain harus: jpeg, jpg, png, webp, atau pdf.',
            'products.*.customizations.design_image.max' => 'Ukuran file desain maksimal 5MB.',
            'shipping_address.required' => 'Alamat pengiriman wajib diisi.',
            'phone.required' => 'Nomor telepon wajib diisi.',
        ];
    }
}
