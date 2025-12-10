<?php

namespace App\Services;

use App\Models\User;
use App\Models\AdminAction;
use App\Repositories\UserRepository;
use App\Repositories\BookingRepository;
use App\Repositories\RatingRepository;
use App\Repositories\ServiceCategoryRepository;
use App\Repositories\ServiceRepository;
use App\Repositories\AdminActionRepository;
use App\Repositories\NotificationRepository;
use App\Models\Notification;

class AdminService
{
    public function __construct(
        protected UserRepository $userRepository,
        protected BookingRepository $bookingRepository,
        protected RatingRepository $ratingRepository,
        protected ServiceCategoryRepository $categoryRepository,
        protected ServiceRepository $serviceRepository,
        protected AdminActionRepository $adminActionRepository,
        protected NotificationRepository $notificationRepository
    ) {}

    /**
     * Get dashboard statistics.
     */
    public function getDashboardStats(): array
    {
        $userStats = $this->userRepository->getStatistics();
        $bookingStats = $this->bookingRepository->getStatistics();
        $ratingStats = $this->ratingRepository->getStatistics();

        return [
            'total_users' => $userStats['total'] ?? 0,
            'total_providers' => $userStats['providers'] ?? 0,
            'total_customers' => $userStats['customers'] ?? 0,
            'total_bookings' => $bookingStats['total'] ?? 0,
            'total_categories' => $this->categoryRepository->query()->count(),
            'total_services' => $this->serviceRepository->query()->count(),
            'total_ratings' => $ratingStats['total'] ?? 0,
            // keep the nested breakdown available for other consumers
            'users' => $userStats,
            'bookings' => $bookingStats,
            'ratings' => $ratingStats,
        ];
    }

    /**
     * Toggle user active status.
     */
    public function toggleUserActive(int $userId, int $adminId): User
    {
        $user = $this->userRepository->toggleActive($userId);
        
        // Log the action
        $this->adminActionRepository->log(
            $adminId,
            $user->is_active ? AdminAction::ACTION_USER_ACTIVATED : AdminAction::ACTION_USER_DEACTIVATED,
            AdminAction::TARGET_USER,
            $userId,
            "User {$user->name} ({$user->email}) " . ($user->is_active ? 'activated' : 'deactivated')
        );
        
        // Notify user
        $this->notificationRepository->createNotification(
            $userId,
            $user->is_active ? Notification::TYPE_ACCOUNT_ACTIVATED : Notification::TYPE_ACCOUNT_DEACTIVATED,
            []
        );
        
        return $user;
    }

    /**
     * Get paginated users with filters.
     */
    public function getUsers(?string $role = null, ?bool $isActive = null, int $perPage = 15)
    {
        return $this->userRepository->getByRole($role, $isActive, $perPage);
    }

    /**
     * Toggle rating visibility.
     */
    public function toggleRatingVisibility(int $ratingId, int $adminId)
    {
        $rating = $this->ratingRepository->toggleVisibility($ratingId, $adminId);
        
        // Log the action
        $this->adminActionRepository->log(
            $adminId,
            $rating->is_visible ? AdminAction::ACTION_RATING_SHOWN : AdminAction::ACTION_RATING_HIDDEN,
            AdminAction::TARGET_RATING,
            $ratingId,
            "Rating ID {$ratingId} " . ($rating->is_visible ? 'made visible' : 'hidden')
        );
        
        return $rating;
    }

    /**
     * Create service category.
     */
    public function createCategory(array $data, int $adminId)
    {
        $category = $this->categoryRepository->create($data);
        
        $this->adminActionRepository->log(
            $adminId,
            AdminAction::ACTION_CATEGORY_CREATED,
            AdminAction::TARGET_CATEGORY,
            $category->id,
            "Created category: {$category->name}"
        );
        
        return $category;
    }

    /**
     * Update service category.
     */
    public function updateCategory(int $categoryId, array $data, int $adminId)
    {
        $this->categoryRepository->update($categoryId, $data);
        $category = $this->categoryRepository->find($categoryId);
        
        $this->adminActionRepository->log(
            $adminId,
            AdminAction::ACTION_CATEGORY_UPDATED,
            AdminAction::TARGET_CATEGORY,
            $categoryId,
            "Updated category: {$category->name}"
        );
        
        return $category;
    }

    /**
     * Toggle category active status.
     */
    public function toggleCategoryActive(int $categoryId, int $adminId)
    {
        $category = $this->categoryRepository->toggleActive($categoryId);
        
        $this->adminActionRepository->log(
            $adminId,
            AdminAction::ACTION_CATEGORY_UPDATED,
            AdminAction::TARGET_CATEGORY,
            $categoryId,
            "Category {$category->name} " . ($category->is_active ? 'activated' : 'deactivated')
        );
        
        return $category;
    }

    /**
     * Create service.
     */
    public function createService(array $data, int $adminId)
    {
        $service = $this->serviceRepository->create($data);
        
        $this->adminActionRepository->log(
            $adminId,
            AdminAction::ACTION_SERVICE_CREATED,
            AdminAction::TARGET_SERVICE,
            $service->id,
            "Created service: {$service->name}"
        );
        
        return $service;
    }

    /**
     * Update service.
     */
    public function updateService(int $serviceId, array $data, int $adminId)
    {
        $this->serviceRepository->update($serviceId, $data);
        $service = $this->serviceRepository->find($serviceId);
        
        $this->adminActionRepository->log(
            $adminId,
            AdminAction::ACTION_SERVICE_UPDATED,
            AdminAction::TARGET_SERVICE,
            $serviceId,
            "Updated service: {$service->name}"
        );
        
        return $service;
    }

    /**
     * Toggle service active status.
     */
    public function toggleServiceActive(int $serviceId, int $adminId)
    {
        $service = $this->serviceRepository->toggleActive($serviceId);
        
        $this->adminActionRepository->log(
            $adminId,
            AdminAction::ACTION_SERVICE_UPDATED,
            AdminAction::TARGET_SERVICE,
            $serviceId,
            "Service {$service->name} " . ($service->is_active ? 'activated' : 'deactivated')
        );
        
        return $service;
    }

    /**
     * Get recent admin actions.
     */
    public function getRecentActions(int $limit = 10)
    {
        return $this->adminActionRepository->getRecent($limit);
    }

    /**
     * Get all ratings for admin.
     */
    public function getRatings(int $perPage = 15)
    {
        return $this->ratingRepository->getAllForAdmin($perPage);
    }

    /**
     * Get all categories for admin.
     */
    public function getCategories(int $perPage = 15)
    {
        return $this->categoryRepository->getPaginatedForAdmin($perPage);
    }

    /**
     * Delete a service category (admin action).
     */
    public function deleteCategory(int $categoryId, int $adminId)
    {
        // remove category
        $this->categoryRepository->delete($categoryId);

        // log the deletion
        $this->adminActionRepository->log(
            $adminId,
            AdminAction::ACTION_CATEGORY_DELETED,
            AdminAction::TARGET_CATEGORY,
            $categoryId,
            "Deleted category ID {$categoryId}"
        );

        return true;
    }

    /**
     * Get all services for admin.
     */
    public function getServices(?int $categoryId = null, int $perPage = 15)
    {
        return $this->serviceRepository->getPaginatedForAdmin($categoryId, $perPage);
    }
}
