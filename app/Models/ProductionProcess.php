<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;

class ProductionProcess extends Model
{
    use HasFactory;

    protected $table = 'production_processes';

    protected $fillable = [
        'production_code',
        'order_id',
        'order_detail_id',
        'stage',
        'status',
        'documentation',
        'progress_percentage',
        'assigned_to',
        'started_at',
        'completed_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'progress_percentage' => 'integer',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    const STAGES = [
        'pending' => 0,
        'cutting' => 15,
        'assembly' => 45,
        'sanding' => 65,
        'finishing' => 85,
        'quality_control' => 95,
        'completed' => 100,
    ];

    const STAGE_LABELS = [
        'cutting' => 'Pemotongan Bahan',
        'assembly' => 'Perakitan',
        'sanding' => 'Penghalusan',
        'finishing' => 'Finishing',
        'quality_control' => 'Quality Control',
    ];

    const IN_PROGRESS_STATUSES = [
        'cutting',
        'assembly',
        'sanding',
        'finishing',
        'quality_control',
        'in_progress',
    ];

    public static function generateProductionCode(): string
    {
        $date = now()->format('Ymd');
        $last = static::whereDate('created_at', today())->orderByDesc('id')->first();
        $sequence = $last ? ((int) substr($last->production_code, -5)) + 1 : 1;

        return sprintf('PROD-%s-%05d', $date, $sequence);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function orderDetail(): BelongsTo
    {
        return $this->belongsTo(OrderDetail::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->assignedTo();
    }

    public function logs(): HasMany
    {
        return $this->hasMany(ProductionLog::class, 'production_process_id')
            ->orderByDesc('created_at');
    }

    protected function product(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->orderDetail?->product,
        );
    }

    protected function statusColor(): Attribute
    {
        return Attribute::make(
            get: fn() => match ($this->status) {
                'pending' => 'secondary',
                'cutting', 'assembly' => 'info',
                'sanding', 'finishing', 'in_progress' => 'primary',
                'quality_control' => 'warning',
                'completed' => 'success',
                'paused' => 'secondary',
                'issue' => 'danger',
                default => 'secondary',
            },
        );
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn() => match ($this->status) {
                'pending' => 'Menunggu',
                'cutting' => 'Pemotongan',
                'assembly' => 'Perakitan',
                'sanding' => 'Penghalusan',
                'finishing' => 'Finishing',
                'quality_control' => 'Quality Control',
                'in_progress' => 'Sedang Dikerjakan',
                'completed' => 'Selesai',
                'paused' => 'Dijeda',
                'issue' => 'Ada Masalah',
                default => ucfirst($this->status ?? '–'),
            },
        );
    }

    protected function stageLabel(): Attribute
    {
        return Attribute::make(
            get: fn() => self::STAGE_LABELS[$this->stage] ?? ucfirst(str_replace('_', ' ', $this->stage ?? '')),
        );
    }

    public function updateProgress(): void
    {
        if (isset(self::STAGES[$this->status])) {
            $this->update(['progress_percentage' => self::STAGES[$this->status]]);
        }
    }

    public function getNextStage(): ?string
    {
        $stages = array_keys(self::STAGES);
        $currentIndex = array_search($this->status, $stages);

        if ($currentIndex === false || $currentIndex >= count($stages) - 1) {
            return null;
        }

        return $stages[$currentIndex + 1];
    }

    public function canAdvance(): bool
    {
        return $this->getNextStage() !== null && $this->status !== 'completed';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isInProgress(): bool
    {
        return in_array($this->status, self::IN_PROGRESS_STATUSES);
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeAssignedTo(Builder $query, int $userId): Builder
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', '!=', 'completed');
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeInProgress(Builder $query): Builder
    {
        return $query->whereIn('status', self::IN_PROGRESS_STATUSES);
    }
}