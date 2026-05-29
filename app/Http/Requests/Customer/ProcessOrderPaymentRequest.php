<?php

namespace App\Http\Requests\Customer;

use App\Models\Payment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProcessOrderPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $order = $this->route('order');
        
        if (!$order) {
            return false;
        }
        
        // Customer can only pay for their own orders
        // Admin can pay for any order
        $user = $this->user();
        if (!$user || !$user->role) {
            return false;
        }

        if ($user->role->name === 'customer') {
            $orderStatusValue = $order->status instanceof \App\Enums\OrderStatus
                ? $order->status->value
                : $order->status;
            return $order->user_id === $user->id
                && !in_array($orderStatusValue, ['completed', 'cancelled'], true);
        }
        
        return $user->role->name === 'admin';
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
