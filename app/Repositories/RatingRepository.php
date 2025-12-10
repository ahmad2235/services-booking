<?php

namespace App\Repositories;

use App\Models\Rating;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class RatingRepository extends BaseRepository
{
    public function __construct(Rating $model)
    {
        $this->model = $model;
    }

    /**
     * Get visible ratings for a provider.
     */
    public function getVisibleByProvider(int $providerProfileId): Collection
    {
        return $this->model->where('provider_profile_id', $providerProfileId)
            ->where('is_visible', true)
            ->with('booking.customer')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get paginated visible ratings for a provider.
     */
    public function getPaginatedVisibleByProvider(int $providerProfileId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->where('provider_profile_id', $providerProfileId)
            ->where('is_visible', true)
            ->with('booking.customer')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get all ratings for admin.
     */
    public function getAllForAdmin(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with([
            'booking.customer',
            'providerProfile.user',
            'hiddenByAdmin'
        ])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Find rating by booking.
     */
    public function findByBooking(int $bookingId): ?Rating
    {
        return $this->model->where('booking_id', $bookingId)->first();
    }

    /**
     * Toggle visibility.
     */
    public function toggleVisibility(int $id, int $adminId): Rating
    {
        $rating = $this->findOrFail($id);
        $rating->is_visible = !$rating->is_visible;

        if (!$rating->is_visible) {
            $rating->hidden_by_admin_id = $adminId;
            $rating->hidden_at = now();
        } else {
            $rating->hidden_by_admin_id = null;
            $rating->hidden_at = null;
        }

        $rating->save();
        return $rating;
    }

    /**
     * Get average rating for provider.
     */
    public function getAverageForProvider(int $providerProfileId): float
    {
        return $this->model->where('provider_profile_id', $providerProfileId)
            ->where('is_visible', true)
            ->avg('rating_value') ?? 0.0;
    }

    /**
     * Get rating statistics.
     */
    public function getStatistics(): array
    {
        return [
            'total' => $this->model->count(),
            'visible' => $this->model->where('is_visible', true)->count(),
            'hidden' => $this->model->where('is_visible', false)->count(),
            'average' => round($this->model->where('is_visible', true)->avg('rating_value') ?? 0, 2),
        ];
    }

    /**
     * Get rating distribution for a provider.
     */
    public function getDistributionForProvider(int $providerProfileId): array
    {
        $distribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $distribution[$i] = $this->model
                ->where('provider_profile_id', $providerProfileId)
                ->where('is_visible', true)
                ->where('rating_value', $i)
                ->count();
        }
        return $distribution;
    }
}
