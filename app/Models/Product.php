<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'sku',
        'name',
        'slug',
        'description',
        'base_price',
        'dimensions',
        'wood_type',
        'finishing_type',
        'estimated_production_days',
        'images',
        'is_customizable',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'images' => 'array',
            'is_customizable' => 'boolean',
            'is_active' => 'boolean',
            'estimated_production_days' => 'integer',
        ];
    }

    public function getThumbnailAttribute(): string
    {
        if (!empty($this->images) && is_array($this->images) && isset($this->images[0])) {
            return asset('storage/' . $this->images[0]);
        }

        return 'https://via.placeholder.com/300x300/e9ecef/6c757d?text=No+Image';
    }

    public function getGalleryAttribute(): array
    {
        if (empty($this->images) || !is_array($this->images)) {
            return [];
        }

        return array_map(fn($img) => asset('storage/' . $img), $this->images);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }
}