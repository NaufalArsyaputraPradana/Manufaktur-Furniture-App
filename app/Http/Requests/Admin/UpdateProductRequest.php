<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Update Product Request
 * 
 * Validates product update data
 */
class UpdateProductRequest extends FormRequest
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
        $productId = $this->route('product')?->id;

        return [
            'category_id'               => ['required', 'exists:categories,id'],
            'name'                      => ['required', 'string', 'max:255'],
            'sku'                       => [
                'required',
                'string',
                'max:50',
                Rule::unique('products', 'sku')->ignore($productId),
            ],
            'base_price'                => ['nullable', 'numeric', 'min:0'],
            'description'               => ['nullable', 'string'],
            'dimensions'                => ['nullable', 'string', 'max:100'],
            'wood_type'                 => ['nullable', 'string', 'max:100'],
            'finishing_type'            => ['nullable', 'string', 'max:100'],
            'estimated_production_days' => ['required', 'integer', 'min:1'],
            'is_customizable'           => ['nullable', 'boolean'],
            'is_active'                 => ['nullable', 'boolean'],
            'images.*'                  => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'category_id'               => 'kategori',
            'name'                      => 'nama produk',
            'sku'                       => 'SKU',
            'description'               => 'deskripsi',
            'base_price'                => 'harga dasar',
            'dimensions'                => 'dimensi',
            'wood_type'                 => 'jenis kayu',
            'finishing_type'            => 'jenis finishing',
            'estimated_production_days' => 'estimasi hari produksi',
            'images.*'                  => 'gambar',
            'is_customizable'           => 'dapat dikustom',
            'is_active'                 => 'status aktif',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'category_id.required'               => 'Kategori harus dipilih.',
            'category_id.exists'                 => 'Kategori tidak valid.',
            'name.required'                      => 'Nama produk harus diisi.',
            'sku.required'                       => 'SKU produk harus diisi.',
            'sku.unique'                         => 'SKU produk sudah digunakan.',
            'base_price.required'                => 'Harga dasar harus diisi.',
            'base_price.numeric'                 => 'Harga dasar harus berupa angka.',
            'base_price.min'                     => 'Harga dasar minimal 0.',
            'estimated_production_days.required' => 'Estimasi hari produksi wajib diisi.',
            'estimated_production_days.integer'  => 'Estimasi hari produksi harus berupa angka.',
            'estimated_production_days.min'      => 'Estimasi hari produksi minimal 1 hari.',
            'images.*.image'                     => 'File harus berupa gambar.',
            'images.*.mimes'                     => 'Gambar harus berformat: jpg, jpeg, png, atau webp.',
            'images.*.max'                       => 'Ukuran gambar maksimal 2MB per file.',
        ];
    }
}
