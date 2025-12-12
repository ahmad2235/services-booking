<?php

namespace App\Repositories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ServiceRepository extends BaseRepository
{
    public function __construct(Service $model)
    {
        $this->model = $model;
    }

    /**
     * Get all active services.
     */
    public function getActive(): Collection
    {
        return $this->model->where('is_active', true)
            ->with('category')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get services by category.
     */
    public function getByCategory(int $categoryId): Collection
    {
        return $this->model->where('category_id', $categoryId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get active services with category.
     */
    public function getActiveWithCategory(): Collection
    {
        return $this->model->where('is_active', true)
            ->with('category')
            ->whereHas('category', function ($q) {
                $q->where('is_active', true);
            })
            ->orderBy('name')
            ->get();
    }

    /**
     * Get paginated services for admin.
     */
    public function getPaginatedForAdmin(?int $categoryId = null, ?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with('category')
            ->withCount('providerServices');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('name')->paginate($perPage);
    }

    /**
     * Search services.
     */
    public function search(string $term): Collection
    {
        return $this->model->where('is_active', true)
            ->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('description', 'like', "%{$term}%");
            })
            ->with('category')
            ->limit(10)
            ->get();
    }

    /**
     * Toggle active status.
     */
    public function toggleActive(int $id): Service
    {
        $service = $this->findOrFail($id);
        $service->is_active = !$service->is_active;
        $service->save();
        return $service;
    }

    /**
     * Get services not offered by a provider.
     */
    public function getNotOfferedByProvider(int $providerProfileId): Collection
    {
        return $this->model->where('is_active', true)
            ->whereDoesntHave('providerServices', function ($q) use ($providerProfileId) {
                $q->where('provider_profile_id', $providerProfileId);
            })
            ->with('category')
            ->orderBy('name')
            ->get();
    }
}
