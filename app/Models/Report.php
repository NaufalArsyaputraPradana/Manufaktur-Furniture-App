<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'report_type',
        'title',
        'start_date',
        'end_date',
        'status',
        'data',
        'filters',
        'generated_by',
    ];

    protected $casts = [
        'filters' => 'array',
        'data' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'generated_at' => 'datetime',
    ];

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('report_type', $type);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}