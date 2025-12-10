<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rating extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'booking_id',
        'provider_profile_id',
        'rating_value',
        'comment',
        'is_visible',
        'hidden_by_admin_id',
        'hidden_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rating_value' => 'integer',
            'is_visible' => 'boolean',
            'hidden_at' => 'datetime',
        ];
    }

    /**
     * Get the booking.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the provider profile.
     */
    public function providerProfile(): BelongsTo
    {
        return $this->belongsTo(ProviderProfile::class);
    }

    /**
     * Get the admin who hid this rating.
     */
    public function hiddenByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hidden_by_admin_id');
    }

    /**
     * Scope a query to only include visible ratings.
     */
    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    /**
     * Scope a query to only include hidden ratings.
     */
    public function scopeHidden($query)
    {
        return $query->where('is_visible', false);
    }

    /**
     * Get the stars as HTML.
     */
    public function getStarsHtmlAttribute(): string
    {
        $html = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $this->rating_value) {
                $html .= '<i class="bi bi-star-fill text-warning"></i>';
            } else {
                $html .= '<i class="bi bi-star text-muted"></i>';
            }
        }
        return $html;
    }

    /**
     * Boot method to handle model events.
     */
    protected static function booted(): void
    {
        static::created(function (Rating $rating) {
            $rating->providerProfile->recalculateRating();
        });

        static::updated(function (Rating $rating) {
            $rating->providerProfile->recalculateRating();
        });

        static::deleted(function (Rating $rating) {
            $rating->providerProfile->recalculateRating();
        });
    }
}
