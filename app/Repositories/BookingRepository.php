<?php

namespace App\Repositories;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class BookingRepository extends BaseRepository
{
    public function __construct(Booking $model)
    {
        $this->model = $model;
    }

    /**
     * Get bookings for a customer.
     */
    public function getByCustomer(int $customerId, ?string $status = null): Collection
    {
        $query = $this->model->where('customer_id', $customerId)
            ->with(['providerProfile.user', 'providerService.service', 'timeSlot', 'rating']);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->orderBy('scheduled_at', 'desc')->get();
    }

    /**
     * Get paginated bookings for a customer.
     */
    public function getPaginatedByCustomer(int $customerId, ?string $status = null, int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->model->where('customer_id', $customerId)
            ->with(['providerProfile.user', 'providerService.service', 'timeSlot', 'rating']);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->orderBy('scheduled_at', 'desc')->paginate($perPage);
    }

    /**
     * Get bookings for a provider.
     */
    public function getByProvider(int $providerProfileId, ?string $status = null): Collection
    {
        $query = $this->model->where('provider_profile_id', $providerProfileId)
            ->with(['customer', 'providerService.service', 'timeSlot']);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->orderBy('scheduled_at', 'desc')->get();
    }

    /**
     * Get paginated bookings for a provider.
     */
    public function getPaginatedByProvider(int $providerProfileId, ?string $status = null, int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->model->where('provider_profile_id', $providerProfileId)
            ->with(['customer', 'providerService.service', 'timeSlot']);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->orderBy('scheduled_at', 'desc')->paginate($perPage);
    }

    /**
     * Get pending bookings for a provider.
     */
    public function getPendingByProvider(int $providerProfileId): Collection
    {
        return $this->getByProvider($providerProfileId, 'pending');
    }

    /**
     * Get booking with all details.
     */
    public function getWithDetails(int $id): ?Booking
    {
        return $this->model->with([
            'customer.customerProfile',
            'providerProfile.user',
            'providerService.service.category',
            'timeSlot',
            'rating'
        ])->find($id);
    }

    /**
     * Update booking status.
     */
    public function updateStatus(int $id, string $status, array $additionalData = []): Booking
    {
        $booking = $this->findOrFail($id);
        $booking->status = $status;

        foreach ($additionalData as $key => $value) {
            $booking->{$key} = $value;
        }

        $booking->save();
        return $booking;
    }

    /**
     * Get statistics.
     */
    public function getStatistics(): array
    {
        return [
            'total' => $this->model->count(),
            'pending' => $this->model->where('status', 'pending')->count(),
            'confirmed' => $this->model->where('status', 'confirmed')->count(),
            'completed' => $this->model->where('status', 'completed')->count(),
            'cancelled' => $this->model->where('status', 'cancelled')->count(),
            'rejected' => $this->model->where('status', 'rejected')->count(),
        ];
    }

    /**
     * Get provider statistics.
     */
    public function getProviderStatistics(int $providerProfileId): array
    {
        $query = $this->model->where('provider_profile_id', $providerProfileId);

        return [
            'total' => $query->count(),
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'confirmed' => (clone $query)->where('status', 'confirmed')->count(),
            'completed' => (clone $query)->where('status', 'completed')->count(),
            'cancelled' => (clone $query)->where('status', 'cancelled')->count(),
            'rejected' => (clone $query)->where('status', 'rejected')->count(),
            'total_revenue' => (clone $query)->where('status', 'completed')->sum('total_price'),
        ];
    }

    /**
     * Check for conflicting bookings.
     */
    public function hasConflict(int $providerProfileId, int $timeSlotId, ?int $excludeId = null): bool
    {
        $query = $this->model->where('provider_profile_id', $providerProfileId)
            ->where('time_slot_id', $timeSlotId)
            ->whereIn('status', ['pending', 'confirmed']);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
