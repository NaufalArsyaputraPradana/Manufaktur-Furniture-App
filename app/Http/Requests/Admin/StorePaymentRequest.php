<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Store Payment Request
 * 
 * Validates payment creation data
 */
class StorePaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $role = Auth::user()->role->name ?? '';
        return in_array($role, ['admin', 'staff']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'order_id' => 'required|exists:orders,id',
            'payment_method' => 'required|in:transfer,cash,credit_card,e-wallet',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'proof_of_payment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
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
            'order_id' => 'order',
            'payment_method' => 'metode pembayaran',
            'amount' => 'jumlah pembayaran',
            'payment_date' => 'tanggal pembayaran',
            'proof_of_payment' => 'bukti pembayaran',
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
            'order_id.required' => 'Order harus dipilih.',
            'order_id.exists' => 'Order tidak ditemukan.',
            'payment_method.required' => 'Metode pembayaran harus dipilih.',
            'payment_method.in' => 'Metode pembayaran tidak valid. Pilih: transfer, cash, credit_card, atau e-wallet.',
            'amount.required' => 'Jumlah pembayaran harus diisi.',
            'amount.numeric' => 'Jumlah pembayaran harus berupa angka.',
            'amount.min' => 'Jumlah pembayaran tidak boleh negatif.',
            'payment_date.required' => 'Tanggal pembayaran harus diisi.',
            'payment_date.date' => 'Format tanggal pembayaran tidak valid.',
            'proof_of_payment.file' => 'Bukti pembayaran harus berupa file.',
            'proof_of_payment.mimes' => 'Bukti pembayaran harus berformat jpg, jpeg, png, atau pdf.',
            'proof_of_payment.max' => 'Ukuran bukti pembayaran maksimal 2MB.',
            'notes.max' => 'Catatan maksimal 1000 karakter.',
        ];
    }
}
