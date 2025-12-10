<?php

namespace App\Repositories;

use App\Models\ServiceCategory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ServiceCategoryRepository extends BaseRepository
{
    public function __construct(ServiceCategory $model)
    {
        $this->model = $model;
    }

    /**
     * Get all active categories.
     */
    public function getActive(): Collection
    {
        return $this->model->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get categories with service counts.
     */
    public function getWithServiceCounts(): Collection
    {
        return $this->model->withCount(['services' => function ($q) {
            $q->where('is_active', true);
        }])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get paginated categories for admin.
     */
    public function getPaginatedForAdmin(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->withCount('services')
            ->orderBy('name')
            ->paginate($perPage);
    }

    /**
     * Get category with services.
     */
    public function getWithServices(int $id): ?ServiceCategory
    {
        return $this->model->with(['services' => function ($q) {
            $q->where('is_active', true);
        }])->find($id);
    }

    /**
     * Toggle active status.
     */
    public function toggleActive(int $id): ServiceCategory
    {
        $category = $this->findOrFail($id);
        $category->is_active = !$category->is_active;
        $category->save();
        return $category;
    }
}
