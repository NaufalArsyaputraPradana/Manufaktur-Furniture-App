<?php

namespace App\Http\Requests\Production;

use App\Models\OrderShippingLog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderShippingLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('production_staff') ?? false;
    }

    public function rules(): array
    {
        return [
            'stage' => [
                'required',
                'string',
                Rule::in(array_keys(OrderShippingLog::stageLabels())),
            ],
            'notes' => 'nullable|string|max:2000',
            'documentation' => 'nullable|array',
            'documentation.*' => 'file|mimes:jpg,jpeg,png,webp,gif|max:5120',
            'courier_note' => 'nullable|string|max:120',
            'tracking_note' => 'nullable|string|max:120',
        ];
    }

    public function attributes(): array
    {
        return [
            'stage' => 'tahapan',
            'notes' => 'catatan',
            'documentation' => 'dokumentasi',
            'courier_note' => 'kurir',
            'tracking_note' => 'nomor resi',
        ];
    }
}
