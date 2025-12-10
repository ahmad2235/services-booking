@extends('layouts.customer')

@section('title', 'Book Service - Customer')

@section('customer-content')
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('customer.bookings.index') }}">Bookings</a></li>
        <li class="breadcrumb-item active">New Booking</li>
    </ol>
</nav>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Book Service</h4>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('customer.bookings.store') }}">
                    @csrf
                    <input type="hidden" name="provider_service_id" value="{{ $providerService->id }}">

                    <div class="mb-4">
                        <label for="time_slot_id" class="form-label">Select Available Time Slot</label>
                        <select class="form-select" id="time_slot_id" name="time_slot_id" required>
                            <option value="">Choose a time slot...</option>
                            @forelse($availableSlots as $slot)
                                <option value="{{ $slot->id }}" {{ old('time_slot_id') == $slot->id ? 'selected' : '' }}>
                                    {{ $slot->start_datetime->format('M d, Y') }} - 
                                    {{ $slot->start_datetime->format('g:i A') }} to {{ $slot->end_datetime->format('g:i A') }}
                                </option>
                            @empty
                                <option value="" disabled>No available time slots</option>
                            @endforelse
                        </select>
                        <div class="form-text">Select your preferred date and time for the service.</div>
                    </div>

                    <div class="mb-4">
                        <label for="address" class="form-label">Service Address</label>
                        <textarea class="form-control" id="address" name="address" rows="2" required>{{ old('address', Auth::user()->customerProfile->address ?? '') }}</textarea>
                        <div class="form-text">Enter the address where the service should be performed.</div>
                    </div>

                    <div class="mb-4">
                        <label for="notes" class="form-label">Additional Notes (Optional)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" 
                                  placeholder="Any special instructions or requirements...">{{ old('notes') }}</textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" {{ $availableSlots->isEmpty() ? 'disabled' : '' }}>
                            <i class="bi bi-calendar-check"></i> Confirm Booking
                        </button>
                        <a href="{{ route('providers.show', $providerService->provider->user_id) }}" class="btn btn-outline-secondary">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Service Summary -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Service Summary</h5>
            </div>
            <div class="card-body">
                <h5>{{ $providerService->service->name }}</h5>
                <p class="text-muted mb-2">{{ $providerService->service->category->name }}</p>
                
                @if($providerService->custom_description)
                    <p class="small">{{ $providerService->custom_description }}</p>
                @endif

                <hr>
                
                <div class="d-flex justify-content-between mb-2">
                    <span>Duration:</span>
                    <span>{{ $providerService->service->duration_minutes }} min</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="fw-bold">Total Price:</span>
                    <span class="fw-bold text-primary">${{ number_format($providerService->price, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Provider Info -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Provider</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                         style="width: 50px; height: 50px;">
                        <span class="text-white fs-5">{{ substr($providerService->provider->user->name, 0, 1) }}</span>
                    </div>
                    <div>
                        <h6 class="mb-0">{{ $providerService->provider->company_name }}</h6>
                        <small class="text-muted">{{ $providerService->provider->user->name }}</small>
                    </div>
                </div>
                <div class="rating-stars mb-2">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= round($providerService->provider->avg_rating))
                            <i class="bi bi-star-fill"></i>
                        @else
                            <i class="bi bi-star"></i>
                        @endif
                    @endfor
                    <span class="ms-1">({{ number_format($providerService->provider->avg_rating, 1) }})</span>
                </div>
                <a href="{{ route('providers.show', $providerService->provider->user_id) }}" class="btn btn-sm btn-outline-primary w-100">
                    View Provider Profile
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
