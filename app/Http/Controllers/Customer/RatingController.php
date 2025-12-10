<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\CreateRatingRequest;
use App\Services\RatingService;
use App\Services\BookingService;
use App\Repositories\BookingRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Exception;

class RatingController extends Controller
{
    public function __construct(
        protected RatingService $ratingService,
        protected BookingService $bookingService,
        protected BookingRepository $bookingRepository
    ) {}

    /**
     * Show rating form for a booking.
     *
     * @return View|RedirectResponse
     */
    public function create(int $bookingId): View|RedirectResponse
    {
        $user = Auth::user();
        $booking = $this->bookingService->getCustomerBooking($bookingId, $user->id);
        
        if (!$booking) {
            abort(404);
        }
        
        if (!$booking->canBeRated()) {
            return redirect()->route('customer.bookings.show', $bookingId)
                ->with('error', 'This booking cannot be rated.');
        }
        
        return view('customer.ratings.create', compact('booking'));
    }

    /**
     * Store a new rating.
     */
    public function store(CreateRatingRequest $request, int $bookingId): RedirectResponse
    {
        $user = Auth::user();
        
        try {
            $this->ratingService->createRating(
                $bookingId,
                $user->id,
                $request->rating_value,
                $request->comment
            );
            
            return redirect()->route('customer.bookings.show', $bookingId)
                ->with('success', 'Thank you for your rating!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
