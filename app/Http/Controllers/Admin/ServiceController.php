<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreServiceRequest;
use App\Http\Requests\Admin\UpdateServiceRequest;
use App\Services\AdminService;
use App\Repositories\ServiceRepository;
use App\Repositories\ServiceCategoryRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function __construct(
        protected AdminService $adminService,
        protected ServiceRepository $serviceRepository,
        protected ServiceCategoryRepository $categoryRepository
    ) {}

    /**
     * List all services.
     */
    public function index(Request $request): View
    {
        $categoryId = $request->get('category_id');
        $services = $this->adminService->getServices($categoryId);
        $categories = $this->categoryRepository->all();
        
        return view('admin.services.index', compact('services', 'categories', 'categoryId'));
    }

    /**
     * Show form to create a new service.
     */
    public function create(): View
    {
        $categories = $this->categoryRepository->getActive();
        
        return view('admin.services.create', compact('categories'));
    }

    /**
     * Store a new service.
     */
    public function store(StoreServiceRequest $request): RedirectResponse
    {
        $admin = Auth::user();
        
        $this->adminService->createService($request->validated(), $admin->id);
        
        return redirect()->route('admin.services.index')
            ->with('success', 'Service created successfully.');
    }

    /**
     * Show form to edit a service.
     */
    public function edit(int $id): View
    {
        $service = $this->serviceRepository->findOrFail($id);
        $categories = $this->categoryRepository->getActive();
        
        return view('admin.services.edit', compact('service', 'categories'));
    }

    /**
     * Update a service.
     */
    public function update(UpdateServiceRequest $request, int $id): RedirectResponse
    {
        $admin = Auth::user();
        
        $this->adminService->updateService($id, $request->validated(), $admin->id);
        
        return redirect()->route('admin.services.index')
            ->with('success', 'Service updated successfully.');
    }

    /**
     * Toggle service active status.
     */
    public function toggleActive(int $id): RedirectResponse
    {
        $admin = Auth::user();
        
        $service = $this->adminService->toggleServiceActive($id, $admin->id);
        
        $message = $service->is_active 
            ? "Service '{$service->name}' has been activated." 
            : "Service '{$service->name}' has been deactivated.";
        
        return back()->with('success', $message);
    }
}
