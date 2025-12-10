<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'base_price',
        'duration_minutes',
        'default_duration_minutes',
        'default_price_from',
        'default_price_to',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'duration_minutes' => 'integer',
            'base_price' => 'decimal:2',
            'default_duration_minutes' => 'integer',
            'default_price_from' => 'decimal:2',
            'default_price_to' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the category this service belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    /**
     * Get the provider services for this service.
     */
    public function providerServices(): HasMany
    {
        return $this->hasMany(ProviderService::class);
    }

    /**
     * Get the active provider services.
     */
    public function activeProviderServices(): HasMany
    {
        return $this->hasMany(ProviderService::class)->where('is_active', true);
    }

    /**
     * Scope a query to only include active services.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the default price range as a formatted string.
     */
    public function getDefaultPriceRangeAttribute(): string
    {
        if ($this->default_price_from && $this->default_price_to) {
            return '$' . number_format($this->default_price_from, 2) . ' - $' . number_format($this->default_price_to, 2);
        } elseif ($this->default_price_from) {
            return 'From $' . number_format($this->default_price_from, 2);
        } elseif ($this->default_price_to) {
            return 'Up to $' . number_format($this->default_price_to, 2);
        }
        return 'Price varies';
    }

    /**
     * Get the formatted duration.
     */
    public function getFormattedDurationAttribute(): string
    {
        if (!$this->default_duration_minutes) {
            return 'Duration varies';
        }

        $hours = floor($this->default_duration_minutes / 60);
        $minutes = $this->default_duration_minutes % 60;

        if ($hours > 0 && $minutes > 0) {
            return "{$hours}h {$minutes}m";
        } elseif ($hours > 0) {
            return "{$hours}h";
        } else {
            return "{$minutes}m";
        }
    }
}
