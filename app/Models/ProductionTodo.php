<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class ProductionTodo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'production_todos';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'deadline',
        'status',
    ];

    protected $casts = [
        'deadline'   => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        if (!$status) {
            return $query;
        }

        return $query->where('status', $status);
    }

    public function scopeSearchTitle(Builder $query, ?string $search): Builder
    {
        if (!$search) {
            return $query;
        }

        return $query->where('title', 'like', '%' . $search . '%');
    }

    public function scopeOrderDefault(Builder $query): Builder
    {
        return $query
            ->orderByRaw("
                CASE status
                    WHEN 'pending' THEN 1
                    WHEN 'in_progress' THEN 2
                    ELSE 3
                END
            ")
            ->orderByRaw('deadline IS NULL')
            ->orderBy('deadline')
            ->orderByDesc('created_at');
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isOverdue(): bool
    {
        return $this->deadline instanceof Carbon
            && $this->deadline->isPast()
            && !$this->isCompleted();
    }

    public static function statuses(): array
    {
        return ['pending', 'in_progress', 'completed'];
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'      => 'Menunggu',
            'in_progress'  => 'Dalam Proses',
            'completed'    => 'Selesai',
            default        => ucfirst(str_replace('_', ' ', (string) $this->status)),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'pending'      => 'badge bg-warning text-dark',
            'in_progress'  => 'badge bg-info text-dark',
            'completed'    => 'badge bg-success',
            default        => 'badge bg-secondary',
        };
    }
}