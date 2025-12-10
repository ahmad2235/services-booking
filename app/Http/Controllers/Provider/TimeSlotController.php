<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Http\Requests\Provider\StoreTimeSlotRequest;
use App\Http\Requests\Provider\UpdateTimeSlotRequest;
use App\Services\ProviderManagementService;
use App\Repositories\ProviderTimeSlotRepository;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Exception;

class TimeSlotController extends Controller
{
    public function __construct(
        protected ProviderManagementService $providerService,
        protected ProviderTimeSlotRepository $timeSlotRepository
    ) {}

    /**
     * List provider's time slots.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $providerProfile = $user->providerProfile;
        
        $status = $request->get('status');
        $timeSlots = $this->timeSlotRepository->getPaginatedByProvider($providerProfile->id, $status);
        
        // Get calendar data for current month
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        $calendarSlots = $this->timeSlotRepository->getGroupedByDate($providerProfile->id, $startDate, $endDate);
        
        return view('provider.time_slots.index', compact('timeSlots', 'calendarSlots', 'status'));
    }

    /**
     * Show form to create a new time slot.
     */
    public function create(): View
    {
        return view('provider.time_slots.create');
    }

    /**
     * Store a new time slot.
     */
    public function store(StoreTimeSlotRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $providerProfile = $user->providerProfile;
        
        try {
            $this->providerService->createTimeSlot($providerProfile->id, $request->validated());
            
            return redirect()->route('provider.time-slots.index')
                ->with('success', 'Time slot created successfully.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Show form to edit a time slot.
     */
    public function edit(int $id): View
    {
        $user = Auth::user();
        $providerProfile = $user->providerProfile;
        
        $timeSlot = $this->timeSlotRepository->find($id);
        
        if (!$timeSlot || $timeSlot->provider_profile_id !== $providerProfile->id) {
            abort(404);
        }
        
        return view('provider.time_slots.edit', compact('timeSlot'));
    }

    /**
     * Update a time slot.
     */
    public function update(UpdateTimeSlotRequest $request, int $id): RedirectResponse
    {
        $user = Auth::user();
        $providerProfile = $user->providerProfile;
        
        $timeSlot = $this->timeSlotRepository->find($id);
        
        if (!$timeSlot || $timeSlot->provider_profile_id !== $providerProfile->id) {
            abort(404);
        }
        
        try {
            $this->providerService->updateTimeSlot($id, $request->validated());
            
            return redirect()->route('provider.time-slots.index')
                ->with('success', 'Time slot updated successfully.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Delete a time slot.
     */
    public function destroy(int $id): RedirectResponse
    {
        $user = Auth::user();
        $providerProfile = $user->providerProfile;
        
        $timeSlot = $this->timeSlotRepository->find($id);
        
        if (!$timeSlot || $timeSlot->provider_profile_id !== $providerProfile->id) {
            abort(404);
        }
        
        try {
            $this->providerService->deleteTimeSlot($id);
            
            return redirect()->route('provider.time-slots.index')
                ->with('success', 'Time slot deleted successfully.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Block a time slot.
     */
    public function block(int $id): RedirectResponse
    {
        $user = Auth::user();
        $providerProfile = $user->providerProfile;
        
        $timeSlot = $this->timeSlotRepository->find($id);
        
        if (!$timeSlot || $timeSlot->provider_profile_id !== $providerProfile->id) {
            abort(404);
        }
        
        try {
            $this->providerService->blockTimeSlot($id);
            
            return redirect()->route('provider.time-slots.index')
                ->with('success', 'Time slot blocked successfully.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Unblock a time slot.
     */
    public function unblock(int $id): RedirectResponse
    {
        $user = Auth::user();
        $providerProfile = $user->providerProfile;
        
        $timeSlot = $this->timeSlotRepository->find($id);
        
        if (!$timeSlot || $timeSlot->provider_profile_id !== $providerProfile->id) {
            abort(404);
        }
        
        try {
            $this->providerService->unblockTimeSlot($id);
            
            return redirect()->route('provider.time-slots.index')
                ->with('success', 'Time slot unblocked successfully.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
