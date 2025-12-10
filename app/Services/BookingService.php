<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\ProviderTimeSlot;
use App\Repositories\BookingRepository;
use App\Repositories\ProviderTimeSlotRepository;
use App\Repositories\ProviderServiceRepository;
use App\Repositories\NotificationRepository;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;

class BookingService
{
    public function __construct(
        protected BookingRepository $bookingRepository,
        protected ProviderTimeSlotRepository $timeSlotRepository,
        protected ProviderServiceRepository $providerServiceRepository,
        protected NotificationRepository $notificationRepository
    ) {}

    /**
     * Create a new booking.
     */
    public function createBooking(array $data): Booking
    {
        return DB::transaction(function () use ($data) {
            // Get the provider service
            $providerService = $this->providerServiceRepository->findOrFail($data['provider_service_id']);
            
            // Get the time slot
            $timeSlot = $this->timeSlotRepository->findOrFail($data['time_slot_id']);
            
            // Validate time slot is available
            if (!$timeSlot->isAvailable()) {
                throw new Exception('The selected time slot is not available.');
            }
            
            // Check for conflicts
            if ($this->bookingRepository->hasConflict($providerService->provider_profile_id, $timeSlot->id)) {
                throw new Exception('This time slot already has a booking.');
            }
            
            // Create booking
            $booking = $this->bookingRepository->create([
                'customer_id' => $data['customer_id'],
                'provider_profile_id' => $providerService->provider_profile_id,
                'provider_service_id' => $providerService->id,
                'time_slot_id' => $timeSlot->id,
                'scheduled_at' => $timeSlot->start_datetime,
                'duration_minutes' => $providerService->estimated_duration_minutes ?? $timeSlot->duration_minutes,
                'total_price' => $providerService->price,
                'status' => Booking::STATUS_PENDING,
                'customer_note' => $data['customer_note'] ?? null,
            ]);
            
            // Notify provider
            $this->notificationRepository->createNotification(
                $providerService->providerProfile->user_id,
                Notification::TYPE_BOOKING_CREATED,
                [
                    'booking_id' => $booking->id,
                    'customer_name' => $booking->customer->name,
                    'service_name' => $providerService->service->name,
                    'scheduled_at' => $timeSlot->start_datetime->toDateTimeString(),
                ]
            );
            
            return $booking->load(['providerProfile.user', 'providerService.service', 'timeSlot']);
        });
    }

    /**
     * Confirm a booking (provider action).
     */
    public function confirmBooking(int $bookingId, ?string $providerNote = null): Booking
    {
        return DB::transaction(function () use ($bookingId, $providerNote) {
            $booking = $this->bookingRepository->findOrFail($bookingId);
            
            if (!$booking->isPending()) {
                throw new Exception('Only pending bookings can be confirmed.');
            }
            
            // Update time slot status to reserved
            if ($booking->time_slot_id) {
                $this->timeSlotRepository->updateStatus($booking->time_slot_id, ProviderTimeSlot::STATUS_RESERVED);
            }
            
            // Update booking
            $booking = $this->bookingRepository->updateStatus($bookingId, Booking::STATUS_CONFIRMED, [
                'provider_note' => $providerNote,
            ]);
            
            // Notify customer
            $this->notificationRepository->createNotification(
                $booking->customer_id,
                Notification::TYPE_BOOKING_CONFIRMED,
                [
                    'booking_id' => $booking->id,
                    'provider_name' => $booking->providerProfile->user->name,
                    'scheduled_at' => $booking->scheduled_at->toDateTimeString(),
                ]
            );
            
            return $booking;
        });
    }

    /**
     * Reject a booking (provider action).
     */
    public function rejectBooking(int $bookingId, ?string $rejectReason = null): Booking
    {
        return DB::transaction(function () use ($bookingId, $rejectReason) {
            $booking = $this->bookingRepository->findOrFail($bookingId);
            
            if (!$booking->isPending()) {
                throw new Exception('Only pending bookings can be rejected.');
            }
            
            // Free up the time slot if it was reserved
            if ($booking->time_slot_id) {
                $timeSlot = $this->timeSlotRepository->find($booking->time_slot_id);
                if ($timeSlot && $timeSlot->isReserved()) {
                    $this->timeSlotRepository->updateStatus($booking->time_slot_id, ProviderTimeSlot::STATUS_AVAILABLE);
                }
            }
            
            // Update booking
            $booking = $this->bookingRepository->updateStatus($bookingId, Booking::STATUS_REJECTED, [
                'reject_reason' => $rejectReason,
            ]);
            
            // Notify customer
            $this->notificationRepository->createNotification(
                $booking->customer_id,
                Notification::TYPE_BOOKING_REJECTED,
                [
                    'booking_id' => $booking->id,
                    'provider_name' => $booking->providerProfile->user->name,
                    'reject_reason' => $rejectReason,
                ]
            );
            
            return $booking;
        });
    }

    /**
     * Cancel a booking (customer action).
     */
    public function cancelBooking(int $bookingId, ?string $cancelReason = null): Booking
    {
        return DB::transaction(function () use ($bookingId, $cancelReason) {
            $booking = $this->bookingRepository->findOrFail($bookingId);
            
            if (!$booking->canBeCancelled()) {
                throw new Exception('This booking cannot be cancelled.');
            }
            
            // Free up the time slot
            if ($booking->time_slot_id) {
                $this->timeSlotRepository->updateStatus($booking->time_slot_id, ProviderTimeSlot::STATUS_AVAILABLE);
            }
            
            // Update booking
            $booking = $this->bookingRepository->updateStatus($bookingId, Booking::STATUS_CANCELLED, [
                'cancel_reason' => $cancelReason,
                'cancelled_at' => Carbon::now(),
            ]);
            
            // Notify provider
            $this->notificationRepository->createNotification(
                $booking->providerProfile->user_id,
                Notification::TYPE_BOOKING_CANCELLED,
                [
                    'booking_id' => $booking->id,
                    'customer_name' => $booking->customer->name,
                    'cancel_reason' => $cancelReason,
                ]
            );
            
            return $booking;
        });
    }

    /**
     * Mark a booking as completed (provider action).
     */
    public function completeBooking(int $bookingId): Booking
    {
        return DB::transaction(function () use ($bookingId) {
            $booking = $this->bookingRepository->findOrFail($bookingId);
            
            if (!$booking->isConfirmed()) {
                throw new Exception('Only confirmed bookings can be marked as completed.');
            }
            
            // Update booking
            $booking = $this->bookingRepository->updateStatus($bookingId, Booking::STATUS_COMPLETED, [
                'completed_at' => Carbon::now(),
            ]);
            
            // Notify customer
            $this->notificationRepository->createNotification(
                $booking->customer_id,
                Notification::TYPE_BOOKING_COMPLETED,
                [
                    'booking_id' => $booking->id,
                    'provider_name' => $booking->providerProfile->user->name,
                    'service_name' => $booking->providerService->service->name,
                ]
            );
            
            return $booking;
        });
    }

    /**
     * Get booking for customer.
     */
    public function getCustomerBooking(int $bookingId, int $customerId): ?Booking
    {
        $booking = $this->bookingRepository->getWithDetails($bookingId);
        
        if ($booking && $booking->customer_id === $customerId) {
            return $booking;
        }
        
        return null;
    }

    /**
     * Get booking for provider.
     */
    public function getProviderBooking(int $bookingId, int $providerProfileId): ?Booking
    {
        $booking = $this->bookingRepository->getWithDetails($bookingId);
        
        if ($booking && $booking->provider_profile_id === $providerProfileId) {
            return $booking;
        }
        
        return null;
    }
}
