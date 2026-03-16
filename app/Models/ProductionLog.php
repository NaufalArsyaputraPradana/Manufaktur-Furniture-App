<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ProductionLog extends Model
{
    use HasFactory;

    protected $table = 'production_logs';

    protected $fillable = [
        'production_process_id',
        'user_id',
        'stage',
        'action',
        'progress_percentage',
        'notes',
        'material_used',
        'documentation_images',
    ];

    protected function casts(): array
    {
        return [
            'documentation_images' => 'array',
            'material_used' => 'array',
            'progress_percentage' => 'integer',
        ];
    }

    public function productionProcess(): BelongsTo
    {
        return $this->belongsTo(ProductionProcess::class, 'production_process_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function stageLabel(): Attribute
    {
        return Attribute::make(
            get: fn() => match ($this->stage) {
                'cutting' => 'Pemotongan',
                'assembly' => 'Perakitan',
                'sanding' => 'Penghalusan',
                'finishing' => 'Finishing',
                'quality_control' => 'Quality Control',
                default => ucfirst(str_replace('_', ' ', $this->stage ?? '–')),
            },
        );
    }

    protected function actionLabel(): Attribute
    {
        return Attribute::make(
            get: fn() => match ($this->action) {
                'started' => 'Dimulai',
                'in_progress' => 'Sedang Dikerjakan',
                'completed' => 'Selesai',
                'paused' => 'Dijeda',
                'issue' => 'Ada Masalah',
                default => ucfirst(str_replace('_', ' ', $this->action ?? '–')),
            },
        );
    }

    protected function actionColor(): Attribute
    {
        return Attribute::make(
            get: fn() => match ($this->action) {
                'started' => 'primary',
                'in_progress' => 'info',
                'completed' => 'success',
                'paused' => 'warning',
                'issue' => 'danger',
                default => 'secondary',
            },
        );
    }

    protected function actionIcon(): Attribute
    {
        return Attribute::make(
            get: fn() => match ($this->action) {
                'started' => 'bi-play-circle-fill',
                'in_progress' => 'bi-gear-fill',
                'completed' => 'bi-check-circle-fill',
                'paused' => 'bi-pause-circle-fill',
                'issue' => 'bi-exclamation-triangle-fill',
                default => 'bi-circle-fill',
            },
        );
    }
}