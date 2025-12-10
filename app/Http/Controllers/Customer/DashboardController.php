<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\BookingService;
use App\Services\CatalogService;
use App\Services\NotificationService;
use App\Repositories\BookingRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected BookingService $bookingService,
        protected BookingRepository $bookingRepository,
        protected CatalogService $catalogService,
        protected NotificationService $notificationService
    ) {}

    /**
     * Show customer dashboard.
     */
    public function index(): View
    {
        $user = Auth::user();
        
        $recentBookings = $this->bookingRepository->getPaginatedByCustomer($user->id, null, 5);
        $pendingBookings = $this->bookingRepository->getByCustomer($user->id, 'pending');
        $confirmedBookings = $this->bookingRepository->getByCustomer($user->id, 'confirmed');
        $unreadNotifications = $this->notificationService->getUnreadCount($user->id);
        
        // Build simple booking stats for the dashboard view
        $allBookings = $this->bookingRepository->getByCustomer($user->id);

        $stats = [
            'total_bookings' => $allBookings->count(),
            'pending_bookings' => $allBookings->where('status', 'pending')->count(),
            'completed_bookings' => $allBookings->where('status', 'completed')->count(),
        ];

        return view('customer.dashboard', compact(
            'recentBookings',
            'pendingBookings',
            'confirmedBookings',
            'unreadNotifications',
            'stats'
        ));
    }
}
