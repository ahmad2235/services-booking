<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Services\ProviderManagementService;
use App\Services\NotificationService;
use App\Repositories\BookingRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected ProviderManagementService $providerService,
        protected BookingRepository $bookingRepository,
        protected NotificationService $notificationService
    ) {}

    /**
     * Show provider dashboard.
     */
    public function index(): View
    {
        $user = Auth::user();
        $providerProfile = $user->providerProfile;
        
        $providerStats = $this->providerService->getDashboardStats($providerProfile->id);
        $bookingStats = $this->bookingRepository->getProviderStatistics($providerProfile->id);

        // Merge booking counts with provider stats for the view
        $stats = [
            'total_bookings' => $bookingStats['total'] ?? 0,
            'pending_bookings' => $bookingStats['pending'] ?? 0,
            'confirmed_bookings' => $bookingStats['confirmed'] ?? 0,
            'completed_bookings' => $bookingStats['completed'] ?? 0,
            'avg_rating' => $providerStats['avg_rating'] ?? 0.0,
            'total_services' => $providerStats['active_services'] ?? 0,
            'total_earnings' => $bookingStats['total_revenue'] ?? 0.0,
        ];

        $pendingBookings = $this->bookingRepository->getPendingByProvider($providerProfile->id);
        $recentBookings = $this->bookingRepository->getPaginatedByProvider($providerProfile->id, null, 5);
        $upcomingBookings = $this->bookingRepository->getPaginatedByProvider($providerProfile->id, 'confirmed', 5);
        $unreadNotifications = $this->notificationService->getUnreadCount($user->id);
        
        return view('provider.dashboard', compact(
            'providerProfile',
            'stats',
            'bookingStats',
            'pendingBookings',
            'recentBookings',
            'upcomingBookings',
            'unreadNotifications'
        ));
    }
}
