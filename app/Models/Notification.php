<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    /**
     * Disable default timestamps since we have custom ones.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'type',
        'data',
        'is_read',
        'created_at',
        'read_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data' => 'array',
            'is_read' => 'boolean',
            'created_at' => 'datetime',
            'read_at' => 'datetime',
        ];
    }

    /**
     * Notification type constants
     */
    const TYPE_BOOKING_CREATED = 'booking_created';
    const TYPE_BOOKING_CONFIRMED = 'booking_confirmed';
    const TYPE_BOOKING_REJECTED = 'booking_rejected';
    const TYPE_BOOKING_CANCELLED = 'booking_cancelled';
    const TYPE_BOOKING_COMPLETED = 'booking_completed';
    const TYPE_RATING_RECEIVED = 'rating_received';
    const TYPE_ACCOUNT_ACTIVATED = 'account_activated';
    const TYPE_ACCOUNT_DEACTIVATED = 'account_deactivated';

    /**
     * Get the user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Get the notification message based on type.
     */
    public function getMessageAttribute(): string
    {
        $data = $this->data ?? [];

        return match($this->type) {
            self::TYPE_BOOKING_CREATED => 'You have a new booking request from ' . ($data['customer_name'] ?? 'a customer'),
            self::TYPE_BOOKING_CONFIRMED => 'Your booking has been confirmed',
            self::TYPE_BOOKING_REJECTED => 'Your booking has been rejected',
            self::TYPE_BOOKING_CANCELLED => 'A booking has been cancelled',
            self::TYPE_BOOKING_COMPLETED => 'Your booking has been marked as completed',
            self::TYPE_RATING_RECEIVED => 'You received a new rating',
            self::TYPE_ACCOUNT_ACTIVATED => 'Your account has been activated',
            self::TYPE_ACCOUNT_DEACTIVATED => 'Your account has been deactivated',
            default => 'You have a new notification',
        };
    }
}
