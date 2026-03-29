<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class OrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'quantity',
        'unit_price',
        'subtotal',
        'is_custom',
        'custom_specifications',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'is_custom' => 'boolean',
            'custom_specifications' => 'array',
            'quantity' => 'integer',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function isCustom(): bool
    {
        return $this->is_custom === true;
    }

    public function getFormattedPriceAttribute(): string
    {
        $price = $this->unit_price !== null ? (float) $this->unit_price : 0;
        return 'Rp ' . number_format($price, 0, ',', '.');
    }

    public function getFormattedSubtotalAttribute(): string
    {
        $subtotal = $this->subtotal !== null ? (float) $this->subtotal : 0;
        return 'Rp ' . number_format($subtotal, 0, ',', '.');
    }

    public function getCustomBomAttribute(): ?array
    {
        if ($this->is_custom && !empty($this->custom_specifications['bom'])) {
            return $this->custom_specifications['bom'];
        }
        return null;
    }

    public function getSpecsTextAttribute(): string
    {
        if ($this->is_custom) {
            return $this->custom_specifications['description'] ?? '-';
        }
        return 'Standar Katalog';
    }

    /** URL publik untuk gambar desain custom (gambar saja; PDF tidak dijadikan URL img). */
    public function customDesignImageUrl(): ?string
    {
        if (!$this->is_custom) {
            return null;
        }
        $path = $this->custom_specifications['design_image'] ?? null;
        if (!$path || str_ends_with(strtolower($path), '.pdf')) {
            return null;
        }
        if (!Storage::disk('public')->exists($path)) {
            return null;
        }

        return asset('storage/' . $path);
    }

    /**
     * Placeholder — Bill of Material belum diimplementasikan.
     * Kembalikan array kosong agar tidak ada runtime error.
     */
    public function calculateMaterialRequirements(): array
    {
        return [];
    }
}