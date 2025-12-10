<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected AdminService $adminService
    ) {}

    /**
     * Show admin dashboard.
     */
    public function index(): View
    {
        $stats = $this->adminService->getDashboardStats();
        $recentActions = $this->adminService->getRecentActions(10);
        
        return view('admin.dashboard', compact('stats', 'recentActions'));
    }
}
