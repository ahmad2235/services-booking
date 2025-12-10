<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProviderTimeSlot extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'provider_profile_id',
        'start_datetime',
        'end_datetime',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_datetime' => 'datetime',
            'end_datetime' => 'datetime',
        ];
    }

    /**
     * Status constants
     */
    const STATUS_AVAILABLE = 'available';
    const STATUS_RESERVED = 'reserved';
    const STATUS_BLOCKED = 'blocked';

    /**
     * Get the provider profile.
     */
    public function providerProfile(): BelongsTo
    {
        return $this->belongsTo(ProviderProfile::class);
    }

    /**
     * Get the bookings for this time slot.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'time_slot_id');
    }

    /**
     * Check if the slot is available.
     */
    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_AVAILABLE;
    }

    /**
     * Check if the slot is reserved.
     */
    public function isReserved(): bool
    {
        return $this->status === self::STATUS_RESERVED;
    }

    /**
     * Check if the slot is blocked.
     */
    public function isBlocked(): bool
    {
        return $this->status === self::STATUS_BLOCKED;
    }

    /**
     * Check if the slot is in the future.
     */
    public function isFuture(): bool
    {
        return $this->start_datetime->isFuture();
    }

    /**
     * Scope a query to only include available slots.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }

    /**
     * Scope a query to only include future slots.
     */
    public function scopeFuture($query)
    {
        return $query->where('start_datetime', '>', Carbon::now());
    }

    /**
     * Get the formatted time range.
     */
    public function getFormattedTimeRangeAttribute(): string
    {
        return $this->start_datetime->format('M d, Y H:i') . ' - ' . $this->end_datetime->format('H:i');
    }

    /**
     * Get the duration in minutes.
     */
    public function getDurationMinutesAttribute(): int
    {
        return $this->start_datetime->diffInMinutes($this->end_datetime);
    }

    /**
     * Check if this slot overlaps with another time range.
     */
    public function overlapsWithRange(Carbon $start, Carbon $end): bool
    {
        return $this->start_datetime < $end && $this->end_datetime > $start;
    }

    /**
     * Check for overlapping time slots for the same provider.
     */
    public static function hasOverlap(int $providerProfileId, Carbon $start, Carbon $end, ?int $excludeId = null): bool
    {
        $query = self::where('provider_profile_id', $providerProfileId)
            ->where('start_datetime', '<', $end)
            ->where('end_datetime', '>', $start);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }
}
