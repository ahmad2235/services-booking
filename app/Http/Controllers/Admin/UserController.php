<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(
        protected AdminService $adminService
    ) {}

    /**
     * List all users.
     */
    public function index(Request $request): View
    {
        $role = $request->get('role');
        $isActive = $request->has('is_active') ? $request->boolean('is_active') : null;
        
        $users = $this->adminService->getUsers($role, $isActive);
        
        return view('admin.users.index', compact('users', 'role', 'isActive'));
    }

    /**
     * Toggle user active status.
     */
    public function toggleActive(int $id): RedirectResponse
    {
        $admin = Auth::user();
        
        // Prevent self-deactivation
        if ($admin->id === $id) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }
        
        $user = $this->adminService->toggleUserActive($id, $admin->id);
        
        $message = $user->is_active 
            ? "User {$user->name} has been activated." 
            : "User {$user->name} has been deactivated.";
        
        return back()->with('success', $message);
    }
}
