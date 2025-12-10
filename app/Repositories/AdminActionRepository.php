<?php

namespace App\Repositories;

use App\Models\AdminAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AdminActionRepository extends BaseRepository
{
    public function __construct(AdminAction $model)
    {
        $this->model = $model;
    }

    /**
     * Get actions by admin.
     */
    public function getByAdmin(int $adminId): Collection
    {
        return $this->model->where('admin_id', $adminId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get paginated actions.
     */
    public function getPaginated(int $perPage = 20): LengthAwarePaginator
    {
        return $this->model->with('admin')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Log an admin action.
     */
    public function log(int $adminId, string $actionType, string $targetType, ?int $targetId = null, ?string $details = null): AdminAction
    {
        return $this->model->create([
            'admin_id' => $adminId,
            'action_type' => $actionType,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'details' => $details,
            'created_at' => now(),
        ]);
    }

    /**
     * Get actions by target.
     */
    public function getByTarget(string $targetType, int $targetId): Collection
    {
        return $this->model->where('target_type', $targetType)
            ->where('target_id', $targetId)
            ->with('admin')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get recent actions.
     */
    public function getRecent(int $limit = 10): Collection
    {
        return $this->model->with('admin')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
