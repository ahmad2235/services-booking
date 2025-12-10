<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class ProviderProfile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'bio',
        'years_of_experience',
        'min_price',
        'max_price',
        'coverage_description',
        'avg_rating',
        'total_reviews',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'years_of_experience' => 'integer',
            'min_price' => 'decimal:2',
            'max_price' => 'decimal:2',
            'avg_rating' => 'decimal:2',
            'total_reviews' => 'integer',
        ];
    }

    /**
     * Get the user that owns the provider profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the locations covered by this provider.
     */
    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(Location::class, 'provider_locations')
                    ->withTimestamps();
    }

    /**
     * Get the provider location pivot records.
     */
    public function providerLocations(): HasMany
    {
        return $this->hasMany(ProviderLocation::class);
    }

    /**
     * Get the services offered by this provider.
     */
    public function providerServices(): HasMany
    {
        return $this->hasMany(ProviderService::class);
    }

    /**
     * Get the time slots for this provider.
     */
    public function timeSlots(): HasMany
    {
        return $this->hasMany(ProviderTimeSlot::class);
    }

    /**
     * Get the bookings for this provider.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get the ratings for this provider.
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * Get visible ratings only.
     */
    public function visibleRatings(): HasMany
    {
        return $this->hasMany(Rating::class)->where('is_visible', true);
    }

    /**
     * Get the price range as a formatted string.
     */
    public function getPriceRangeAttribute(): string
    {
        if ($this->min_price && $this->max_price) {
            return '$' . number_format($this->min_price, 2) . ' - $' . number_format($this->max_price, 2);
        } elseif ($this->min_price) {
            return 'From $' . number_format($this->min_price, 2);
        } elseif ($this->max_price) {
            return 'Up to $' . number_format($this->max_price, 2);
        }
        return 'Price varies';
    }

    /**
     * Recalculate average rating and total reviews.
     */
    public function recalculateRating(): void
    {
        $visibleRatings = $this->ratings()->where('is_visible', true);
        
        $this->total_reviews = $visibleRatings->count();
        $this->avg_rating = $visibleRatings->avg('rating_value') ?? 0.00;
        
        $this->save();
    }
}
