<?php

namespace App\Http\Controllers;

use App\Services\CatalogService;
use App\Repositories\RatingRepository;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(
        protected CatalogService $catalogService,
        protected RatingRepository $ratingRepository
    ) {}

    /**
     * Show the home page.
     */
    public function index(): View
    {
        $categories = $this->catalogService->getCategories();
        $featuredProviders = $this->catalogService->getFeaturedProviders(6);
        
        return view('public.home', compact('categories', 'featuredProviders'));
    }

    /**
     * Show services page with filters.
     */
    public function services(Request $request): View
    {
        $categories = $this->catalogService->getCategories();
        // The services page needs a flat list of locations for the location select
        // (not grouped by city) so users can filter by a specific location id.
        $locations = $this->catalogService->getLocations();
        
        $filters = $request->only(['category_id', 'service_id', 'city', 'area', 'min_price', 'max_price', 'min_rating', 'sort_by', 'sort_dir', 'search']);

        // Provide lists for filters (services + cities). Areas can be populated by JS or server-side if a city was selected.
        $services = $this->catalogService->getServices();
        $cities = $this->catalogService->getCities();
        $areas = [];
        if (!empty($filters['city'])) {
            $areas = $this->catalogService->getAreasByCity($filters['city']);
        }

        $providers = $this->catalogService->getProviders($filters);

        return view('public.services', compact('categories', 'locations', 'providers', 'filters', 'services', 'cities', 'areas'));
    }

    /**
     * Show provider profile.
     */
    public function showProvider(int $id): View
    {
        $provider = $this->catalogService->getProviderDetails($id);
        
        if (!$provider || !$provider->user->is_active) {
            abort(404);
        }
        
        // Provide paginated visible ratings for the provider profile
        $ratings = $this->ratingRepository->getPaginatedVisibleByProvider($id, 6);

        return view('public.provider', compact('provider', 'ratings'));
    }

    /**
     * Get areas by city (AJAX).
     */
    public function getAreasByCity(Request $request)
    {
        $city = $request->get('city');
        $areas = $this->catalogService->getAreasByCity($city);
        
        return response()->json($areas);
    }
}
