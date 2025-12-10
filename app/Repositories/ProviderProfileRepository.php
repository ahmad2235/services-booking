<?php

namespace App\Repositories;

use App\Models\ProviderProfile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProviderProfileRepository extends BaseRepository
{
    public function __construct(ProviderProfile $model)
    {
        $this->model = $model;
    }

    /**
     * Find profile by user ID.
     */
    public function findByUserId(int $userId): ?ProviderProfile
    {
        return $this->model->where('user_id', $userId)->first();
    }

    /**
     * Get providers with filters.
     */
    public function getWithFilters(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        $query = $this->model->query()
            ->with(['user', 'locations', 'providerServices.service.category'])
            ->whereHas('user', function ($q) {
                $q->where('is_active', true);
            });

        // Filter by location
        if (!empty($filters['city']) || !empty($filters['area'])) {
            $query->whereHas('locations', function ($q) use ($filters) {
                if (!empty($filters['city'])) {
                    $q->where('city', $filters['city']);
                }
                if (!empty($filters['area'])) {
                    $q->where('area', $filters['area']);
                }
            });
        }

        // Filter by service
        if (!empty($filters['service_id'])) {
            $query->whereHas('providerServices', function ($q) use ($filters) {
                $q->where('service_id', $filters['service_id'])
                  ->where('is_active', true);
            });
        }

        // Filter by category
        if (!empty($filters['category_id'])) {
            $query->whereHas('providerServices.service', function ($q) use ($filters) {
                $q->where('category_id', $filters['category_id'])
                  ->where('is_active', true);
            });
        }

        // Filter by price range (treat provider's min/max as a range and the user's min/max as a range).
        // We'll include providers whose ranges overlap the user-provided range. Null min/max on provider
        // are treated as unbounded and therefore match.
        $userMin = $filters['min_price'] ?? null;
        $userMax = $filters['max_price'] ?? null;

        if ($userMin !== null || $userMax !== null) {
            $query->where(function ($q) use ($userMin, $userMax) {
                // If a provider has no min or max price set we consider them a match
                $q->whereNull('min_price')->orWhereNull('max_price');

                // If both user bounds exist, match range-overlap
                if ($userMin !== null && $userMax !== null) {
                    $q->orWhere(function ($q2) use ($userMin, $userMax) {
                        $q2->where('min_price', '<=', $userMax)
                           ->where('max_price', '>=', $userMin);
                    });
                } elseif ($userMin !== null) {
                    // Only minimum provided: match providers whose max_price is >= userMin (or null)
                    $q->orWhere('max_price', '>=', $userMin);
                } elseif ($userMax !== null) {
                    // Only maximum provided: match providers whose min_price is <= userMax (or null)
                    $q->orWhere('min_price', '<=', $userMax);
                }
            });
        }

        // Free-text search across provider company name, provider user name and service name
        if (!empty($filters['search'])) {
            $term = '%' . trim($filters['search']) . '%';
            $query->where(function ($q) use ($term) {
                $q->whereHas('user', function ($u) use ($term) {
                    $u->where('name', 'like', $term);
                })
                ->orWhere('company_name', 'like', $term)
                ->orWhereHas('providerServices.service', function ($s) use ($term) {
                    $s->where('name', 'like', $term);
                });
            });
        }

        // Filter by minimum rating
        if (!empty($filters['min_rating'])) {
            $query->where('avg_rating', '>=', $filters['min_rating']);
        }

        // Sort
        $sortBy = $filters['sort_by'] ?? 'avg_rating';
        $sortDir = $filters['sort_dir'] ?? 'desc';
        
        if ($sortBy === 'price') {
            $query->orderBy('min_price', $sortDir);
        } elseif ($sortBy === 'rating') {
            $query->orderBy('avg_rating', $sortDir);
        } elseif ($sortBy === 'reviews') {
            $query->orderBy('total_reviews', $sortDir);
        } else {
            $query->orderBy('avg_rating', 'desc');
        }

        return $query->paginate($perPage);
    }

    /**
     * Get featured providers.
     */
    public function getFeatured(int $limit = 6): Collection
    {
        return $this->model->query()
            ->with(['user', 'providerServices.service.category'])
            ->whereHas('user', function ($q) {
                $q->where('is_active', true);
            })
            ->where('avg_rating', '>=', 4.0)
            ->orderBy('avg_rating', 'desc')
            ->orderBy('total_reviews', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get provider with all related data.
     */
    public function getWithDetails(int $id): ?ProviderProfile
    {
        return $this->model->with([
            'user',
            'locations',
            'providerServices.service.category',
            'timeSlots' => function ($q) {
                $q->where('status', 'available')
                  ->where('start_datetime', '>', now())
                  ->orderBy('start_datetime');
            },
            'visibleRatings.booking.customer',
        ])->find($id);
    }

    /**
     * Update provider locations.
     */
    public function updateLocations(int $id, array $locationIds): void
    {
        $provider = $this->findOrFail($id);
        $provider->locations()->sync($locationIds);
    }
}
