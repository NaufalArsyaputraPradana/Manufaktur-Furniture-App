<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerPortalSession extends Model
{
    use HasFactory;

    protected $table = 'customer_portal_sessions';

    protected $fillable = [
        'user_id',
        'session_token',
        'ip_address',
        'user_agent',
        'status',
        'last_activity_at',
        'expires_at',
    ];

    protected $casts = [
        'last_activity_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->expires_at->isFuture();
    }

    public function markAsInactive(): void
    {
        $this->update(['status' => 'inactive']);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->whereDate('expires_at', '>', now());
    }
}
