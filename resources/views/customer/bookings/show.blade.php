@extends('layouts.customer')

@section('title', 'Booking Details - Customer')

@section('customer-content')
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('customer.bookings.index') }}">Bookings</a></li>
        <li class="breadcrumb-item active">Booking #{{ $booking->id }}</li>
    </ol>
</nav>

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Booking Details</h4>
                @switch($booking->status)
                    @case('pending')
                        <span class="badge bg-warning fs-6">Pending</span>
                        @break
                    @case('confirmed')
                        <span class="badge bg-primary fs-6">Confirmed</span>
                        @break
                    @case('completed')
                        <span class="badge bg-success fs-6">Completed</span>
                        @break
                    @case('cancelled')
                        <span class="badge bg-secondary fs-6">Cancelled</span>
                        @break
                    @case('rejected')
                        <span class="badge bg-danger fs-6">Rejected</span>
                        @break
                @endswitch
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5>Service</h5>
                        <p class="mb-1"><strong>{{ $booking->providerService->service->name }}</strong></p>
                        <p class="text-muted mb-0">{{ $booking->providerService->service->category->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <h5>Provider</h5>
                        <p class="mb-1"><strong>{{ $booking->providerService->provider->company_name }}</strong></p>
                        <p class="text-muted mb-0">{{ $booking->providerService->provider->user->name }}</p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5>Scheduled Date & Time</h5>
                        <p class="mb-1"><i class="bi bi-calendar"></i> {{ $booking->scheduled_at->format('l, F d, Y') }}</p>
                        <p class="mb-0"><i class="bi bi-clock"></i> {{ $booking->scheduled_at->format('g:i A') }}</p>
                    </div>
                    <div class="col-md-6">
                        <h5>Duration</h5>
                        <p class="mb-0">{{ $booking->providerService->service->duration_minutes }} minutes</p>
                    </div>
                </div>

                <div class="mb-4">
                    <h5>Service Address</h5>
                    <p class="mb-0"><i class="bi bi-geo-alt"></i> {{ $booking->address }}</p>
                </div>

                @if($booking->notes)
                    <div class="mb-4">
                        <h5>Notes</h5>
                        <p class="mb-0">{{ $booking->notes }}</p>
                    </div>
                @endif

                @if($booking->status === 'rejected' && $booking->rejection_reason)
                    <div class="alert alert-danger">
                        <h6><i class="bi bi-x-circle"></i> Rejection Reason</h6>
                        <p class="mb-0">{{ $booking->rejection_reason }}</p>
                    </div>
                @endif

                @if($booking->status === 'cancelled' && $booking->cancellation_reason)
                    <div class="alert alert-secondary">
                        <h6><i class="bi bi-info-circle"></i> Cancellation Reason</h6>
                        <p class="mb-0">{{ $booking->cancellation_reason }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Actions -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex gap-2 flex-wrap">
                    @if(in_array($booking->status, ['pending', 'confirmed']))
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">
                            <i class="bi bi-x-circle"></i> Cancel Booking
                        </button>
                    @endif

                    @if($booking->status === 'completed' && !$booking->rating)
                        <a href="{{ route('customer.ratings.create', $booking->id) }}" class="btn btn-success">
                            <i class="bi bi-star"></i> Rate This Service
                        </a>
                    @endif

                    <a href="{{ route('customer.bookings.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Bookings
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Payment Summary -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Payment Summary</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Service Price:</span>
                    <span>${{ number_format($booking->total_price, 2) }}</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="fw-bold">Total:</span>
                    <span class="fw-bold text-primary h4">${{ number_format($booking->total_price, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Rating (if completed and rated) -->
        @if($booking->rating)
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Your Rating</h5>
                </div>
                <div class="card-body">
                    <div class="rating-stars mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $booking->rating->rating_value)
                                <i class="bi bi-star-fill fs-4"></i>
                            @else
                                <i class="bi bi-star fs-4"></i>
                            @endif
                        @endfor
                    </div>
                    @if($booking->rating->comment)
                        <p class="mb-0">{{ $booking->rating->comment }}</p>
                    @endif
                    <small class="text-muted">Rated on {{ $booking->rating->created_at->format('M d, Y') }}</small>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Cancel Modal -->
@if(in_array($booking->status, ['pending', 'confirmed']))
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('customer.bookings.cancel', $booking->id) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Cancel Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to cancel this booking?</p>
                    <div class="mb-3">
                        <label for="cancellation_reason" class="form-label">Reason for cancellation (optional)</label>
                        <textarea class="form-control" id="cancellation_reason" name="cancellation_reason" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Keep Booking</button>
                    <button type="submit" class="btn btn-danger">Cancel Booking</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
