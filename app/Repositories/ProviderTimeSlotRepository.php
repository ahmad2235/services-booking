<?php

namespace App\Repositories;

use App\Models\ProviderTimeSlot;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProviderTimeSlotRepository extends BaseRepository
{
    public function __construct(ProviderTimeSlot $model)
    {
        $this->model = $model;
    }

    /**
     * Get time slots by provider.
     */
    public function getByProvider(int $providerProfileId): Collection
    {
        return $this->model->where('provider_profile_id', $providerProfileId)
            ->orderBy('start_datetime')
            ->get();
    }

    /**
     * Get future time slots by provider.
     */
    public function getFutureByProvider(int $providerProfileId): Collection
    {
        return $this->model->where('provider_profile_id', $providerProfileId)
            ->where('start_datetime', '>', Carbon::now())
            ->orderBy('start_datetime')
            ->get();
    }

    /**
     * Get available future time slots by provider.
     */
    public function getAvailableFutureByProvider(int $providerProfileId): Collection
    {
        return $this->model->where('provider_profile_id', $providerProfileId)
            ->where('status', 'available')
            ->where('start_datetime', '>', Carbon::now())
            ->orderBy('start_datetime')
            ->get();
    }

    /**
     * Get paginated time slots for provider.
     */
    public function getPaginatedByProvider(int $providerProfileId, ?string $status = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->where('provider_profile_id', $providerProfileId);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->orderBy('start_datetime', 'desc')->paginate($perPage);
    }

    /**
     * Check for overlapping slots.
     */
    public function hasOverlap(int $providerProfileId, Carbon $start, Carbon $end, ?int $excludeId = null): bool
    {
        return ProviderTimeSlot::hasOverlap($providerProfileId, $start, $end, $excludeId);
    }

    /**
     * Update slot status.
     */
    public function updateStatus(int $id, string $status): ProviderTimeSlot
    {
        $slot = $this->findOrFail($id);
        $slot->status = $status;
        $slot->save();
        return $slot;
    }

    /**
     * Get slots for a date range.
     */
    public function getForDateRange(int $providerProfileId, Carbon $startDate, Carbon $endDate): Collection
    {
        return $this->model->where('provider_profile_id', $providerProfileId)
            ->where('start_datetime', '>=', $startDate)
            ->where('start_datetime', '<=', $endDate)
            ->orderBy('start_datetime')
            ->get();
    }

    /**
     * Get slots grouped by date for calendar view.
     */
    public function getGroupedByDate(int $providerProfileId, Carbon $startDate, Carbon $endDate): Collection
    {
        return $this->getForDateRange($providerProfileId, $startDate, $endDate)
            ->groupBy(function ($slot) {
                return $slot->start_datetime->format('Y-m-d');
            });
    }
}
