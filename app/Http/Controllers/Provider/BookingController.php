<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Http\Requests\Provider\RejectBookingRequest;
use App\Services\BookingService;
use App\Repositories\BookingRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Exception;

class BookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService,
        protected BookingRepository $bookingRepository
    ) {}

    /**
     * List provider's bookings.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $providerProfile = $user->providerProfile;
        
        $status = $request->get('status');
        $bookings = $this->bookingRepository->getPaginatedByProvider($providerProfile->id, $status);
        
        return view('provider.bookings.index', compact('bookings', 'status'));
    }

    /**
     * Show booking details.
     */
    public function show(int $id): View
    {
        $user = Auth::user();
        $providerProfile = $user->providerProfile;
        
        $booking = $this->bookingService->getProviderBooking($id, $providerProfile->id);
        
        if (!$booking) {
            abort(404);
        }
        
        return view('provider.bookings.show', compact('booking'));
    }

    /**
     * Confirm a booking.
     */
    public function confirm(Request $request, int $id): RedirectResponse
    {
        $user = Auth::user();
        $providerProfile = $user->providerProfile;
        
        $booking = $this->bookingService->getProviderBooking($id, $providerProfile->id);
        
        if (!$booking) {
            abort(404);
        }
        
        try {
            $this->bookingService->confirmBooking($id, $request->provider_note);
            
            return redirect()->route('provider.bookings.show', $id)
                ->with('success', 'Booking confirmed successfully.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Accept a booking (compatibility alias for confirm).
     */
    public function accept(Request $request, int $id): RedirectResponse
    {
        return $this->confirm($request, $id);
    }

    /**
     * Reject a booking.
     */
    public function reject(RejectBookingRequest $request, int $id): RedirectResponse
    {
        $user = Auth::user();
        $providerProfile = $user->providerProfile;
        
        $booking = $this->bookingService->getProviderBooking($id, $providerProfile->id);
        
        if (!$booking) {
            abort(404);
        }
        
        try {
            $this->bookingService->rejectBooking($id, $request->reject_reason);
            
            return redirect()->route('provider.bookings.index')
                ->with('success', 'Booking rejected.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Mark booking as completed.
     */
    public function complete(int $id): RedirectResponse
    {
        $user = Auth::user();
        $providerProfile = $user->providerProfile;
        
        $booking = $this->bookingService->getProviderBooking($id, $providerProfile->id);
        
        if (!$booking) {
            abort(404);
        }
        
        try {
            $this->bookingService->completeBooking($id);
            
            return redirect()->route('provider.bookings.show', $id)
                ->with('success', 'Booking marked as completed.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
