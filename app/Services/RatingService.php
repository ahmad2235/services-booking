<?php

namespace App\Services;

use App\Models\Rating;
use App\Models\Booking;
use App\Repositories\RatingRepository;
use App\Repositories\BookingRepository;
use App\Repositories\NotificationRepository;
use App\Models\Notification;
use Exception;

class RatingService
{
    public function __construct(
        protected RatingRepository $ratingRepository,
        protected BookingRepository $bookingRepository,
        protected NotificationRepository $notificationRepository
    ) {}

    /**
     * Create a rating for a completed booking.
     */
    public function createRating(int $bookingId, int $customerId, int $ratingValue, ?string $comment = null): Rating
    {
        $booking = $this->bookingRepository->findOrFail($bookingId);
        
        // Validate ownership
        if ($booking->customer_id !== $customerId) {
            throw new Exception('You are not authorized to rate this booking.');
        }
        
        // Validate booking status
        if (!$booking->isCompleted()) {
            throw new Exception('Only completed bookings can be rated.');
        }
        
        // Check if already rated
        if ($this->ratingRepository->findByBooking($bookingId)) {
            throw new Exception('This booking has already been rated.');
        }
        
        // Validate rating value
        $minRating = config('booking.rating.min', 1);
        $maxRating = config('booking.rating.max', 5);
        
        if ($ratingValue < $minRating || $ratingValue > $maxRating) {
            throw new Exception("Rating must be between {$minRating} and {$maxRating}.");
        }
        
        // Create rating
        $rating = $this->ratingRepository->create([
            'booking_id' => $bookingId,
            'provider_profile_id' => $booking->provider_profile_id,
            'rating_value' => $ratingValue,
            'comment' => $comment,
            'is_visible' => true,
        ]);
        
        // Notify provider
        $this->notificationRepository->createNotification(
            $booking->providerProfile->user_id,
            Notification::TYPE_RATING_RECEIVED,
            [
                'rating_id' => $rating->id,
                'rating_value' => $ratingValue,
                'customer_name' => $booking->customer->name,
                'service_name' => $booking->providerService->service->name,
            ]
        );
        
        return $rating->load('booking.customer');
    }

    /**
     * Toggle rating visibility (admin action).
     */
    public function toggleVisibility(int $ratingId, int $adminId): Rating
    {
        return $this->ratingRepository->toggleVisibility($ratingId, $adminId);
    }

    /**
     * Get ratings for a provider.
     */
    public function getProviderRatings(int $providerProfileId, int $perPage = 10)
    {
        return $this->ratingRepository->getPaginatedVisibleByProvider($providerProfileId, $perPage);
    }

    /**
     * Get rating distribution for a provider.
     */
    public function getRatingDistribution(int $providerProfileId): array
    {
        return $this->ratingRepository->getDistributionForProvider($providerProfileId);
    }

    /**
     * Get all ratings for admin.
     */
    public function getAllRatingsForAdmin(int $perPage = 15)
    {
        return $this->ratingRepository->getAllForAdmin($perPage);
    }

    /**
     * Get rating statistics.
     */
    public function getStatistics(): array
    {
        return $this->ratingRepository->getStatistics();
    }
}
