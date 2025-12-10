<?php

namespace App\Repositories;

use App\Models\CustomerProfile;

class CustomerProfileRepository extends BaseRepository
{
    public function __construct(CustomerProfile $model)
    {
        $this->model = $model;
    }

    /**
     * Find profile by user ID.
     */
    public function findByUserId(int $userId): ?CustomerProfile
    {
        return $this->model->where('user_id', $userId)->first();
    }

    /**
     * Update or create profile for user.
     */
    public function updateOrCreateForUser(int $userId, array $data): CustomerProfile
    {
        return $this->model->updateOrCreate(
            ['user_id' => $userId],
            $data
        );
    }

    /**
     * Get profiles by city.
     */
    public function getByCity(string $city)
    {
        return $this->model->where('city', $city)->get();
    }

    /**
     * Get profiles by area.
     */
    public function getByArea(string $city, string $area)
    {
        return $this->model->where('city', $city)->where('area', $area)->get();
    }
}
