<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class UserRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * Find user by email.
     */
    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * Get users by role with optional filters.
     */
    public function getByRole(?string $role = null, ?bool $isActive = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->query();

        if ($role) {
            $query->where('role', $role);
        }

        if ($isActive !== null) {
            $query->where('is_active', $isActive);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Get all customers.
     */
    public function getCustomers(int $perPage = 15): LengthAwarePaginator
    {
        return $this->getByRole('customer', null, $perPage);
    }

    /**
     * Get all providers.
     */
    public function getProviders(int $perPage = 15): LengthAwarePaginator
    {
        return $this->getByRole('provider', null, $perPage);
    }

    /**
     * Get all admins.
     */
    public function getAdmins(): Collection
    {
        return $this->model->where('role', 'admin')->get();
    }

    /**
     * Toggle user active status.
     */
    public function toggleActive(int $id): User
    {
        $user = $this->findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();
        return $user;
    }

    /**
     * Get user statistics.
     */
    public function getStatistics(): array
    {
        return [
            'total' => $this->model->count(),
            'customers' => $this->model->where('role', 'customer')->count(),
            'providers' => $this->model->where('role', 'provider')->count(),
            'admins' => $this->model->where('role', 'admin')->count(),
            'active' => $this->model->where('is_active', true)->count(),
            'inactive' => $this->model->where('is_active', false)->count(),
        ];
    }

    /**
     * Create customer with profile.
     */
    public function createCustomer(array $userData, array $profileData): User
    {
        $userData['role'] = 'customer';
        $user = $this->create($userData);
        $user->customerProfile()->create($profileData);
        return $user->load('customerProfile');
    }

    /**
     * Create provider with profile.
     */
    public function createProvider(array $userData, array $profileData): User
    {
        $userData['role'] = 'provider';
        $user = $this->create($userData);
        $user->providerProfile()->create($profileData);
        return $user->load('providerProfile');
    }
}
