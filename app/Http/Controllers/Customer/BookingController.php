<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\CreateBookingRequest;
use App\Http\Requests\Customer\CancelBookingRequest;
use App\Services\BookingService;
use App\Services\CatalogService;
use App\Repositories\BookingRepository;
use App\Repositories\ProviderServiceRepository;
use App\Repositories\ProviderTimeSlotRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Exception;

class BookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService,
        protected BookingRepository $bookingRepository,
        protected CatalogService $catalogService,
        protected ProviderServiceRepository $providerServiceRepository,
        protected ProviderTimeSlotRepository $timeSlotRepository
    ) {}

    /**
     * List customer bookings.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $status = $request->get('status');
        
        $bookings = $this->bookingRepository->getPaginatedByCustomer($user->id, $status, 10);
        
        return view('customer.bookings.index', compact('bookings', 'status'));
    }

    /**
     * Show booking details.
     */
    public function show(int $id): View
    {
        $user = Auth::user();
        $booking = $this->bookingService->getCustomerBooking($id, $user->id);
        
        if (!$booking) {
            abort(404);
        }
        
        return view('customer.bookings.show', compact('booking'));
    }

    /**
     * Show booking creation form.
     *
     * Can return a view or a redirect response when a provider/service is not provided.
     */
    public function create(Request $request, $providerService = null): View|RedirectResponse
    {
        // providerService route parameter may be an id or a bound ProviderService model
        if ($providerService instanceof \App\Models\ProviderService) {
            $providerServiceId = $providerService->id;
        } else {
            $providerServiceId = $providerService ?? $request->get('provider_service_id');
        }
        
        if (!$providerServiceId) {
            return redirect()->route('services')
                ->with('error', 'Please select a service to book.');
        }
        
        $providerService = $this->providerServiceRepository->getWithDetails($providerServiceId);
        
        if (!$providerService || !$providerService->is_active) {
            abort(404);
        }
        
        $availableSlots = $this->timeSlotRepository->getAvailableFutureByProvider(
            $providerService->provider_profile_id
        );
        
        return view('customer.bookings.create', compact('providerService', 'availableSlots'));
    }

    /**
     * Store a new booking.
     */
    public function store(CreateBookingRequest $request): RedirectResponse
    {
        $user = Auth::user();
        
        try {
            $booking = $this->bookingService->createBooking([
                'customer_id' => $user->id,
                'provider_service_id' => $request->provider_service_id,
                'time_slot_id' => $request->time_slot_id,
                'customer_note' => $request->customer_note,
            ]);
            
            return redirect()->route('customer.bookings.show', $booking->id)
                ->with('success', 'Your booking has been submitted successfully. Please wait for the provider to confirm.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Cancel a booking.
     */
    public function cancel(CancelBookingRequest $request, int $id): RedirectResponse
    {
        $user = Auth::user();
        $booking = $this->bookingService->getCustomerBooking($id, $user->id);
        
        if (!$booking) {
            abort(404);
        }
        
        try {
            $this->bookingService->cancelBooking($id, $request->cancel_reason);
            
            return redirect()->route('customer.bookings.index')
                ->with('success', 'Your booking has been cancelled successfully.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
