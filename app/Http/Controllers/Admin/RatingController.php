<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RatingController extends Controller
{
    public function __construct(
        protected AdminService $adminService
    ) {}

    /**
     * List all ratings.
     */
    public function index(): View
    {
        $ratings = $this->adminService->getRatings();
        
        return view('admin.ratings.index', compact('ratings'));
    }

    /**
     * Toggle rating visibility.
     */
    public function toggleVisibility(int $id): RedirectResponse
    {
        $admin = Auth::user();
        
        $rating = $this->adminService->toggleRatingVisibility($id, $admin->id);
        
        $message = $rating->is_visible 
            ? "Rating is now visible." 
            : "Rating has been hidden.";
        
        return back()->with('success', $message);
    }
}
