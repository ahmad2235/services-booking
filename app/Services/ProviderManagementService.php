<?php

namespace App\Services;

use App\Models\ProviderProfile;
use App\Models\ProviderService;
use App\Models\ProviderTimeSlot;
use App\Repositories\ProviderProfileRepository;
use App\Repositories\ProviderServiceRepository;
use App\Repositories\ProviderTimeSlotRepository;
use App\Repositories\LocationRepository;
use App\Repositories\ServiceRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class ProviderManagementService
{
    public function __construct(
        protected ProviderProfileRepository $providerProfileRepository,
        protected ProviderServiceRepository $providerServiceRepository,
        protected ProviderTimeSlotRepository $timeSlotRepository,
        protected LocationRepository $locationRepository,
        protected ServiceRepository $serviceRepository
    ) {}

    /**
     * Update provider profile.
     */
    public function updateProfile(int $providerProfileId, array $data): ProviderProfile
    {
        $this->providerProfileRepository->update($providerProfileId, $data);
        return $this->providerProfileRepository->findOrFail($providerProfileId);
    }

    /**
     * Update provider covered locations.
     */
    public function updateLocations(int $providerProfileId, array $locationIds): void
    {
        $this->providerProfileRepository->updateLocations($providerProfileId, $locationIds);
    }

    /**
     * Add a service to provider's offerings.
     */
    public function addService(int $providerProfileId, array $data): ProviderService
    {
        // Check if already offering this service
        $existing = $this->providerServiceRepository->findByProviderAndService(
            $providerProfileId,
            $data['service_id']
        );
        
        if ($existing) {
            throw new Exception('You are already offering this service.');
        }
        
        return $this->providerServiceRepository->create([
            'provider_profile_id' => $providerProfileId,
            'service_id' => $data['service_id'],
            'price' => $data['price'],
            'description' => $data['description'] ?? null,
            'estimated_duration_minutes' => $data['estimated_duration_minutes'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    /**
     * Update a provider service.
     */
    public function updateService(int $providerServiceId, array $data): ProviderService
    {
        $this->providerServiceRepository->update($providerServiceId, $data);
        return $this->providerServiceRepository->findOrFail($providerServiceId);
    }

    /**
     * Toggle provider service active status.
     */
    public function toggleServiceActive(int $providerServiceId): ProviderService
    {
        return $this->providerServiceRepository->toggleActive($providerServiceId);
    }

    /**
     * Delete a provider service.
     */
    public function deleteService(int $providerServiceId): bool
    {
        return $this->providerServiceRepository->delete($providerServiceId);
    }

    /**
     * Create a time slot.
     */
    public function createTimeSlot(int $providerProfileId, array $data): ProviderTimeSlot
    {
        $startDatetime = Carbon::parse($data['start_datetime']);
        $endDatetime = Carbon::parse($data['end_datetime']);
        
        // Validate times
        if ($startDatetime >= $endDatetime) {
            throw new Exception('End time must be after start time.');
        }
        
        if ($startDatetime < Carbon::now()) {
            throw new Exception('Cannot create time slots in the past.');
        }
        
        // Check for overlaps
        if ($this->timeSlotRepository->hasOverlap($providerProfileId, $startDatetime, $endDatetime)) {
            throw new Exception('This time slot overlaps with an existing one.');
        }
        
        return $this->timeSlotRepository->create([
            'provider_profile_id' => $providerProfileId,
            'start_datetime' => $startDatetime,
            'end_datetime' => $endDatetime,
            'status' => $data['status'] ?? ProviderTimeSlot::STATUS_AVAILABLE,
        ]);
    }

    /**
     * Update a time slot.
     */
    public function updateTimeSlot(int $timeSlotId, array $data): ProviderTimeSlot
    {
        $slot = $this->timeSlotRepository->findOrFail($timeSlotId);
        
        // Don't allow updating reserved slots
        if ($slot->isReserved()) {
            throw new Exception('Cannot modify a reserved time slot.');
        }
        
        $startDatetime = isset($data['start_datetime']) ? Carbon::parse($data['start_datetime']) : $slot->start_datetime;
        $endDatetime = isset($data['end_datetime']) ? Carbon::parse($data['end_datetime']) : $slot->end_datetime;
        
        // Validate times
        if ($startDatetime >= $endDatetime) {
            throw new Exception('End time must be after start time.');
        }
        
        // Check for overlaps (excluding current slot)
        if ($this->timeSlotRepository->hasOverlap($slot->provider_profile_id, $startDatetime, $endDatetime, $timeSlotId)) {
            throw new Exception('This time slot overlaps with an existing one.');
        }
        
        $this->timeSlotRepository->update($timeSlotId, [
            'start_datetime' => $startDatetime,
            'end_datetime' => $endDatetime,
            'status' => $data['status'] ?? $slot->status,
        ]);
        
        return $this->timeSlotRepository->findOrFail($timeSlotId);
    }

    /**
     * Delete a time slot.
     */
    public function deleteTimeSlot(int $timeSlotId): bool
    {
        $slot = $this->timeSlotRepository->findOrFail($timeSlotId);
        
        // Don't allow deleting reserved slots
        if ($slot->isReserved()) {
            throw new Exception('Cannot delete a reserved time slot.');
        }
        
        return $this->timeSlotRepository->delete($timeSlotId);
    }

    /**
     * Block a time slot.
     */
    public function blockTimeSlot(int $timeSlotId): ProviderTimeSlot
    {
        $slot = $this->timeSlotRepository->findOrFail($timeSlotId);
        
        if ($slot->isReserved()) {
            throw new Exception('Cannot block a reserved time slot.');
        }
        
        return $this->timeSlotRepository->updateStatus($timeSlotId, ProviderTimeSlot::STATUS_BLOCKED);
    }

    /**
     * Unblock a time slot.
     */
    public function unblockTimeSlot(int $timeSlotId): ProviderTimeSlot
    {
        $slot = $this->timeSlotRepository->findOrFail($timeSlotId);
        
        if (!$slot->isBlocked()) {
            throw new Exception('This time slot is not blocked.');
        }
        
        return $this->timeSlotRepository->updateStatus($timeSlotId, ProviderTimeSlot::STATUS_AVAILABLE);
    }

    /**
     * Get available services not offered by provider.
     */
    public function getAvailableServices(int $providerProfileId)
    {
        return $this->serviceRepository->getNotOfferedByProvider($providerProfileId);
    }

    /**
     * Get provider dashboard statistics.
     */
    public function getDashboardStats(int $providerProfileId): array
    {
        $profile = $this->providerProfileRepository->find($providerProfileId);
        
        return [
            'avg_rating' => $profile->avg_rating,
            'total_reviews' => $profile->total_reviews,
            'active_services' => $this->providerServiceRepository->getActiveByProvider($providerProfileId)->count(),
            'available_slots' => $this->timeSlotRepository->getAvailableFutureByProvider($providerProfileId)->count(),
        ];
    }
}
