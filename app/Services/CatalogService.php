<?php

namespace App\Services;

use App\Repositories\ServiceCategoryRepository;
use App\Repositories\ServiceRepository;
use App\Repositories\ProviderProfileRepository;
use App\Repositories\LocationRepository;

class CatalogService
{
    public function __construct(
        protected ServiceCategoryRepository $categoryRepository,
        protected ServiceRepository $serviceRepository,
        protected ProviderProfileRepository $providerRepository,
        protected LocationRepository $locationRepository
    ) {}

    /**
     * Get all active categories with service counts.
     */
    public function getCategories()
    {
        return $this->categoryRepository->getWithServiceCounts();
    }

    /**
     * Get category with its services.
     */
    public function getCategoryWithServices(int $categoryId)
    {
        return $this->categoryRepository->getWithServices($categoryId);
    }

    /**
     * Get all active services.
     */
    public function getServices()
    {
        return $this->serviceRepository->getActiveWithCategory();
    }

    /**
     * Get services by category.
     */
    public function getServicesByCategory(int $categoryId)
    {
        return $this->serviceRepository->getByCategory($categoryId);
    }

    /**
     * Search services.
     */
    public function searchServices(string $term)
    {
        return $this->serviceRepository->search($term);
    }

    /**
     * Get providers with filters.
     */
    public function getProviders(array $filters = [], int $perPage = 12)
    {
        return $this->providerRepository->getWithFilters($filters, $perPage);
    }

    /**
     * Get featured providers.
     */
    public function getFeaturedProviders(int $limit = 6)
    {
        return $this->providerRepository->getFeatured($limit);
    }

    /**
     * Get provider with details.
     */
    public function getProviderDetails(int $providerProfileId)
    {
        return $this->providerRepository->getWithDetails($providerProfileId);
    }

    /**
     * Get all active locations.
     */
    public function getLocations()
    {
        return $this->locationRepository->getActive();
    }

    /**
     * Get locations grouped by city.
     */
    public function getLocationsGroupedByCity()
    {
        return $this->locationRepository->getGroupedByCity();
    }

    /**
     * Get unique cities.
     */
    public function getCities()
    {
        return $this->locationRepository->getCities();
    }

    /**
     * Get areas by city.
     */
    public function getAreasByCity(string $city)
    {
        return $this->locationRepository->getAreasByCity($city);
    }
}
