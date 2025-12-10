<?php

namespace App\Repositories;

use App\Models\ProviderService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProviderServiceRepository extends BaseRepository
{
    public function __construct(ProviderService $model)
    {
        $this->model = $model;
    }

    /**
     * Get services by provider.
     */
    public function getByProvider(int $providerProfileId): Collection
    {
        return $this->model->where('provider_profile_id', $providerProfileId)
            ->with('service.category')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get active services by provider.
     */
    public function getActiveByProvider(int $providerProfileId): Collection
    {
        return $this->model->where('provider_profile_id', $providerProfileId)
            ->where('is_active', true)
            ->with('service.category')
            ->get();
    }

    /**
     * Find by provider and service.
     */
    public function findByProviderAndService(int $providerProfileId, int $serviceId): ?ProviderService
    {
        return $this->model->where('provider_profile_id', $providerProfileId)
            ->where('service_id', $serviceId)
            ->first();
    }

    /**
     * Toggle active status.
     */
    public function toggleActive(int $id): ProviderService
    {
        $providerService = $this->findOrFail($id);
        $providerService->is_active = !$providerService->is_active;
        $providerService->save();
        return $providerService;
    }

    /**
     * Get paginated services for a provider.
     */
    public function getPaginatedByProvider(int $providerProfileId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->where('provider_profile_id', $providerProfileId)
            ->with('service.category')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get provider service with details.
     */
    public function getWithDetails(int $id): ?ProviderService
    {
        return $this->model->with([
            'service.category',
            'providerProfile.user',
            'providerProfile.locations',
            'providerProfile.timeSlots' => function ($q) {
                $q->where('status', 'available')
                  ->where('start_datetime', '>', now())
                  ->orderBy('start_datetime');
            }
        ])->find($id);
    }
}
