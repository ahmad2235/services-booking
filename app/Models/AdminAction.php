<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminAction extends Model
{
    use HasFactory;

    /**
     * Disable updated_at since we only have created_at.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'admin_id',
        'action_type',
        'target_type',
        'target_id',
        'details',
        'created_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'target_id' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Action type constants
     */
    const ACTION_USER_ACTIVATED = 'user_activated';
    const ACTION_USER_DEACTIVATED = 'user_deactivated';
    const ACTION_RATING_HIDDEN = 'rating_hidden';
    const ACTION_RATING_SHOWN = 'rating_shown';
    const ACTION_CATEGORY_CREATED = 'category_created';
    const ACTION_CATEGORY_UPDATED = 'category_updated';
    const ACTION_CATEGORY_DELETED = 'category_deleted';
    const ACTION_SERVICE_CREATED = 'service_created';
    const ACTION_SERVICE_UPDATED = 'service_updated';
    const ACTION_SERVICE_DELETED = 'service_deleted';

    /**
     * Target type constants
     */
    const TARGET_USER = 'user';
    const TARGET_BOOKING = 'booking';
    const TARGET_RATING = 'rating';
    const TARGET_CATEGORY = 'category';
    const TARGET_SERVICE = 'service';

    /**
     * Get the admin who performed this action.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Get a human-readable description of the action.
     */
    public function getDescriptionAttribute(): string
    {
        return match($this->action_type) {
            self::ACTION_USER_ACTIVATED => 'Activated user account',
            self::ACTION_USER_DEACTIVATED => 'Deactivated user account',
            self::ACTION_RATING_HIDDEN => 'Hidden rating from public view',
            self::ACTION_RATING_SHOWN => 'Made rating visible to public',
            self::ACTION_CATEGORY_CREATED => 'Created new service category',
            self::ACTION_CATEGORY_UPDATED => 'Updated service category',
            self::ACTION_CATEGORY_DELETED => 'Deleted service category',
            self::ACTION_SERVICE_CREATED => 'Created new service',
            self::ACTION_SERVICE_UPDATED => 'Updated service',
            self::ACTION_SERVICE_DELETED => 'Deleted service',
            default => $this->action_type,
        };
    }
}
