<?php

namespace App\Repositories;

use App\Models\Location;
use Illuminate\Support\Collection;

class LocationRepository extends BaseRepository
{
    public function __construct(Location $model)
    {
        $this->model = $model;
    }

    /**
     * Get all active locations.
     */
    public function getActive(): Collection
    {
        return $this->model->where('is_active', true)
            ->orderBy('city')
            ->orderBy('area')
            ->get();
    }

    /**
     * Get locations grouped by city.
     */
    public function getGroupedByCity(): Collection
    {
        return $this->model->where('is_active', true)
            ->orderBy('city')
            ->orderBy('area')
            ->get()
            ->groupBy('city');
    }

    /**
     * Get unique cities.
     */
    public function getCities(): Collection
    {
        return $this->model->where('is_active', true)
            ->distinct()
            ->orderBy('city')
            ->pluck('city');
    }

    /**
     * Get areas by city.
     */
    public function getAreasByCity(string $city): Collection
    {
        return $this->model->where('city', $city)
            ->where('is_active', true)
            ->orderBy('area')
            ->get();
    }

    /**
     * Find by city and area.
     */
    public function findByCityAndArea(string $city, string $area): ?Location
    {
        return $this->model->where('city', $city)
            ->where('area', $area)
            ->first();
    }
}
