<?php

namespace App\Http\Requests\Customer;

use App\Models\Payment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProcessOrderPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'payment_channel' => ['required', 'string', Rule::in([
                Payment::CHANNEL_MANUAL_DP,
                Payment::CHANNEL_MANUAL_FULL,
            ])],
            'payment_proof' => ['required', 'file', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
        ];
    }

    public function messages(): array
    {
        return [
            'payment_proof.required' => 'Unggah bukti transfer terlebih dahulu.',
            'payment_proof.mimes'   => 'Bukti harus berupa gambar (JPEG, PNG, atau WebP).',
            'payment_proof.max'     => 'Ukuran bukti maksimal 4MB.',
        ];
    }
}
