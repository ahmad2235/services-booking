<?php

namespace App\Repositories;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class NotificationRepository extends BaseRepository
{
    public function __construct(Notification $model)
    {
        $this->model = $model;
    }

    /**
     * Get notifications for a user.
     */
    public function getByUser(int $userId): Collection
    {
        return $this->model->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get paginated notifications for a user.
     */
    public function getPaginatedByUser(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get unread notifications for a user.
     */
    public function getUnreadByUser(int $userId): Collection
    {
        return $this->model->where('user_id', $userId)
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get unread count for a user.
     */
    public function getUnreadCount(int $userId): int
    {
        return $this->model->where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Create a notification.
     */
    public function createNotification(int $userId, string $type, array $data = []): Notification
    {
        return $this->model->create([
            'user_id' => $userId,
            'type' => $type,
            'data' => $data,
            'is_read' => false,
            'created_at' => now(),
        ]);
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(int $id): Notification
    {
        $notification = $this->findOrFail($id);
        $notification->markAsRead();
        return $notification;
    }

    /**
     * Mark all notifications as read for a user.
     */
    public function markAllAsRead(int $userId): int
    {
        return $this->model->where('user_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    /**
     * Delete old notifications.
     */
    public function deleteOldNotifications(int $daysOld = 30): int
    {
        return $this->model->where('created_at', '<', now()->subDays($daysOld))
            ->where('is_read', true)
            ->delete();
    }
}
