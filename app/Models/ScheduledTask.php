<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScheduledTask extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'scheduled_tasks';

    protected $fillable = [
        'name',
        'description',
        'production_process_id',
        'task_type',
        'priority',
        'scheduled_start_at',
        'scheduled_end_at',
        'actual_start_at',
        'actual_end_at',
        'status',
        'estimated_duration_hours',
        'assigned_resources',
        'progress_percentage',
        'notes',
    ];

    protected $casts = [
        'scheduled_start_at' => 'datetime',
        'scheduled_end_at' => 'datetime',
        'actual_start_at' => 'datetime',
        'actual_end_at' => 'datetime',
        'assigned_resources' => 'array',
    ];

    public function productionProcess(): BelongsTo
    {
        return $this->belongsTo(ProductionProcess::class);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', ['high', 'critical']);
    }

    public function scopeOverdue($query)
    {
        return $query->where('scheduled_end_at', '<', now())
                     ->whereIn('status', ['scheduled', 'in_progress']);
    }

    public function markAsStarted(): void
    {
        $this->update([
            'status' => 'in_progress',
            'actual_start_at' => now(),
        ]);
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'actual_end_at' => now(),
            'progress_percentage' => 100,
        ]);
    }
}
