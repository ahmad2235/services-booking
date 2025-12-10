<?php

namespace App\Services;

use App\Repositories\NotificationRepository;

class NotificationService
{
    public function __construct(
        protected NotificationRepository $notificationRepository
    ) {}

    /**
     * Get unread notifications for user.
     */
    public function getUnreadNotifications(int $userId)
    {
        return $this->notificationRepository->getUnreadByUser($userId);
    }

    /**
     * Get unread count for user.
     */
    public function getUnreadCount(int $userId): int
    {
        return $this->notificationRepository->getUnreadCount($userId);
    }

    /**
     * Get all notifications for user.
     */
    public function getUserNotifications(int $userId, int $perPage = 15)
    {
        return $this->notificationRepository->getPaginatedByUser($userId, $perPage);
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(int $notificationId)
    {
        return $this->notificationRepository->markAsRead($notificationId);
    }

    /**
     * Mark all notifications as read for user.
     */
    public function markAllAsRead(int $userId): int
    {
        return $this->notificationRepository->markAllAsRead($userId);
    }

    /**
     * Create notification.
     */
    public function createNotification(int $userId, string $type, array $data = [])
    {
        return $this->notificationRepository->createNotification($userId, $type, $data);
    }
}
