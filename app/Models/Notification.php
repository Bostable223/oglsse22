<?php
// app/Models/Notification.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'action_url',
        'data',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Scope: Unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope: Recent notifications
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Get icon based on type
     */
    public function getIconAttribute(): string
    {
        return match($this->type) {
            'listing_approved' => 'fa-check-circle',
            'listing_rejected' => 'fa-times-circle',
            'package_expiring' => 'fa-clock',
            'new_message' => 'fa-envelope',
            default => 'fa-bell',
        };
    }

    /**
     * Get color based on type
     */
    public function getColorAttribute(): string
    {
        return match($this->type) {
            'listing_approved' => 'green',
            'listing_rejected' => 'red',
            'package_expiring' => 'yellow',
            'new_message' => 'blue',
            default => 'gray',
        };
    }
}