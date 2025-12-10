<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Http\Requests\Provider\UpdateProfileRequest;
use App\Http\Requests\Provider\UpdateLocationsRequest;
use App\Services\ProviderManagementService;
use App\Services\CatalogService;
use App\Repositories\ProviderProfileRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        protected ProviderManagementService $providerService,
        protected ProviderProfileRepository $providerProfileRepository,
        protected CatalogService $catalogService
    ) {}

    /**
     * Show profile edit form.
     */
    public function edit(): View
    {
        $user = Auth::user();
        $providerProfile = $user->providerProfile->load('locations');
        $allLocations = $this->catalogService->getLocationsGroupedByCity();
        
        return view('provider.profile.edit', compact('providerProfile', 'allLocations'));
    }

    /**
     * Update profile.
     */
    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $providerProfile = $user->providerProfile;
        
        $this->providerService->updateProfile($providerProfile->id, $request->validated());
        
        return redirect()->route('provider.profile.edit')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Update covered locations.
     */
    public function updateLocations(UpdateLocationsRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $providerProfile = $user->providerProfile;
        
        $this->providerService->updateLocations($providerProfile->id, $request->location_ids ?? []);
        
        return redirect()->route('provider.profile.edit')
            ->with('success', 'Covered locations updated successfully.');
    }
}
