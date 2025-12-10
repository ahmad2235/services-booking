<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Http\Requests\Provider\StoreServiceRequest;
use App\Http\Requests\Provider\UpdateServiceRequest;
use App\Services\ProviderManagementService;
use App\Repositories\ProviderServiceRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Exception;

class ServiceController extends Controller
{
    public function __construct(
        protected ProviderManagementService $providerService,
        protected ProviderServiceRepository $providerServiceRepository
    ) {}

    /**
     * List provider's services.
     */
    public function index(): View
    {
        $user = Auth::user();
        $providerProfile = $user->providerProfile;
        
        $services = $this->providerServiceRepository->getPaginatedByProvider($providerProfile->id);
        
        return view('provider.services.index', compact('services'));
    }

    /**
     * Show form to add a new service.
     */
    public function create(): View
    {
        $user = Auth::user();
        $providerProfile = $user->providerProfile;
        
        $availableServices = $this->providerService->getAvailableServices($providerProfile->id);
        
        return view('provider.services.create', compact('availableServices'));
    }

    /**
     * Store a new provider service.
     */
    public function store(StoreServiceRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $providerProfile = $user->providerProfile;
        
        try {
            $this->providerService->addService($providerProfile->id, $request->validated());
            
            return redirect()->route('provider.services.index')
                ->with('success', 'Service added successfully.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Show form to edit a service.
     */
    public function edit(int $id): View
    {
        $user = Auth::user();
        $providerProfile = $user->providerProfile;
        
        $providerService = $this->providerServiceRepository->find($id);
        
        if (!$providerService || $providerService->provider_profile_id !== $providerProfile->id) {
            abort(404);
        }
        
        return view('provider.services.edit', compact('providerService'));
    }

    /**
     * Update a provider service.
     */
    public function update(UpdateServiceRequest $request, int $id): RedirectResponse
    {
        $user = Auth::user();
        $providerProfile = $user->providerProfile;
        
        $providerService = $this->providerServiceRepository->find($id);
        
        if (!$providerService || $providerService->provider_profile_id !== $providerProfile->id) {
            abort(404);
        }
        
        $this->providerService->updateService($id, $request->validated());
        
        return redirect()->route('provider.services.index')
            ->with('success', 'Service updated successfully.');
    }

    /**
     * Toggle service active status.
     */
    public function toggleActive(int $id): RedirectResponse
    {
        $user = Auth::user();
        $providerProfile = $user->providerProfile;
        
        $providerService = $this->providerServiceRepository->find($id);
        
        if (!$providerService || $providerService->provider_profile_id !== $providerProfile->id) {
            abort(404);
        }
        
        $this->providerService->toggleServiceActive($id);
        
        return redirect()->route('provider.services.index')
            ->with('success', 'Service status updated.');
    }

    /**
     * Delete a provider service.
     */
    public function destroy(int $id): RedirectResponse
    {
        $user = Auth::user();
        $providerProfile = $user->providerProfile;
        
        $providerService = $this->providerServiceRepository->find($id);
        
        if (!$providerService || $providerService->provider_profile_id !== $providerProfile->id) {
            abort(404);
        }
        
        $this->providerService->deleteService($id);
        
        return redirect()->route('provider.services.index')
            ->with('success', 'Service removed successfully.');
    }
}
