<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionSchedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'production_schedules';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'start_datetime',
        'end_datetime',
        'location',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime'   => 'datetime',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
        'deleted_at'     => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeBetween(Builder $query, ?string $start, ?string $end): Builder
    {
        if ($start && $end) {
            return $query->where(function (Builder $q) use ($start, $end) {
                $q->whereBetween('start_datetime', [$start, $end])
                  ->orWhereBetween('end_datetime', [$start, $end]);
            });
        }

        return $query;
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
            ->orderBy('start_datetime')
            ->orderBy('end_datetime');
    }

    public function isPast(): bool
    {
        return $this->end_datetime?->isPast() ?? false;
    }

    public function durationInMinutes(): ?int
    {
        if (!$this->start_datetime || !$this->end_datetime) {
            return null;
        }

        return $this->start_datetime->diffInMinutes($this->end_datetime);
    }

    public function toFullCalendarEvent(): array
    {
        return [
            'id'    => $this->id,
            'title' => $this->title,
            'start' => $this->start_datetime->toIso8601String(),
            'end'   => $this->end_datetime->toIso8601String(),
            'extendedProps' => [
                'description' => $this->description,
                'location'    => $this->location,
            ],
        ];
    }

    public function toIcsString(): string
    {
        $uid  = sprintf('schedule-%d@furniture-manufacturing-system', $this->id);
        $now  = now()->format('Ymd\THis');
        $from = $this->start_datetime->format('Ymd\THis');
        $to   = $this->end_datetime->format('Ymd\THis');

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Furniture Manufacturing System//ID',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'BEGIN:VEVENT',
            'UID:' . $uid,
            'DTSTAMP:' . $now . 'Z',
            'DTSTART:' . $from . 'Z',
            'DTEND:' . $to . 'Z',
            'SUMMARY:' . $this->escapeIcsText((string) $this->title),
            'DESCRIPTION:' . $this->escapeIcsText((string) $this->description),
            'LOCATION:' . $this->escapeIcsText((string) $this->location),
            'END:VEVENT',
            'END:VCALENDAR',
        ];

        return implode("\r\n", $lines);
    }

    protected function escapeIcsText(string $text): string
    {
        $text = str_replace(['\\', ';', ','], ['\\\\', '\;', '\,'], $text);
        $text = preg_replace('/\R/', '\n', $text);

        return $text ?? '';
    }
}