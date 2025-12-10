<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Services\AdminService;
use App\Repositories\ServiceCategoryRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ServiceCategoryController extends Controller
{
    public function __construct(
        protected AdminService $adminService,
        protected ServiceCategoryRepository $categoryRepository
    ) {}

    /**
     * List all categories.
     */
    public function index(): View
    {
        $categories = $this->adminService->getCategories();

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show form to create a new category.
     */
    public function create(): View
    {
        return view('admin.categories.create');
    }

    /**
     * Store a new category.
     */
    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $admin = Auth::user();
        
        $this->adminService->createCategory($request->validated(), $admin->id);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Show form to edit a category.
     */
    public function edit(int $id): View
    {
        $category = $this->categoryRepository->findOrFail($id);
        
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update a category.
     */
    public function update(UpdateCategoryRequest $request, int $id): RedirectResponse
    {
        $admin = Auth::user();
        
        $this->adminService->updateCategory($id, $request->validated(), $admin->id);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Toggle category active status.
     */
    public function toggleActive(int $id): RedirectResponse
    {
        $admin = Auth::user();
        
        $category = $this->adminService->toggleCategoryActive($id, $admin->id);
        
        $message = $category->is_active 
            ? "Category '{$category->name}' has been activated." 
            : "Category '{$category->name}' has been deactivated.";
        
        return back()->with('success', $message);
    }

    /**
     * Destroy a category.
     */
    public function destroy(int $id): RedirectResponse
    {
        $admin = Auth::user();

        // Use AdminService so the action is logged
        $this->adminService->deleteCategory($id, $admin->id);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
